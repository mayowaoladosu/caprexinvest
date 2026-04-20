@extends(template().'layouts.app')
@section('title','Not Found')
@section('content')
    <!-- error -->
    <section class="error pt_120 pb_120">
        <div class="container">
            <div class="error-container text-center">
                <div class="error-image">
                    <img src="{{asset(template(true).'images/error/error.png')}}" alt="error">
                </div>
                <div class="error-content">
                    <h3 style="
                            font-size: 48px;
                            font-weight: 600;
                            line-height: 56px;
                            margin-bottom: 16px;">
                       @lang('Page Not Found-Keep Exploring')!
                    </h3>
                    <p style="
                            max-width: 719px;
                            width: 100%;
                            margin: 0 auto;">
                        @lang("It seems the page you're looking doesn't exist.Let,s guide you back to the right path explore our services or contact us for assistance")
                    </p>
                    <a href="{{route('page')}}" class="btn-1 mt_30">@lang('Back to Home') <i class="icon-arrow-1 fs_14"></i></a>
                </div>
            </div>
        </div>
    </section>
    <!-- error -->

@endsection
