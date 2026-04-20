<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Order;
use App\Models\Page;
use App\Models\Transaction;
use App\Traits\Notify;
use App\Traits\PaymentValidationCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use PaymentValidationCheck , Notify;
    public function checkout()
    {
        if (!basicControl()->ecommerce) {
            abort(403);
        }
        if (count(session('cart') ?? []) <= 0) {
            return redirect()->route('page')->with('error', 'Cart is empty');
        }
        $page = Page::where('slug', 'checkout')->first();
        $data['pageSeo'] = [
            'page_title' => $page->page_title,
            'meta_title' => $page->meta_title,
            'meta_keywords' => isset($page->meta_keywords) && $page->meta_keywords ?implode(',', $page->meta_keywords ?? []):'',
            'meta_description' => $page->meta_description,
            'og_description' => $page->og_description,
            'meta_robots' => $page->meta_robots,
            'meta_image' => getFile($page->meta_image_driver, $page->meta_image),
            'breadcrumb_image' => $page->breadcrumb_status ?
                getFile($page->breadcrumb_image_driver, $page->breadcrumb_image) : null,
        ];

        $data['areas'] = Area::get();
        return view(template() . 'pages.checkout', $data);
    }

    public function shippingCharge(Request $request)
    {
        $area_id = $request->area_id;
        $area = Area::with('shippingCharge')->where('id', $area_id)->first();
        if (!$area) {
            return response()->json(['errors' => ['charge' => ['Area not found']]], 400);
        }
        $totalDeliveryCharge = deliveryCharge($area);
        return response()->json(['charge' => $totalDeliveryCharge]);

    }

    public function order(Request $request)
    {
        if (!basicControl()->ecommerce) {
            abort(403);
        }
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            'city' => 'required',
            'zip' => 'nullable|numeric',
            'additional_information' => 'nullable|max:1000',
            'area' => 'required | exists:areas,id',
        ]);


        $area = Area::with('shippingCharge')->findOrFail($request->area);
        $vat = vat(cartTotal(session('cart' ?? [])));
        $workingCost = workingCost(cartTotal(session('cart' ?? [])));
        $subtotal = cartTotal(session('cart' ?? []));
        $deliveryCharge = deliveryCharge($area);

        if (session('coupon')) {
            $discount = discountPrice(cartTotal(session('cart' ?? [])), session('coupon'))['discountWithOutCurrency'];
        }

        if (isset($discount)) {
            $total = ($subtotal + $vat + $deliveryCharge + $workingCost) - $discount;
        } else {
            $total = $subtotal + $vat + $deliveryCharge + $workingCost;
        }



        DB::beginTransaction();
        try {
            $order = new Order();
            $order->user_id = \auth()->id() ?? null;
            if ($request->payment_method === 'cash') {
                $order->gateway_id = 1500;
            } elseif ($request->payment_method === 'wallet') {
                if (!auth()->user()) {
                    throw new \Exception('You need to login first');
                }
                $order->gateway_id = 1600;
            }
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->phone = $request->phone;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->address = $request->address;
            $order->area_id = $request->area;
            $order->additional_information = $request->additional_information;
            if (session('coupon')) {
                $discount = discountPrice(cartTotal(session('cart' ?? [])), session('coupon'))['discountWithOutCurrency'];
                $order->coupon_code = session('coupon');
                $order->discount = $discount;
            }
            $order->subtotal = $subtotal;
            $order->delivery_charge = $deliveryCharge;
            $order->vat = $vat;
            $order->working_cost = $workingCost;
            $order->total = $total;
            $order->payment_status = 5;
            $order->save();

            $cart = session()->get('cart', []);
            $itemsData = [];
            foreach ($cart as $item) {
                $itemsData[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }
            $order->orderItem()->createMany($itemsData);

            session()->forget('cart');
            session()->forget('coupon');
            session()->forget('discountPrice');
            session()->forget('discount');

            session()->put('order_id', $order->id);
            session()->put('total', $total);
            DB::commit();
            return redirect()->route('payment');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function payment()
    {
        $data['gateways'] = Gateway::where('status', 1)->orderBy('sort_by', 'ASC')->get();
        $data['amount'] = session('total');
        return view(template() . 'pages.order_payment', $data);
    }
    public function orderComplete($id)
    {
        $pageSeo = Page::where('slug', 'order_complete')->first();
        $pageSeo->meta_keywords = isset($pageSeo->meta_keywords) && $pageSeo->meta_keywords ? implode(",", $pageSeo->meta_keywords) : '';
        $pageSeo->image = getFile($pageSeo->meta_image_driver, $pageSeo->meta_image);
        $order = Order::findOrFail($id);
        return view(template() . 'pages.order_completed', compact('order', 'pageSeo'));
    }
    public function orderPaymentRequest(Request $request)
    {
        $id = session()->get('order_id');
        $amount = session('total');
        $gateway = $request->gateway_id;
        $currency = $request->supported_currency ?? '';
        $cryptoCurrency = $request->supported_crypto_currency;

        $order = Order::findOrFail($id);

        try {
            if ($gateway == 1500 || $gateway == 1600){
                if ($gateway == 1600){
                    if (!auth()->user()){
                        throw new \Exception('You need to login first');
                    }elseif (auth()->user()->balance < $amount){
                        throw new \Exception('Insufficient balance');
                    }

                }
               $payment = $this->manualPayment($gateway,$order,$amount);

                if ($payment === true){
                    return redirect()->route('success', $order->id);
                }else{
                    return back()->with('error', $payment);
                }
            }else{
                $checkAmountValidate = $this->validationCheck($amount, $gateway, $currency, $cryptoCurrency, 'order');
                if ($checkAmountValidate['status'] == 'error') {
                    return back()->with('error', $checkAmountValidate['msg']);
                }
            }

            $deposit = Deposit::create([
                'user_id' => Auth::id() ?? null,
                'payment_method_id' => $checkAmountValidate['data']['gateway_id']??$gateway,
                'payment_method_currency' => $checkAmountValidate['data']['currency'],
                'amount' => $checkAmountValidate['data']['amount'],
                'depositable_id' => $id,
                'depositable_type' => 'App\Models\Order',
                'percentage_charge' => $checkAmountValidate['data']['percentage_charge'],
                'fixed_charge' => $checkAmountValidate['data']['fixed_charge'],
                'payable_amount' => $checkAmountValidate['data']['payable_amount'],
                'base_currency_charge' => $checkAmountValidate['data']['base_currency_charge'],
                'payable_amount_in_base_currency' => $checkAmountValidate['data']['payable_amount_base_in_currency'],
                'status' => 0,
            ]);

            $order->depositable()->save($deposit);
            session()->forget('total');
            session()->forget('order_id');
            return redirect(route('payment.process', $deposit->trx_id));
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }


    private function manualPayment($gateway,$order,$amount)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            if ($gateway == 1500){
                $deposit = Deposit::create([
                    'user_id' => Auth::id()??null,
                    'payment_method_id' => 1500,
                    'payment_method_currency' => basicControl()->base_currency,
                    'amount' => $order->total,
                    'percentage_charge' => 0,
                    'fixed_charge' => 0,
                    'payable_amount' => $order->total,
                    'base_currency_charge' => 0,
                    'payable_amount_in_base_currency' => $order->total,
                    'status' => 2,
                ]);
                $order->depositable()->save($deposit);

                $order->transaction_id = $deposit->trx_id;
                $order->gateway_id = 1500;
                 $order->payment_status = 0;
                $order->save();
            }elseif ($gateway == 1600){
                $user->balance = $user->balance - $amount;
                $user->save();
                $transaction = new Transaction();
                $transaction->user_id = Auth::user()->id;
                $transaction->amount = $amount;
                $transaction->charge = 0;
                $transaction->trx_type = '+';
                $transaction->remarks = 'Payment Via Wallet';
                $order->transactional()->save($transaction);
                $order->transaction_id = $transaction->trx_id;
                $order->payment_status = 1;
                $order->gateway_id = 1600;
                $order->save();
            }else{
                return 'Payment Gateway not supported yet';
            }
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();

            return 'Something went wrong';
        }

            $params = [
                'order_number' => $order->order_number,
                'trx_id' => $order->transaction_id,
                'username' => $user->username ?? 'Guest User',
                'payment_amount' => currencyPosition($order->total),
                'payment_method' => $gateway == 1500 ? 'Cash on Delivery' : 'Wallet',
                'datetime' => dateTime($order->created_at),
            ];

            $action = [
                "link" => "#",
                "icon" => "fa-regular fa-bell"
            ];

            $firebaseAction = "#";

            if ($user) {
                $this->sendMailSms($user, 'PAYMENT_CONFIRMATION', $params);
                $this->userPushNotification($user, 'PAYMENT_CONFIRMATION', $params, $action);
                $this->userFirebasePushNotification($user, 'PAYMENT_CONFIRMATION', $params, $firebaseAction);
            }

            if ($user) {
                $actionAdmin = [
                    "name" => $user->firstname . ' ' . $user->lastname,
                    "image" => getFile($user->image_driver, $user->image),
                    "link" => route('admin.transaction'),
                    "icon" => "fa-regular fa-bell"
                ];
            } else {
                $actionAdmin = [
                    "name" => 'Guest User',
                    "image" => getFile('local', 'image.avif'),
                    "link" => route('admin.transaction'),
                    "icon" => "fa-regular fa-bell"
                ];
            }

            $firebaseAction = route('admin.transaction');
            $this->adminMail('PAYMENT_CONFIRMATION_ADMIN', $params);
            $this->adminPushNotification('PAYMENT_CONFIRMATION_ADMIN', $params, $actionAdmin);
            $this->adminFirebasePushNotification('PAYMENT_CONFIRMATION_ADMIN', $params, $firebaseAction);


        return true;
    }
}
