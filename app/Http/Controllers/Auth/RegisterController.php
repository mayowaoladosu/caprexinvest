<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UserSystemInfo;
use App\Http\Controllers\Controller;
use App\Models\ContentDetails;
use App\Models\NotificationPermission;
use App\Models\Page;
use App\Models\ReferralBonus;
use App\Models\User;
use App\Models\UserLogin;
use App\Traits\Notify;
use Facades\App\Services\BasicService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Rules\PhoneLength;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers,Notify;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    protected $maxAttempts = 3; // Change this to 4 if you want 4 tries
    protected $decayMinutes = 5; // Change this according to your
    protected $redirectTo = '/user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->theme = template();
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $basic = basicControl();
        if ($basic->registration == 0) {
            return redirect('/')->with('warning', 'Registration Has Been Disabled.');
        }
        $referUser = null;
        if ($request->ref){
            $referUser =  User::where('id', $request->ref)->first();

            if ($referUser){
                session()->put('referral', $referUser->id);
            }
        }
        $data['referUser'] = $referUser;

        $section = 'register_section';
        $footer_section = ContentDetails::with('content')
            ->whereHas('content', function ($query) use ($section) {
                $query->where('name', $section);
            })
            ->get();
        $single_content = $footer_section->where('content.name', 'register_section')->where('content.type', 'single')->first();
        $data['register_section']= [
            'single' => $single_content? collect($single_content->description ?? [])->merge($single_content->content->only('media')) : [],
        ];
        $data['siteKey'] = config('google.recaptcha_site_key');

        $page= Page::where('slug', 'register')->first();
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
        return view(template() . 'auth.register',$data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $basicControl = basicControl();



        if ($basicControl->strong_password == 0) {
            $rules['password'] = ['required', 'min:6', 'confirmed'];
        } else {
            $rules['password'] = ["required", 'confirmed',
                Password::min(6)->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()];
        }

        if ($basicControl->google_recaptcha && $basicControl->google_recaptcha_user_registration) {
            $rules['g-recaptcha-response'] = ['sometimes', 'required'];
        }

        if ($basicControl->manual_recaptcha && basicControl()->manual_recaptcha_user_registration){
            $rules['captcha'] = ['required',function($attribute, $value, $fail){
                if ($value !== session()->get('captcha')){
                    $fail('The captcha does not match');
                }
            }];
        }

        $rules['first_name'] = ['required', 'string', 'max:91'];
        $rules['last_name'] = ['required', 'string', 'max:91'];
        $rules['username'] = ['required', 'alpha_dash', 'min:5', 'unique:users,username'];
        $rules['email'] = ['required', 'string', 'email', 'max:255',  'unique:users,email'];
        $rules['phone'] = ['required', 'string', 'unique:users,phone', new PhoneLength($data['phone_code']??'')];
        $rules['phone_code'] = ['required', 'string', 'max:15'];
        return Validator::make($data, $rules, [
            'first_name.required' => 'First Name Field is required',
            'last_name.required' => 'Last Name Field is required',
            'g-recaptcha-response.required' => 'The reCAPTCHA field is required.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $basic = basicControl();
        $referUser = session()->get('referral');

        return User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verification' => ($basic->email_verification) ? 0 : 1,
            'sms_verification' => ($basic->sms_verification) ? 0 : 1,
            'referral_id' => $referUser??null,
            'phone' => $data['phone'],
            'phone_code' => $data['phone_code'],
            'country_code' => strtoupper($data['country_code']),
            'country' => $data['country'],
        ]);
    }

    public function register(Request $request)
    {

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        session()->forget('referral');

        $this->setNotificationPermission($user);

        if (basicControl()->registration_bonus){
            $this->sendRegistrationBonus($user);
        }


        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        if ($request->ajax()) {
            return route('user.home');
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    public function setNotificationPermission($user) :void
    {
        NotificationPermission::create([
            'notifyable_id' => $user->id,
            'notifyable_type' => User::class,
            'template_email_key' => config('notifypermission.email'),
            'template_in_app_key' => config('notifypermission.in_app'),
            'template_push_key' => config('notifypermission.push'),
            'template_sms_key' => config('notifypermission.sms'),

        ]);
    }


    public function sendRegistrationBonus($user):void
    {
        $user->profit_balance += basicControl()->registration_bonus_amount;
        $user->save();
        $transaction = BasicService::makeTransaction($user, basicControl()->registration_bonus_amount, 0, '+', null, 'Signup Bonus',$user,'profit');
        $user->transactional()->save($transaction);

        $params = [
            'transaction_id' => $transaction->trx_id,
            'bonus_amount' => currencyPosition(getAmount(basicControl()->registration_bonus_amount)),
            'username' => $user->username,

        ];
        $action = [
            'link' => '#',
            'icon' => 'fa fa-money-bill-alt text-white'
        ];

        $this->userPushNotification($user, 'REGISTRATION_BONUS', $params, $action);
        $this->userFirebasePushNotification($user, 'REGISTRATION_BONUS', $params, '#');
        $this->sendMailSms($user, 'REGISTRATION_BONUS', $params);

        if ($user->referral){
            $referralUser = $user->referral;
            $referralUser->profit_balance += basicControl()->referral_user_bonus_amount;
            $referralUser->save();

            $transaction = BasicService::makeTransaction($referralUser, basicControl()->referral_user_bonus_amount, 0, '+', null, 'Signup Referral Bonus',$referralUser,'profit');
            $trx = $transaction->trx_id;

            $bonus = new ReferralBonus();
            $bonus->from_user_id = $referralUser->id; // those person who Referred for investment
            $bonus->to_user_id = $user->id; // those person who signup
            $bonus->level = 1;
            $bonus->amount = getAmount(basicControl()->referral_user_bonus_amount);
            $bonus->commission_type = 'Registration Bonus';
            $bonus->trx_id = $trx;
            $bonus->remarks = 'Referral bonus From' . ' '.$user->username;
            $bonus->save();
            $bonus->transactional()->save($transaction);

            $params = [
                'transaction_id' => $trx,
                'amount' => currencyPosition(getAmount(basicControl()->referral_user_bonus_amount)),
                'bonus_from' => $user->username,
                'final_balance' => $referralUser->profit_balance,
                'level' => 1
            ];
            $action = [
                'link' => route('user.referral.bonus'),
                'icon' => 'fa fa-money-bill-alt text-white'
            ];
            $firebaseAction = route('user.referral.bonus');

            $this->userPushNotification($referralUser, 'REFERRAL_BONUS', $params, $action);
            $this->userFirebasePushNotification($referralUser, 'REFERRAL_BONUS', $params, $firebaseAction);
            $this->sendMailSms($referralUser, 'REFERRAL_BONUS', $params);
        }


    }

    protected function registered(Request $request, $user)
    {
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

        $msg = [
            'username' => $user->username,
        ];
        $action = [
            "link" => '#',
            "icon" => "fa fa-money-bill-alt"
        ];
        $this->userPushNotification($user,'NEW_USER_REGISTRATION',$msg,$action);
        $this->userFirebasePushNotification($user,'NEW_USER_REGISTRATION',$msg,'#');
        $this->sendMailSms($user,'NEW_USER_REGISTRATION',$msg);

    }

    protected function guard()
    {
        return Auth::guard();
    }

}
