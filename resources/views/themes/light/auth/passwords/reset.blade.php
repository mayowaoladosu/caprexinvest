@extends($theme.'layouts.app')

@section('content')


<section class="sign-in">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="sign-in-container">
                    <div class="sign-in-container-inner">
                        <div class="sign-in-title">
                            <h4>@lang('Reset Password')!</h4>
                            <p>@lang('Regain access with your seamless and secure account retrieval process in just a few clicks')</p>
                        </div>
                        <div class="sign-in-form">
                            <form action="{{ route('password.update') }}" method="post">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="sign-in-form-group">
                                    <input type="email" name="email" value="{{ $email ?? old('email') }}" class="sign-in-input"
                                           placeholder="@lang('Email address')" required autofocus autocomplete="off">
                                    @error('email')
                                    <span class="invalid-feedback d-block"
                                          role="alert">{{ $message }}<strong></strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="sign-in-form-group">
                                    <input type="password" name="password" value="{{old('password') }}" class="sign-in-input"
                                           placeholder="@lang('Password')" required autocomplete="off">
                                    @error('password')
                                    <span class="invalid-feedback d-block"
                                          role="alert">{{ $message }}<strong></strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="sign-in-form-group">
                                    <input type="password" name="password_confirmation" value="{{old('password') }}" class="sign-in-input"
                                           placeholder="@lang('Confirm Password')" required autocomplete="off">
                                </div>
                                <div class="sign-in-btn">
                                    <button type="submit" class="btn-1">@lang('Reset Password')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
