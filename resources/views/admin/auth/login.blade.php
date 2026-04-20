@extends('admin.layouts.login')
@section('page_title', __('Admin Login'))
@section('content')
    <div class="row">
        <div
            class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center min-vh-lg-100 position-relative bg-light px-0">
            <!-- Logo & Language -->
            <div class="login_page_logo">
                <div class="d-none d-lg-flex justify-content-between">
                    <a href="javascript:void(0)">
                        <img class="w-100"
                             src="{{ getFile($basicControl->admin_logo_driver, $basicControl->admin_logo, true) }}"
                             alt="Image Description" data-hs-theme-appearance="default"
                             style="min-width: 7rem; max-width: 7rem;">
                        <img class="w-100"
                             src="{{ getFile($basicControl->admin_dark_mode_logo_driver, $basicControl->admin_dark_mode_logo, true) }}"
                             alt="Image Description" data-hs-theme-appearance="dark"
                             style="min-width: 7rem; max-width: 7rem;">
                    </a>
                </div>
            </div>
            <!-- End Logo & Language -->

            <div class="image-box">
                <img class="login-image" src="{{ asset('assets/admin/img/login.jpg') }}" alt="Image Description"
                     data-hs-theme-appearance="default">
                <img class="login-image" src="{{ asset('assets/admin/img/login.jpg') }}" alt="Image Description"
                     data-hs-theme-appearance="dark">
            </div>
        </div>
        <!-- End Col -->

        <div class="col-lg-6 d-flex justify-content-center align-items-center min-vh-lg-100">
            <div class="w-100 content-space-t-4 content-space-t-lg-2 content-space-b-1" style="max-width: 25rem;">

                @if (Session::has('error'))
                    <div data-hs-theme-appearance="default" class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="fw-semibold">{{ Session::get('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div  data-hs-theme-appearance="dark" class="alert alert-danger alert-dismissible fade show" role="alert" bis_skin_checked="1">
                        <span class="fw-semibold text-white">{{ Session::get('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                <!-- Form -->
                <form method="post" action="{{ route('admin.login.submit') }}" class="js-validate needs-validation"
                      novalidate>
                    @csrf

                    <!-- Form -->
                    <div class="mb-4">
                        <label class="form-label" for="signinSrEmail">Your email</label>
                        <input type="email" class="form-control form-control-lg" name="username"
                               {{ old('username', config('demo.IS_DEMO') ? request()->username ?? 'admin' : '') }}
                               id="signinSrEmail" tabindex="1" placeholder="@lang('Enter Email or Username')" aria-label="@lang('Enter Email or Username')"
                               required>


                        @error('username')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                        @error('email')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror


                    </div>
                    <!-- End Form -->



                    <!-- Form -->
                    <div class="mb-4">
                        <label class="form-label w-100" for="signupSrPassword" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                                <span>@lang('Password')</span>
                                <a class="form-label-link mb-0"
                                   href="{{ route('admin.password.request') }}">@lang('Forgot Password')?</a>
                            </span>
                        </label>

                        <div class="input-group input-group-merge" data-hs-validation-validate-class>
                            <input type="password" class="js-toggle-password form-control form-control-lg" name="password"
                                   id="signupSrPassword" placeholder="@lang('Enter Your Password')" aria-label="@lang('Enter Your Password')"
                                   required minlength="8"
                                   data-hs-toggle-password-options='{
                           "target": "#changePassTarget",
                           "defaultClass": "bi-eye-slash",
                           "showClass": "bi-eye",
                           "classChangeTarget": "#changePassIcon"
                         }'>
                            <a id="changePassTarget" class="input-group-append input-group-text" href="javascript:;">
                                <i id="changePassIcon" class="bi-eye"></i>
                            </a>
                        </div>

                        @error('password')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- End Form -->

                    @if ($basicControl->google_recaptcha && $basicControl->google_reCaptcha_admin_login)
                        <div class="form-group mb-2">
                            <div class="g-recaptcha @error('g-recaptcha-response') is-invalid @enderror"
                                 data-sitekey="{{ $siteKey }}"></div>
                            @error('g-recaptcha-response')
                            <div class="text-danger">@lang($message)</div>
                            @enderror
                        </div>
                    @endif

                    @if (basicControl()->manual_recaptcha && basicControl()->manual_recaptcha_admin_login)
                        <div class="mb-4">
                            <label class="form-label" for="captcha">@lang('Captcha Code')</label>
                            <input type="text" tabindex="2"
                                   class="form-control form-control-lg @error('captcha') is-invalid @enderror" name="captcha"
                                   id="captcha" autocomplete="off" placeholder="Enter Captcha" required>
                            @error('captcha')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="input-group input-group-merge" data-hs-validation-validate-class>
                                <img src="{{ route('captcha') . '?rand=' . rand() }}" id='captcha_image'>
                                <a class="input-group-append input-group-text" href='javascript: refreshCaptcha();'>
                                    <i class="bi-arrow-repeat fs-1 text-primary"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Form Check -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember_me" value=""
                               id="termsCheckbox" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="termsCheckbox">
                            @lang('Remember me')
                        </label>
                    </div>
                    <!-- End Form Check -->

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">@lang('Sign in')</button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
        <!-- End Col -->
    </div>




@endsection

@push('script')
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
