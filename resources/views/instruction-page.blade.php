@extends(template().'layouts.error')
@section('title','Instruction Page')
@section('content')
    <section class="error-section">
        <div class="container">
            <div class="row g-5 align-items-center justify-content-center">

                @if(auth()->guard('admin')->check() == false)
                    <div class="col-sm-6">
                        <div class="error-thum">
                            <img src="{{ asset(template(true) . 'img/error/error2.png')}}" alt="...">
                        </div>
                    </div>
                @endif
                <div class="col-sm-6">
                    <div class="error-content">
                        @php
                            $languageName = config('languages.langCode')[app()->currentLocale()] ?? 'your selected language';
                        @endphp

                        <div class="error-info font-30">
                            @lang('Coming Soon Content in') {{ $languageName }}
                        </div>
                        <p class="mt-3">
                            @lang('If content is not yet available in') <span class="text-gradient">{{ $languageName }}</span>,
                            @lang('our team is preparing localized pages for that audience. Thank you for your patience while we complete the update.')
                        </p>

                        @if(auth()->guard('admin')->check())
                            <div class="btn-area">
                                <a href="{{ route('admin.page.index', basicControl()->theme??'light') }}"
                                   class="cmn-btn">@lang('Go To Settings')</a>
                            </div>
                        @endif
                    </div>
                </div>


                @if(auth()->guard('admin')->check())
                    <div class="col-sm-12">
                        <div class="instruction-thumbs">
                            <img src="{{ asset('assets/admin/img/content-add-instruction.png')}}" alt="...">
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </section>
@endsection
