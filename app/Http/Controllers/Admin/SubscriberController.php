<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SubscriberController extends Controller
{
    public function index()
    {
        return view('admin.subscriber.index');
    }

    public function getList(Request $request)
    {
        $subscribers = Subscriber::query()
            ->when(!empty(request()->search['value']), function ($query) {
                $query->where('email','LIKE','%'.request()->search['value'].'%');
            });

        return DataTables::of($subscribers)
            ->addColumn('no', function ($subscriber) {
                $count = 0;
                return ++$count;
            })
            ->addColumn('email', function ($subscriber) {
                return $subscriber->email;
            })
            ->addColumn('joined', function ($subscriber) {
                return dateTime($subscriber->created_at);
            })
            ->addColumn('action', function ($subscriber) {
                $actionHtml = '';
                $deleteUrl = route('admin.subscriber.remove', $subscriber->id);
                $actionHtml =  "<a class='btn btn-white btn-sm loginAccount deleteBtn' data-bs-toggle='modal' data-bs-target='#deleteModal' href='javascript:void(0)' data-route='$deleteUrl'>
                           <i class='bi bi-trash dropdown-item-icon'></i>
                          ".trans("Delete")."
                        </a>";
                return $actionHtml;
            })
            ->rawColumns(['no','email','joined','action'])
            ->make(true);
    }

    public function sendEmailForm()
    {
        return view('admin.subscriber.email_form');
    }

    public function sendEmail(Request $request)
    {
        $rules = [
            'subject' => 'required',
            'message' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $basic = basicControl();
        $email_from = $basic->sender_email;
        $requestMessage = $request->message;
        $subject = $request->subject;
        $email_body = $basic->email_description;
        if (!Subscriber::first()) return back()->withInput()->with('error', 'No subscribers to send email.');
        $subscribers = Subscriber::all();
        foreach ($subscribers as $subscriber) {
            $name = explode('@', $subscriber->email)[0];
            $message = str_replace("[[name]]", $name, $email_body);
            $message = str_replace("[[message]]", $requestMessage, $message);
            Mail::to($subscriber->email)->queue(new SendMail($email_from, $subject, $message));
        }
        return back()->with('success', 'Email has been sent to subscribers.');
    }

    public function remove($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
        return back()->with('success', 'Subscriber has been removed');
    }
}
