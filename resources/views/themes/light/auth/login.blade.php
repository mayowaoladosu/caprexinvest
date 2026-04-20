@extends($theme . 'layouts.login')
@section('title', trans('Login'))
@section('content')

    <!-- sign-up -->
    <section class="sign-in">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="sign-in-image-cotainer">
                        <div class="sign-in-logo">
                            <a href="{{ route('page') }}"><img src="{{ logo(true) }}" alt="image"></a>
                        </div>
                        <div class="sign-in-left-btn">
                            <a href="{{ $login_section['single']['media']->button_url ?? '' }}"
                                class="btn-1">{{ $login_section['single']['button_name'] ?? '' }} <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <div class="sign-in-image">
                            <img src="{{ isset($login_section['single']['media']->image) ? getFile($login_section['single']['media']->image->driver, $login_section['single']['media']->image->path) : '' }}"
                                alt="image">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="sign-in-container">
                        <div class="sign-in-container-inner">
                            <div class="sign-in-title">
                                <h6>@lang($login_section['single']['title'] ?? '')</h6>
                                <h3>@lang($login_section['single']['sub_title'] ?? '')</h3>
                                <div class="signup-account">
                                    <p>@lang('Do not have an account')? <a href="{{ route('register') }}"> @lang('Sign Up')</a></p>
                                </div>
                            </div>
                            <div class="sign-in-form">
                                <form action="{{ route('login') }}" method="post">
                                    @csrf
                                    <div class="sign-in-form-group">
                                        <label>@lang('Username')</label>
                                        <input type="text" name="username"
                                            value="{{ old('username', config('demo.IS_DEMO') ? request()->username ?? 'demouser' : '') }}"
                                            class="sign-in-input" placeholder="@lang('Enter your username or email')">
                                        @error('username')
                                            <span class="invalid-feedback d-block"
                                                role="alert">{{ $message }}<strong></strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="sign-in-form-group">
                                        <label>@lang('Password')</label>
                                        <div class="password-box">
                                            <input type="password" name="password"
                                                value="{{ old('password', config('demo.IS_DEMO') ? request()->password ?? 'demouser' : '') }}"
                                                class="sign-in-input password" placeholder="@lang('Password')">
                                            <i class="password-icon fa-regular fa-eye"></i>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">{{ $message }} </span>
                                        @enderror
                                    </div>
                                    @if ($basicControl->manual_recaptcha === 1 && $basicControl->manual_recaptcha_user_login === 1)
                                        <div class="sign-in-form-group">
                                            <label>@lang('Captcha')</label>
                                            <input type="text" name="captcha" id="captcha" class="sign-in-input"
                                                placeholder="@lang('Enter captcha')">
                                            @error('captcha')
                                                <span class="invalid-feedback d-block"
                                                    role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mt-4">
                                            <div class="input-group input-group-merge manualRecaptchaImage"
                                                data-hs-validation-validate-class>
                                                <img src="{{ route('captcha') . '?rand=' . rand() }}" id='captcha_image'>
                                                <a class="input-group-append input-group-text manualRecaptchaIcon"
                                                    href='javascript: refreshCaptcha();'>
                                                    <i class="fa-solid fa-rotate-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if (basicControl()->google_reCaptcha_user_login && basicControl()->google_recaptcha)
                                        <div class="row mt-4">
                                            <div class="g-recaptcha @error('g-recaptcha-response') is-invalid @enderror"
                                                data-sitekey="{{ $siteKey }}"></div>
                                            @error('g-recaptcha-response')
                                                <span class="invalid-feedback d-block text-danger" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="rember">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label>@lang('Remember me')</label>
                                        </div>
                                        <div class="form-check">
                                            <a href="{{ route('password.request') }}">@lang('Forget Password')?</a>
                                        </div>
                                    </div>
                                    <div class="sign-in-btn">
                                        <button type="submit" class="btn-1">@lang('Sign In')</button>
                                    </div>
                                </form>
                                <div class="media-login">
                                    <div class="media-login-border">
                                        <h5>@lang('OR')</h5>
                                    </div>
                                    <ul>
                                        @if (config('socialite.facebook_status'))
                                            <li><a href="{{ route('socialiteLogin', 'facebook') }}"><img
                                                        src="{{ asset('assets/admin/facebook.png') }}" alt="icon"> @lang('Facebook')</a>
                                            </li>
                                        @endif
                                        @if (config('socialite.google_status'))
                                            <li><a href="{{ route('socialiteLogin', 'google') }}"><img
                                                        src="{{ asset('assets/admin/google.png') }}" alt="icon"> @lang('Google')</a>
                                            </li>
                                        @endif
                                        @if (config('socialite.github_status'))
                                            <li><a href="{{ route('socialiteLogin', 'github') }}"><img
                                                        src="{{ asset('assets/admin/github.png') }}" alt="icon"> @lang('Github')</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- sign-up -->
@endsection

@push('js-lib')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush


@push('extra-js')
    <script>
        'use strict';

        function refreshCaptcha() {
            let img = document.images['captcha_image'];
            img.src = img.src.substring(
                0, img.src.lastIndexOf("?")
            ) + "?rand=" + Math.random() * 1000;
        }
    </script>
@endpush

@push('style')
    <style>
        @media(max-width: 991px) {
            .footer-copyright {
                margin-bottom: 0 !important;
            }
        }
    </style>
@endpush