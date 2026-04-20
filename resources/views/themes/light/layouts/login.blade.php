<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta content="{{ isset($pageSeo['meta_description']) ? $pageSeo['meta_description'] : '' }}" name="description">
    <meta content="{{ is_array(@$pageSeo['meta_keywords']) ? implode(', ', @$pageSeo['meta_keywords']) : @$pageSeo['meta_keywords'] }}"
          name="keywords">
    <meta name="theme-color" content="{{ basicControl()->primary_color }}">
    <meta name="author" content="{{basicControl()->site_title}}">
    <meta name="robots" content="{{ isset($pageSeo['meta_robots']) ? $pageSeo['meta_robots'] : '' }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ isset(basicControl()->site_title) ? basicControl()->site_title : '' }}">
    <meta property="og:title" content="{{ isset($pageSeo['meta_title']) ? $pageSeo['meta_title'] : '' }}">
    <meta property="og:description" content="{{ isset($pageSeo['og_description']) ? $pageSeo['og_description'] : '' }}">
    <meta property="og:image" content="{{ getFile(@$pageSeo['meta_image_driver'], @$pageSeo['meta_image']) }}">

    <meta name="twitter:card" content="{{ isset($pageSeo['meta_title']) ? $pageSeo['meta_title'] : '' }}">
    <meta name="twitter:title" content="{{ isset($pageSeo['meta_title']) ? $pageSeo['meta_title'] : '' }}">
    <meta name="twitter:description" content="{{ isset($pageSeo['meta_description']) ? $pageSeo['meta_description'] : '' }}">
    <meta name="twitter:image" content="{{ getFile(@$pageSeo['meta_image_driver'], @$pageSeo['meta_image']) }}">
    <!-- Favicons -->
    <link href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}" rel="icon">
    <title> {{basicControl()->site_title}}
        @if(isset($pageSeo['page_title']))
            | {{str_replace(basicControl()->site_title, ' ',$pageSeo['page_title'])}}
        @else
            | @yield('title')
        @endif
    </title>

    <!-- Stylesheets -->
    <link href="{{asset(template(true).'css/style.css')}}" rel="stylesheet">
    @stack('css-lib')

</head>

<body>
<div class="page-wrapper">

    <!-- preloader start -->
    <div class="loader-wrap">
        <div class="preloader">
            <div class="preloader-close">x</div>
            <div id="handle-preloader" class="handle-preloader">
                <div class="animation-preloader">
                    <div class="loader-image">
                        <img src="{{asset(template(true).'images/loader.gif')}}" alt="loader">
                    </div>
                    <div class="txt-loading">
                        @foreach(loader() as $charecter)
                            @if($charecter == " ")
                                <span data-text-preloader="{{$charecter}}" class="letters-loading ml_15">
                               {{$charecter}}
                                </span>
                            @else
                                <span data-text-preloader="{{$charecter}}" class="letters-loading">
                               {{$charecter}}
                                </span>
                            @endif

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- preloader end -->

    @yield('content')
    @include(template().'partials.footer')
</div>



<!--Scroll to top start -->
<div class="scroll-to-top">
    <div>
        <div class="scroll-top-inner">
            <div class="scroll-bar">
                <div class="bar-inner"></div>
            </div>
            <div class="scroll-bar-text">@lang('Go To Top')</div>
        </div>
    </div>
</div>
<!-- Scroll to top end -->

<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>
<script src="{{asset(template(true).'js/bootstrap.min.js')}}"></script>
<script src="{{asset(template(true).'js/select2.min.js')}}"></script>
<script src="{{asset(template(true).'js/jquery.nice-select.min.js')}}"></script>
<script src="{{asset(template(true).'js/appear.js')}}"></script>
<script src="{{asset(template(true).'js/wow.js')}}"></script>
<script src="{{asset(template(true).'js/owl.js')}}"></script>
<script src="{{asset(template(true).'js/TweenMax.min.js')}}"></script>
<script src="{{asset(template(true).'js/odometer.min.js')}}"></script>
<script src="{{asset(template(true).'js/swiper.min.js')}}"></script>
<script src="{{asset(template(true).'js/parallax-scroll.js')}}"></script>
<script src="{{asset(template(true).'js/jarallax.min.js')}}"></script>
<script src="{{asset(template(true).'js/jquery.paroller.min.js')}}"></script>
<script src="{{asset(template(true).'js/jquery.magnific-popup.min.js')}}"></script>
<script src="{{asset(template(true).'js/isotope.js')}}"></script>
<script src="{{asset(template(true).'js/flatpickr-min.js')}}"></script>
<script src="{{asset(template(true).'js/socialSharing.js')}}"></script>
<script src="{{asset(template(true).'js/script.js')}}"></script>
<script src="{{ asset('assets/global/js/notiflix-aio-3.2.6.min.js') }}"></script>
@include('plugins')




<script>
    "use strict";
    var root = document.querySelector(':root');
    root.style.setProperty('--primary', '{{basicControl()->primary_color}}');
    root.style.setProperty('--secondary', '{{basicControl()->secondary_color}}');
</script>


@stack('js-lib')

@stack('script')

@if (session()->has('success'))
    <script>
        Notiflix.Notify.success("@lang(session('success'))");
    </script>
@endif

@if (session()->has('error'))
    <script>
        Notiflix.Notify.failure("@lang(session('error'))");
    </script>
@endif

@if (session()->has('warning'))
    <script>
        Notiflix.Notify.warning("@lang(session('warning'))");
    </script>
@endif

</body>

</html>


