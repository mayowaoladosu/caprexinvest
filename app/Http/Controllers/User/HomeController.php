<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\DistributeBonus;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\GoldPrice;
use App\Models\InvestHistory;
use App\Models\InvestmentPlan;
use App\Models\Kyc;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Payout;
use App\Models\Ranking;
use App\Models\ReferralBonus;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Rules\PhoneLength;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Facades\App\Services\BasicService;



class HomeController extends Controller
{
    use Upload;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
        $this->theme = template();
    }

    public function saveToken(Request $request)
    {
        try {
            Auth::user()
                ->fireBaseToken()
                ->create([
                    'token' => $request->token,
                ]);
            return response()->json([
                'msg' => 'token saved successfully.',
            ]);
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }


    public function index()
    {
        $data['user'] = Auth::user();
        $data['firebaseNotify'] = config('firebase');

        $data['total_deposit'] =  Deposit::where('user_id', Auth::id())->where('depositable_type',Deposit::class) ->where('status', 1)->sum('payable_amount_in_base_currency');
        $data['total_withdraw'] = Payout::where('user_id', Auth::id())->where('status', 2)->sum('amount_in_base_currency');
        $data['last_withdraw'] = Payout::where('user_id', Auth::id())->where('status', 2)->select('amount_in_base_currency')->orderBy('created_at', 'desc')->first();
        $data['recent_plan'] = InvestHistory::with('plan')->where('user_id', Auth::id())->latest()->take(4)
            ->orderByDesc('created_at')->get();
        $data['tickets'] = SupportTicket::with('user') // Eager load related models
        ->where('user_id', Auth::id())
            ->get()->count();


        $sevenDaysAgo = Carbon::now()->subDays(7);

        $data['recentTickets'] = SupportTicket::with('user') // Eager load related models
        ->where('user_id', Auth::id())->where('created_at', '>=', $sevenDaysAgo)->count('id');
        $data['recent_withdraw'] = Payout::where('user_id', Auth::id())->where('status', 2)
            ->where('created_at', '>=', $sevenDaysAgo)->sum('amount_in_base_currency');
        $data['recent_plan_invest'] = InvestHistory::with('plan')->where('user_id', Auth::id())
            ->where('created_at', '>=', $sevenDaysAgo)
            ->sum('invest_amount');
        $data['recent_deposit'] = Deposit::where('user_id', Auth::id())
            ->where('status' ,1)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->sum('payable_amount_in_base_currency');

        $data['rank'] = auth()->user()->rank;

        return view(template() . 'user.dashboard', $data);
    }


    public function profile()
    {
        $data['languages'] = Language::all();
        $data['allKyc'] = Kyc::with('userKyc')->where('status', 1)->get();
        $data['user'] = Auth::user();
        return view(template() . 'user.profile.my_profile', $data);
    }

    public function profileUpdateImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:png,jpg|max:3072',
            ]);
            $user = Auth::user();
            if ($request->hasFile('image')) {
                $image = $this->fileUpload($request->image, config('filelocation.userProfile.path'), null, null, 'avif', 60, $user->image, $user->image_driver);
                if ($image) {
                    $profileImage = $image['path'];
                    $ImageDriver = $image['driver'];
                }
            }
            $user->image = $profileImage ?? $user->image;
            $user->image_driver = $ImageDriver ?? $user->image_driver;
            $user->save();
            return response()->json('Updated Successfully.');
        }catch (\Exception $exception){
            return response()->json(['err' => $exception->getMessage()],200);
        }
    }

    public function profileUpdate(Request $request)
    {
        $languages = Language::all()->map(function ($item) {
            return $item->id;
        });

        $req = $request->except('_method', '_token');
        $user = Auth::user();
        $phoneCode = $request->phone_code;
        $rules = [
            'first_name' => 'required|string|min:1',
            'last_name' => 'required|string|min:1',
            'email' => 'email:rfc,dns|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', new PhoneLength($phoneCode),Rule::unique('users', 'phone')->ignore($user->id)],
            'phone_code' => 'required | max:15',
            'country_code' => 'required | string | max:80',
            'country' => 'required | string | max:80',
            'username' => "sometimes|required|alpha_dash|min:5|unique:users,username," . $user->id,
            'address' => 'required',
            'language' => Rule::in($languages),
        ];
        $message = [
            'firstname.required' => 'First Name field is required',
            'lastname.required' => 'Last Name field is required',
        ];

        $validator = Validator::make($req, $rules, $message);
        if ($validator->fails()) {
            $validator->errors()->add('profile', '1');
            return back()->withErrors($validator)->withInput();
        }
        $user->language_id = $req['language'];
        $user->firstname = $req['first_name'];
        $user->lastname = $req['last_name'];
        $user->email = $req['email'];
        $user->username = $req['username'];
        $user->address_one = $req['address'];
        $user->phone = $req['phone'];
        $user->phone_code =  $req['phone_code'];
        $user->country_code = $req['country_code'];
        $user->country = $req['country'];
        $user->save();
        return back()->with('success', 'Updated Successfully.');
    }


    public function updatePassword(Request $request)
    {
        $rules = [
            'current_password' => "required",
            'password' => "required|min:5|confirmed",
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = Auth::user();
        try {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->save();
                return back()->with('success', 'Password Changes successfully.');
            } else {
                throw new \Exception('Current password did not match');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function addFund()
    {
        $data['basic'] = basicControl();
        $data['gateways'] = Gateway::where('status', 1)->orderBy('sort_by', 'ASC')->get();
        return view(template() . 'user.fund.add_fund', $data);
    }

    public function fund(Request $request)
    {
        $trx = $request->trx_id;
        $filterDate = explode('to', $request->date_range);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;
        $userId = Auth::id();
        $funds = Deposit::with(['depositable', 'gateway'])
            ->whereHas('gateway')
            ->where('depositable_type',Deposit::class)
            ->where('user_id', $userId)
            ->when(!empty($request->date_range) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->date_range) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $endDate = Carbon::parse(trim($endDate))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(!empty($trx), function ($query) use ($trx) {

                return $query->where('trx_id', $trx);
            })
            ->orderBy('id', 'desc')
            ->latest()->paginate(15);
        return view($this->theme . 'user.fund.index', compact('funds'));
    }


    public function investPlan(Request $request)
    {
        // validation rules
        $rules = [
            'balance_type' => 'required | in:checkout,balance,profit',
            'amount' => 'required|numeric',
            'plan_id' => 'required',
        ];

        // validate request
        $validator = Validator::make($request->all(), $rules);

        //if validation failed then return back with validation error message
        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $balance_type = $request->balance_type;
        $user = Auth::guard('web')->user();

        try {

            $plan = InvestmentPlan::where(['status' => 1, 'id' => $request->plan_id])->firstOr(function () {
                throw new \Exception('Invalid plan request');
            });

            $amount = $request->amount;

            // validate invest amount , user balance & unit
            if ($plan->amount_has_fixed && $plan->plan_price != $amount){
                throw new \Exception("Please invest " . currencyPosition($plan->plan_price));
            }
            if (!$plan->amount_has_fixed && $plan->min_invest > $amount){
                throw new \Exception("Minimum Invest Limit " . currencyPosition($plan->min_invest));
            }
            if (!$plan->amount_has_fixed && $plan->max_invest < $amount){
                throw new \Exception("Maximum Invest Limit " . currencyPosition($plan->max_invest));
            }

            //if payment type or balance type is checkout then redirect to payment page
            if ($balance_type == 'checkout'){
                session()->put('amount', encrypt($amount));
                session()->put('plan_id', encrypt($plan->id));
                return  redirect()->route('user.payment');
            }

            // check balance type is profit balance or wallet balance
            if ($balance_type == 'profit'){

                //throw error if user profit balance is low
                if ($amount > $user->profit_balance) {
                    throw  new  \Exception('Insufficient Balance');
                }

                $profit = $plan->Profit($amount);
                //make invest
                $invest =  BasicService::makeInvest($user,$plan,$profit,null,$amount);

                if ($invest){
                    //make transaction
                    $transactional_type = 'App\Models\InvestmentPlan';
                    $transaction = BasicService::makeTransaction($user,$amount,0,'-',$invest->trx,'Investment from profit balance',$transactional_type,'profit');
                    $plan->transactional()->save($transaction);

                    //update user balance
                    $user->profit_balance = getAmount($user->profit_balance - $amount);
                    $user->total_invest += $amount;
                    $user->save();

                    //distribute referral bonus for investment
                    if (basicControl()->investment_commission && $user->referral_id){
                        DistributeBonus::dispatch($user, $amount, 'invest',$plan);
                    }

                    return redirect()->route('success')->with('success', 'Plan has been Purchased Successfully');
                }else{
                    return  redirect()->route('failed')->with('error', 'Something Went Wrong');
                }
            }

            // if user balance type is wallet

            //throw error if user wallet balance is low
            if ($amount > $user->balance) {
                throw  new  \Exception('Insufficient Balance');
            }

            $profit = $plan->Profit($amount);

            // make invest
            $invest =  BasicService::makeInvest($user,$plan,$profit,null,$amount);

            if ($invest){

                //make transaction
                $transactional_type = 'App\Models\InvestmentPlan';
                $transaction = BasicService::makeTransaction($user,$amount,0,'-',$invest->trx,'Investment from wallet',$transactional_type,'wallet');
                $plan->transactional()->save($transaction);

                //update user balance
                $user->balance = getAmount($user->balance - $amount);
                $user->total_invest += $amount;
                $user->save();

                //distribute referral bonus for investment
                if (basicControl()->investment_commission && $user->referral_id){
                    DistributeBonus::dispatch($user, $amount, 'invest',$plan);
                }
                return redirect()->route('success')->with('success', 'Plan has been Purchased Successfully');
            }else{
                return  redirect()->route('failed')->with('error', 'Something Went Wrong');
            }
        }catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function investment(Request $request)
    {
        $name = $request->name;
        $filterDate = explode('to', $request->date_range);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $planInvestment = InvestHistory::with('plan')->where('user_id', Auth::id())
            ->when(!empty($request->date_range) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->date_range) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $endDate = Carbon::parse(trim($endDate))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(!empty($name), function ($query) use ($name) {
                return $query->whereHas('plan',function ($query)use($name){
                    return $query->where('plan_name','LIKE','%'.$name.'%');
                });
            })
            ->orderBy('created_at','DESC')->paginate(12);

        return view(template().'user.investment.plan_investment',compact('planInvestment'));
    }

    public function orders()
    {
        if (!basicControl()->ecommerce){
            abort(403);
        }
        $filterDate = explode('to', request()->date_range);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $orders = Order::query()
            ->orderBy('created_at','desc')
            ->where('user_id', Auth::id())
            ->where('payment_status','!=' , 5)
            ->when(request()->has('order_id') && request()->order_id, function ($query) {
                return $query->where('order_number',request()->order_id);
            })
            ->when(!empty(request()->date_range) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty(request()->date_range) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $endDate = Carbon::parse(trim($endDate))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(request()->has('status') && request()->status && request()->status !== 'all', function ($query) {
                if (request()->status == '4'){
                    return $query->where('order_status', 0);
                }else{
                    return $query->where('order_status', request()->status);
                }
            })
             ->whereNotNull('gateway_id')
            ->paginate(15);

        return view(template().'user.order.index',compact('orders'));
    }

    public function orderItems($id)
    {
        $orderItems = OrderItems::query()->with('product.details','order')->where('order_id',$id)->get();
        return view(template().'user.order.show',compact('orderItems'));
    }


    public function badges()
    {
        $data['allBadges'] = Ranking::orderBy('sort_by', 'ASC')->get();
        return view(template() . 'user.badge.index', $data);
    }

    public function referral()
    {
        $userId = Auth::id();
        $data['title'] = "My Referrals";
        $data['directReferralUsers'] = getDirectReferralUsers($userId);
        return view(template().'user.referral.referral',$data);
    }
    public function referralBonus(Request $request)
    {

        $remark = $request->remark;
        $filterDate = explode('to', $request->date_range);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;
        $commission_type = $request->type;


        $referrals = ReferralBonus::query()->with(['user:id,username,firstname,lastname,image,image_driver'])
            ->where('from_user_id', Auth::id())
            ->orderBy('created_at','desc')
            ->when(!empty($request->date_range) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->date_range) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $endDate = Carbon::parse(trim($endDate))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(!empty($remark), function ($query) use ($remark) {
                return $query->where('remarks', $remark);
            })
            ->when(!empty($commission_type),function ($query) use ($commission_type){
                return $query->where('commission_type', $commission_type);
            })
            ->paginate(15);

        return view(template().'user.referral.referral_bonus',compact('referrals'));
    }

    public function getReferralsBonus()
    {
        $referrals = ReferralBonus::where('from_user_id', Auth::id())
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('month', 'year')
            ->get();
        return response()->json([
            'referrals' => $this->formatChartData($referrals),
        ]);
    }

    public function getReferralUser(Request $request)
    {
        $data = getDirectReferralUsers($request->userId);
        $directReferralUsers = $data->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'count_direct_referral' => count(getDirectReferralUsers($user->id)),
                'joined_at' => dateTime($user->created_at),
            ];
        });

        return response()->json(['data' => $directReferralUsers]);
    }

    public function transactions(Request $request)
    {
        $dateRange = str_replace('to', '', $request->date_range);
        $dateRange = preg_replace('/\s+/', ' ', $dateRange);
        $dateArray = array_map('trim', explode(' ', $dateRange));
        $fromDate = false;
        $toDate = false;
        if ($request->date_range){
            $fromDate = $dateArray[0];
            $toDate = $dateArray[1];
        }

        $transactions = Transaction::where('user_id', Auth::id())
            ->when($request->trx_id , function ($query) use ($request){
                $query->where('trx_id',$request->trx_id);
            })
            ->when($request->remark , function ($query) use ($request){
                $query->where('remarks','LIKE','%'.$request->remark.'%');
            })
            ->when($fromDate && $toDate , function ($query) use ($fromDate ,$toDate){
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view(template().'user.transaction.index',compact('transactions'));
    }

    public function investHistory(Request $request)
    {

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $diffInDays = $startDate->diffInDays($endDate);
        $groupBy = $diffInDays <= 1 ? 'HOUR' : 'DATE';
        $dateFormat = $diffInDays <= 1 ? 'ga' : 'd-M';

        $planInvest = InvestHistory::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw($groupBy . '(created_at) as period, SUM(invest_amount) as total')
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        $referrals = ReferralBonus::where('from_user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw($groupBy . '(created_at) as period, SUM(amount) as total')
            ->groupBy('period')
            ->get();

        $labels = [];
        $investData = [];
        $revenueData = [];

        foreach ($planInvest as $data) {
            $period = $data->period;
            $formattedPeriod = $diffInDays <= 1 ? date($dateFormat, strtotime($period . ':00')) : date($dateFormat, strtotime($period));
            $labels[] = $formattedPeriod;
            $investData[] = $data->total;
        }

        foreach ($referrals as $data) {
            $period = $data->period;
            $formattedPeriod = $diffInDays <= 1 ? date($dateFormat, strtotime($period . ':00')) : date($dateFormat, strtotime($period));
            $labels[] = $formattedPeriod;
            $revenueData[] = $data->total;
        }


        $response = [
            'labels' => $labels,
            'investData' => $investData,
            'referral_bonus' => $revenueData,
        ];

        return response()->json($response);

    }

    public function depositPayout(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $diffInDays = $startDate->diffInDays($endDate);
        $groupBy = $diffInDays <= 1 ? 'HOUR' : 'DATE';
        $dateFormat = $diffInDays <= 1 ? 'ga' : 'd-M';

        $deposits = Deposit::where('user_id', Auth::id())
            ->where('status', 1)
            ->where('depositable_type', Deposit::class)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw($groupBy . '(created_at) as period, SUM(payable_amount_in_base_currency) as total')
            ->groupBy('period')
            ->get();


        $payouts = Payout::where('user_id', Auth::id())
            ->where('status', 2)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw($groupBy . '(created_at) as period, SUM(amount_in_base_currency) as total')
            ->groupBy('period')
            ->get();

        $labels = [];
        $depositData = [];
        $payoutData = [];

        foreach ($deposits as $data) {
            $period = $data->period;
            $formattedPeriod = $diffInDays <= 1 ? date($dateFormat, strtotime($period . ':00')) : date($dateFormat, strtotime($period));
            $labels[] = $formattedPeriod;
            $depositData[] = $data->total;
        }

        foreach ($payouts as $data) {
            $period = $data->period;
            $formattedPeriod = $diffInDays <= 1 ? date($dateFormat, strtotime($period . ':00')) : date($dateFormat, strtotime($period));
            $labels[] = $formattedPeriod;
            $payoutData[] = $data->total;
        }

        return response()->json([
            'deposits' => $depositData,
            'payouts' => $payoutData,
            'labels' => $labels,
        ]);

    }

    public function transactionHistory(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $diffInDays = $startDate->diffInDays($endDate);
        $groupBy = $diffInDays <= 1 ? 'HOUR' : 'DATE';
        $dateFormat = $diffInDays <= 1 ? 'ga' : 'd-M';

        $transactions = Transaction::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw($groupBy . '(created_at) as period, SUM(amount) as total')
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        $labels = [];
        $transactionData = [];

        foreach ($transactions as $data) {
            $period = $data->period;
            $formattedPeriod = $diffInDays <= 1 ? date($dateFormat, strtotime($period . ':00')) : date($dateFormat, strtotime($period));
            $labels[] = $formattedPeriod;
            $transactionData[] = $data->total;
        }
        return response()->json([
            'transactions' => $transactionData,
            'labels' => $labels,
        ]);
    }


    public function goldChartData(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Group by date and get average rate per day
        $goldRates = GoldPrice::selectRaw('DATE(recorded_at) as date, AVG(rate) as rate')
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $labels = $goldRates->pluck('date'); // already YYYY-MM-DD
        $rates = $goldRates->pluck('rate');

        return response()->json([
            'labels' => $labels,
            'rates' => $rates
        ]);
    }

    private function getFormattedDates()
    {
        $formattedDates = [];
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(now()->year, $month, 1);
            $formattedDates[] = $date->format('M');  // Short month name
        }

        return $formattedDates;
    }

    private function formatChartData($data)
    {
        $formattedData = array_fill(0, 12, 0);
        foreach ($data as $item) {
            $formattedData[$item->month - 1] = getAmount($item->total);
        }
        return $formattedData;
    }

}
