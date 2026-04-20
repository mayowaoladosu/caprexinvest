<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\PayoutMethod;
use App\Models\Transaction;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PayoutLogController extends Controller
{
    use Notify , Upload;

    public function index()
    {
        $payoutRecord = \Cache::get('payoutRecord');
        if (!$payoutRecord) {
            $payoutRecord = Payout::selectRaw('COUNT(id) AS totalWithdrawLog')
                ->selectRaw('COUNT(CASE WHEN status = 1 THEN id END) AS pendingWithdraw')
                ->selectRaw('(COUNT(CASE WHEN status = 1 THEN id END) / COUNT(id)) * 100 AS pendingWithdrawPercentage')
                ->selectRaw('COUNT(CASE WHEN status = 2 THEN id END) AS successWithdraw')
                ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END) / COUNT(id)) * 100 AS successWithdrawPercentage')
                ->selectRaw('COUNT(CASE WHEN status = 3 THEN id END) AS cancelWithdraw')
                ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END) / COUNT(id)) * 100 AS cancelWithdrawPercentage')
                ->get()
                ->toArray();
            \Cache::put('payoutRecord', $payoutRecord);
        }

        $data['methods'] = PayoutMethod::where('is_active', 1)->orderBy('id', 'asc')->get();
        return view('admin.payout.logs', $data, compact('payoutRecord'));
    }

    public function search(Request $request)
    {
        $filterTransactionId = $request->filterTransactionID;
        $filterStatus = $request->filterStatus;
        $filterMethod = $request->filterMethod;
        $basicControl = basicControl();
        $search = $request->search['value'] ?? null;

        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $payout = Payout::query()->with(['user:id,username,firstname,lastname,image,image_driver', 'method:id,name,logo,driver'])
            ->whereHas('user')
            ->whereHas('method')
            ->orderBy('id', 'desc')
            ->where('status', '!=', 0)
            ->orderBy('id', 'desc')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', $search)
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%$search%")
                            ->orWhere('username', 'LIKE', "%$search%");
                    });
            })
            ->when(!empty($filterTransactionId), function ($query) use ($filterTransactionId) {
                return $query->where('trx_id', $filterTransactionId);
            })
            ->when(isset($filterStatus), function ($query) use ($filterStatus) {
                if ($filterStatus == "all") {
                    return $query->where('status', '!=', null);
                }
                return $query->where('status', $filterStatus);
            })
            ->when(isset($filterMethod), function ($query) use ($filterMethod) {
                return $query->whereHas('method', function ($subQuery) use ($filterMethod) {
                    if ($filterMethod == "all") {
                        $subQuery->where('id', '!=', null);
                    } else {
                        $subQuery->where('id', $filterMethod);
                    }
                });
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($payout)
            ->addColumn('no', function ($item) {
                static $counter = 0;
                $counter++;
                return $counter;
            })
            ->addColumn('name', function ($item) {
                $url = route("admin.user.edit", optional($item->user)->id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . optional($item->user)->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->user)->firstname . ' ' . optional($item->user)->lastname . '</h5>
                                  <span class="fs-6 text-body">@' . optional($item->user)->username . '</span>
                                </div>
                              </a>';
            })
            ->addColumn('trx', function ($item) {
                return $item->trx_id;
            })
            ->addColumn('method', function ($item) {

                return '<a class="d-flex align-items-center me-2 cursor-unset" href="javascript:void(0)">
                                <div class="flex-shrink-0">
                                  ' . $item->picture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->method)->name . '</h5>
                                </div>
                              </a>';
            })
            ->addColumn('amount', function ($item) {
                $statusClass = $item->getStatusClass();
                return "<h6 class='mb-0 $statusClass '>" . fractionNumber(getAmount($item->amount)). ' ' . $item->payout_currency_code . "</h6>";

            })
            ->addColumn('charge', function ($item) {
                return "<span class='text-danger'>".getAmount($item->charge) . ' ' . $item->payout_currency_code."</span>";
            })
            ->addColumn('net amount', function ($item) {
                return "<h6>".currencyPosition($item->amount_in_base_currency +0)."</h6>";
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 0) {
                    return '<span class="badge bg-soft-warning text-warning">' . trans('Pending') . '</span>';
                } else if ($item->status == 1) {
                    return '<span class="badge bg-soft-warning text-warning">' . trans('Pending') . '</span>';
                } else if ($item->status == 2) {
                    return '<span class="badge bg-soft-success text-success">' . trans('Successful') . '</span>';
                } else if ($item->status == 3) {
                    return '<span class="badge bg-soft-danger text-danger">' . trans('Cancel') . '</span>';
                }
            })
            ->addColumn('date', function ($item) {
                return dateTime($item->created_at, 'd M Y h:i A');
            })
            ->addColumn('action', function ($item) {
                $route = route('admin.payout.show', $item->id);

                return "<a href='$route' class='btn btn-white btn-sm edit_btn'
               >  <i class='bi-eye fill me-1'></i> </a>";
            })
            ->rawColumns(['name', 'amount', 'charge', 'method', 'net amount', 'status', 'action'])
            ->make(true);

    }


    public function action(Request $request, $id)
    {

        $this->validate($request, [
            'id' => 'required',
            'status' => ['required', Rule::in(['2', '3'])],
        ]);
        $payout = Payout::where('id', $request->id)->whereIn('status', [1])->with('user', 'method')->firstOrFail();


        if ($request->status == 3) {
            $this->cancelPayout($request->id, $request->feedback);
            return back()->with('success', "Payment Rejected.");
        }

        if (optional($payout->payoutMethod)->is_automatic == 1) {
            $methodObj = 'App\\Services\\Payout\\' . optional($payout->payoutMethod)->code . '\\Card';
            $data = $methodObj::payouts($payout);

            if (!$data) {
                return back()->with('alert', 'Method not available or unknown errors occur');
            }

            if ($data['status'] == 'error') {
                $payout->feedback = $request->feedback;
                $payout->last_error = $data['data'];
                $payout->status = 3;
                $payout->save();
                return back()->with('error', $data['data']);
            }
        }

        if (optional($payout->payoutMethod)->is_automatic == 0) {
            $payout->feedback = $request->feedback;
            $payout->status = 2;
            $payout->save();

        } else {
            if (optional($payout->payoutMethod)->code == 'coinbase' || optional($payout->payoutMethod)->code == 'perfectmoney') {
                $payout->feedback = $request->note;
                $payout->status = 2;
                $payout->save();
            } else {
                $payout->note = $request->feedback;
                $payout->response_id = $data['response_id'];
                $payout->save();
            }
        }
        $this->userNotify($payout);

        return back()->with('success', 'Payment Confirmed');
    }

    public function cancelPayout($id, $feedback)
    {

        $payout = Payout::where('id', $id)->whereIn('status', [1])->with('user', 'method')->firstOrFail();

        if (!$payout) {
            return back()->with('error', 'Transaction not found');
        } elseif ($payout->status != 1) {
            return back()->with('error', 'Action not possible');
        }

        $payout->feedback = $feedback;
        $payout->status = 3;
        $payout->save();

        /* Add money from Sender Wallet */
        updateBalance($payout->user_id, $payout->net_amount_in_base_currency, 1,$payout->balance_type);


        $balance = $payout->balance_type == 'balance'?'main balance':'profit balance';
        $netAmount = currencyPosition($payout->net_amount_in_base_currency+0);
        $transaction = new Transaction();
        $transaction->user_id = $payout->user_id;
        $transaction->amount = $payout->net_amount_in_base_currency;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->trx_id = $payout->trx_id;
        $transaction->wallet_type = $payout->balance_type == 'balance'?'wallet':'profit';
        $transaction->remarks = "Add {$netAmount} to the {$balance} for rejected payout.";
        $payout->transactional()->save($transaction);

        $receivedUser = $payout->user;
        $params = [
            'sender' => $receivedUser->name,
            'amount' => getAmount($payout->amount),
            'currency' => $payout->payout_currency_code,
            'transaction' => $payout->trx_id,
        ];

        $action = [
            "link" => route('user.payout.index'),
            "icon" => "fa fa-money-bill-alt text-white"
        ];
        $firebaseAction = route('user.payout.index');
        $this->sendMailSms($receivedUser, 'PAYOUT_CANCEL', $params);
        $this->userPushNotification($receivedUser, 'PAYOUT_CANCEL', $params, $action);
        $this->userFirebasePushNotification($receivedUser, 'PAYOUT_CANCEL', $params, $firebaseAction);

    }

    public function userNotify($payout)
    {
        $msg = [
            'username' => optional($payout->user)->username,
            'amount' => getAmount($payout->amount),
            'currency' => $payout->payout_currency_code,
            'gateway' => optional($payout->method)->name,
            'transaction' => $payout->trx_id,
        ];
        $action = [
            "link" => '#',
            "icon" => "fas fa-money-bill-alt text-white"
        ];
        $fireBaseAction = "#";
        $this->userPushNotification($payout->user, 'PAYOUT_APPROVED', $msg, $action);
        $this->userFirebasePushNotification('PAYOUT_APPROVED', $msg, $fireBaseAction);
        $this->sendMailSms($payout->user, 'PAYOUT_APPROVED', [
            'gateway_name' => optional($payout->method)->name,
            'amount' => currencyPosition($payout->amount),
            'charge' => currencyPosition($payout->charge),
            'transaction' => $payout->trx_id,
            'feedback' => $payout->note,
        ]);
    }

    public function show($id)
    {
        $payout = Payout::with(['user','method'])
                        ->has('user')
                        ->has('method')
                        ->findOrFail($id);
        return view('admin.payout.show', compact('payout'));
    }

    public function update(Request $request, $id)
    {
        $payout = Payout::with(['user','method'])
            ->has('user')
            ->has('method')
            ->findOrFail($id);

        if ($payout->status == 3 && $request->status != 3){
            return back()->with('error', 'This withdraw request is already rejected');
        }

        $params = optional($payout->method)->inputForm;
        $rules = [];
        if ($params !== null) {
            foreach ($params as $key => $cus) {
                if ($cus->type != 'file'){
                    $rules[$key] = [$cus->validation == 'required' ? $cus->validation : 'nullable'];
                }else{
                    $rules[$key] = ['nullable'];
                }
                if ($cus->type === 'file') {
                    $rules[$key][] = 'image';
                    $rules[$key][] = 'mimes:jpeg,jpg,png';
                    $rules[$key][] = 'max:4048';
                } elseif ($cus->type === 'text') {
                    $rules[$key][] = 'max:191';
                } elseif ($cus->type === 'number') {
                    $rules[$key][] = 'integer';
                } elseif ($cus->type === 'textarea') {
                    $rules[$key][] = 'min:3';
                    $rules[$key][] = 'max:300';
                }
            }
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $reqField = [];

        foreach ($request->all() as $k => $v) {
            foreach ($params as $inKey => $inVal) {
                if ($k == $inVal->field_name) {
                    if ($inVal->type == 'file') {
                        if ($request->hasFile($inKey)){
                            try {
                                $file = $this->fileUpload($request[$inKey], config('filelocation.payoutLog.path'),null,null,'webp',60);
                                $reqField[$inKey] = [
                                    'field_name' => $inVal->field_name,
                                    'field_value' => $file['path'],
                                    'field_driver' => $file['driver'],
                                    'validation' => $inVal->validation,
                                    'type' => $inVal->type,
                                ];

                                if (isset($payout->information->{$inVal->field_name})){
                                    $this->fileDelete($payout->information->{$inVal->field_name}->field_driver,$payout->information->{$inVal->field_name}->field_value);
                                }
                            } catch (\Exception $exp) {
                                session()->flash('error', 'Could not upload your ' . $inKey);
                                return back()->withInput();
                            }
                        }else{
                           if (isset($payout->information->{$inVal->field_name})){
                               $reqField[$inKey] = [
                                   'field_name' => $inVal->field_name,
                                   'field_value' => $payout->information->{$inVal->field_name}->field_value,
                                   'field_driver' => $payout->information->{$inVal->field_name}->field_driver,
                                   'validation' => $inVal->validation,
                                   'type' => $inVal->type,
                               ];
                           }
                        }

                    } else {
                        $reqField[$inKey] = [
                            'field_name' => $inVal->field_name,
                            'validation' => $inVal->validation,
                            'field_value' => $v,
                            'type' => $inVal->type,
                        ];
                    }

                    if (optional($payout->method)->code == 'paypal') {
                        $reqField['recipient_type'] = [
                            'field_name' => 'receiver',
                            'validation' => $inVal->validation,
                            'field_value' => $request->recipient_type,
                            'type' => 'text',
                        ];
                    }
                }elseif($k != $inVal->field_name && $inVal->type == 'file'){
                    $reqField[$inKey] = [
                        'field_name' => $inVal->field_name,
                        'field_value' => $payout->information->{$inVal->field_name}->field_value,
                        'field_driver' => $payout->information->{$inVal->field_name}->field_driver,
                        'validation' => $inVal->validation,
                        'type' => $inVal->type,
                    ];
                }
            }

        }

        $payout->feedback = $request->feedback;
        $payout->information = $reqField;
        $payout->save();

        if ($payout->status != 3 &&$request->status == 3) {
            $this->cancelPayout($payout->id, $request->feedback);
        }
        if ($payout->status != 2 && $request->status == 2){
            $this->approvedPayout($payout,$request->feedback);
        }

        if ($request->save_status == 2){
            return redirect()->route('admin.payout.log')->with('success', 'Payout updated successfully');
        }
        return back()->with('success', 'Payout updated successfully');
    }

    private function approvedPayout($payout,$feedback)
    {
        if (optional($payout->method)->is_automatic == 1) {
            $methodObj = 'App\\Services\\Payout\\' . optional($payout->payoutMethod)->code . '\\Card';
            $data = $methodObj::payouts($payout);

            if (!$data) {
                return back()->with('alert', 'Method not available or unknown errors occur');
            }

            if ($data['status'] == 'error') {
                $payout->feedback = $feedback;
                $payout->last_error = $data['data'];
                $payout->status = 3;
                $payout->save();
                return back()->with('error', $data['data']);
            }
        }

        if (optional($payout->payoutMethod)->is_automatic == 0) {
            $payout->feedback = $feedback;
            $payout->status = 2;
            $payout->save();

        } else {
            if (optional($payout->payoutMethod)->code == 'coinbase' || optional($payout->payoutMethod)->code == 'perfectmoney') {
                $payout->feedback = $feedback;
                $payout->status = 2;
                $payout->save();
            } else {
                $payout->note = $feedback;
                $payout->response_id = $data['response_id'];
                $payout->save();
            }
        }
        $this->userNotify($payout);
    }

}
