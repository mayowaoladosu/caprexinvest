<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UserSystemInfo;
use App\Http\Controllers\Controller;
use App\Models\ContentDetails;
use App\Models\Page;
use App\Models\Ranking;
use App\Models\UserLogin;
use App\Providers\RouteServiceProvider;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers,Notify;

    protected $maxAttempts = 3; // Change this to 4 if you want 4 tries
    protected $decayMinutes = 5; // Change this according to your

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->theme = template();
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        $data['siteKey'] = config('google.recaptcha_site_key');

        $section = 'login_section';
        $footer_section = ContentDetails::with('content')
            ->whereHas('content', function ($query) use ($section) {
                $query->where('name', $section);
            })
            ->get();
        $single_content = $footer_section->where('content.name', 'login_section')->where('content.type', 'single')->first();
        $data['login_section']= [
            'single' => $single_content? collect($single_content->description ?? [])->merge($single_content->content->only('media')) : [],
        ];
        $page= Page::where('slug', 'login')->first();
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
        return view($this->theme . 'auth/login', $data);
    }



    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    public function login(Request $request)
    {
        $basicControl = basicControl();
        $rules = [
            $this->username() => 'required',
            'password' => 'required',
        ];

        // Manual CAPTCHA Validation
        if ($basicControl->manual_recaptcha == 1 && $basicControl->manual_recaptcha_user_login == 1) {
            $rules['captcha'] = [
                Rule::requiredIf(function () use ($basicControl) {
                    return $basicControl->manual_recaptcha == 1 && $basicControl->manual_recaptcha_user_login == 1;
                }),
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('The captcha field is required.');
                    } elseif (session()->get('captcha') !== $value) {
                        $fail('The captcha does not match.');
                    }
                },
            ];
        }

        if ($basicControl->google_reCaptcha_user_login == 1 && $basicControl->google_recaptcha == 1) {
            $rules['g-recaptcha-response'] = [
                Rule::requiredIf(function () use ($basicControl) {
                    return $basicControl->google_reCaptcha_user_login && $basicControl->google_recaptcha == 1;
                }),
                function ($attribute, $value, $fail) use ($basicControl) {
                    if ($basicControl->google_reCaptcha_user_login && $basicControl->google_recaptcha == 1) {
                        if (!$value) {
                            $fail('The reCAPTCHA field is required.');
                        }
                        // Optionally, you can add the code to verify Google's reCAPTCHA response here
                    }
                },
            ];
        }

        $message['captcha.confirmed'] = "The captcha does not match.";
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->guard()->validate($this->credentials($request))) {
            if (Auth::attempt([$this->username() => $request->username, 'password' => $request->password])) {
                return $this->sendLoginResponse($request);
            } else {
                return back()->with('error', 'You are banned from this application. Please contact with system Administrator.');
            }
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }


    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard('admin')->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->last_login = Carbon::now();
        $user->last_seen = Carbon::now();
        $user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;
        $user->save();

        $interestBalance = (float)$user->total_profit; //5
        $investBalance = (float)$user->total_invest; //50
        $depositBalance = (float)$user->total_deposit; //5.0

        $badges = Ranking::where('min_invest', '<=', $investBalance)
            ->where('min_deposit', '<=', $depositBalance)
            ->where('min_earning', '<=', $interestBalance)
            ->where('status', 1)
            ->get();



        if ($badges){
            foreach ($badges as $badge) {
                if (($user->total_invest >= $badge->min_invest) && ($user->total_deposit >= $badge->min_deposit) && ($user->total_profit >= $badge->min_earning) ) {
                    $userBadge = $badge;
                }
            }


            if (isset($userBadge) && $userBadge->rank_lavel != $user->last_level ) {
                $user->last_level = $userBadge->rank_lavel;
                $user->rank_id = $userBadge->id;
                $user->save();


                $msg = [
                    'user' => $user->fullname,
                    'badge' => $userBadge->rank_lavel,
                ];

                $adminAction = [
                    "name" => $user->firstname . ' ' . $user->lastname,
                    "image" => getFile($user->image_driver, $user->image),
                    "link" => route('admin.users'),
                    "icon" => "fas fa-ticket-alt text-white",
                ];

                $userAction = [
                    "link" => route('user.profile'),
                    "icon" => "fa fa-money-bill-alt text-white"
                ];

                $this->userPushNotification($user, 'BADGE_NOTIFY_TO_USER', $msg, $userAction);
                $this->userFirebasePushNotification($user,'BADGE_NOTIFY_TO_USER', $msg,route('user.profile'));
                $this->sendMailSms($user,'BADGE_MAIL_TO_USER', [
                    'user' => $user->fullname,
                    'badge' => $userBadge->rank_lavel,
                    'date' => Carbon::now()
                ]);

                $this->adminPushNotification('BADGE_NOTIFY_TO_ADMIN', $msg, $adminAction);
                $this->adminFirebasePushNotification( 'BADGE_NOTIFY_TO_ADMIN', $msg,route('admin.users'));
                $this->adminMail('BADGE_MAIL_TO_USER', [
                    'user' => $user->fullname,
                    'badge' => $userBadge->rank_lavel,
                    'date' => Carbon::now()
                ]);
            }
        }
        $user->last_login = Carbon::now();
        $user->last_seen = Carbon::now();
        $user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;
        $user->save();

        $info = @json_decode(json_encode(getIpInfo()), true);
        $ul['user_id'] = $user->id;

        $ul['longitude'] = (!empty(@$info['long'])) ? implode(',', $info['long']) : null;
        $ul['latitude'] = (!empty(@$info['lat'])) ? implode(',', $info['lat']) : null;
        $ul['country_code'] = (!empty(@$info['code'])) ? implode(',', $info['code']) : null;
        $ul['location'] = (!empty(@$info['city'])) ? implode(',', $info['city']) . (" - " . @implode(',', @$info['area']) . "- ") . @implode(',', $info['country']) . (" - " . @implode(',', $info['code']) . " ") : null;
        $ul['country'] = (!empty(@$info['country'])) ? @implode(',', @$info['country']) : null;

        $ul['ip_address'] = UserSystemInfo::get_ip();
        $ul['browser'] = UserSystemInfo::get_browsers();
        $ul['os'] = UserSystemInfo::get_os();
        $ul['get_device'] = UserSystemInfo::get_device();

        UserLogin::create($ul);

    }


}
