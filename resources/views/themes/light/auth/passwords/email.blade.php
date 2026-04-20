@extends($theme.'layouts.app')
@section('content')

<section class="sign-in">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="sign-in-container">
                    <div class="sign-in-container-inner">
                        <div class="sign-in-title">
                            <h4>@lang('Recover Password')!</h4>
                            <p>@lang('Regain access with your seamless and secure account retrieval process in just a few clicks')!</p>
                        </div>
                        <div class="sign-in-form">
                            <form action="{{ route('password.email') }}" method="post">
                                @csrf
                                <div class="sign-in-form-group">
                                    <input type="email" name="email" value="{{old('email')}}" class="sign-in-input"
                                           placeholder="@lang('Email address')" required>
                                    @error('email')
                                    <span class="invalid-feedback d-block"
                                          role="alert">{{ $message }}<strong></strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="sign-in-btn">
                                    <button type="submit" class="btn-1">@lang('Send Link')</button>
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
