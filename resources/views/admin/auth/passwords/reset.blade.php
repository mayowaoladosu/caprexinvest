@extends('admin.layouts.login')
@section('page_title', 'Admin | Reset Password')
@section('content')




    <div class="row">
        <div
            class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center min-vh-lg-100 position-relative bg-light px-0">
            <!-- Logo & Language -->
            <div class="login_page_logo">
                <div class="d-none d-lg-flex justify-content-between">
                    <a href="javascript:void(0)">
                        <img class="w-100" src="{{ getFile($basicControl->admin_logo_driver, $basicControl->admin_logo) }}"
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
            <div class="w-100  p-3 content-space-t-4 content-space-t-lg-2 content-space-b-1" style="max-width: 25rem;">
                @if (Session::has('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span class="fw-semibold">@lang('We will send a link to reset your password.')</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif



                <form method="post" action="{{ route('admin.password.reset.update') }}"
                      class="js-validate needs-validation" novalidate>
                    @csrf
                    <div class="text-center">
                        <div class="mb-5">
                            <h1 class="display-5">@lang('Reset password')</h1>
                            <p>@lang('Regain access with your seamless and secure account retrieval process in just a few clicks')</p>

                        </div>
                    </div>

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-4">
                        <label class="form-label" for="resetPasswordSrEmail" tabindex="0">@lang('Your email')</label>
                        <input type="email" class="form-control form-control-lg" value="{{ $email }}"
                               name="email" id="resetPasswordSrEmail" tabindex="1" placeholder="Enter your email address"
                               aria-label="Enter your email address" required>
                        <span class="invalid-feedback">@lang('Please enter a valid email address.')</span>

                        @error('email')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="resetPasswordSrEmail" tabindex="0">@lang('Password')</label>

                        <div class="input-group input-group-merge" data-hs-validation-validate-class>
                            <input type="password"
                                   class="js-toggle-password form-control form-control-lg @error('password') is-invalid @enderror"
                                   name="password" id="signupSrPassword" placeholder="Enter Password"
                                   data-hs-toggle-password-options='
                               {
                                "target": "#changePassTarget",
                                "defaultClass": "bi-eye-slash",
                                "showClass": "bi-eye",
                                "classChangeTarget": "#changePassIcon"
                                }'>
                            <a id="changePassTarget" class="input-group-append input-group-text" href="javascript:void(0)">
                                <i id="changePassIcon" class="bi-eye"></i>
                            </a>
                        </div>
                        @error('password')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>



                    <div class="mb-4">
                        <label class="form-label" for="resetPasswordSrEmail" tabindex="0">@lang('Confirm Password')</label>

                        <div class="input-group input-group-merge" data-hs-validation-validate-class>
                            <input type="password"
                                   class="js-toggle-password form-control form-control-lg @error('password') is-invalid @enderror"
                                   name="password_confirmation" id="ConfirmPass" placeholder="Enter Confirm Password"
                                   data-hs-toggle-password-options='
                               {
                                "target": "#changeConfirmPassTarget",
                                "defaultClass": "bi-eye-slash",
                                "showClass": "bi-eye",
                                "classChangeTarget": "#changeConfirmPassIcon"
                                }'>
                            <a id="changeConfirmPassTarget" class="input-group-append input-group-text"
                               href="javascript:void(0)">
                                <i id="changeConfirmPassIcon" class="bi-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">@lang('Submit')</button>
                        <div class="text-center">
                            <a class="btn btn-link" href="{{ route('admin.login') }}">
                                <i class="bi-chevron-left"></i> @lang('Back to Sign in')

                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Col -->
    </div>
@endsection
