<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}" rel="icon">


    <title>{{trans('Dashboard')}} | @lang(basicControl()->site_title) </title>

    <meta name="csrf-token" content="{{csrf_token()}}">

    <link rel="stylesheet" href="{{ asset($themeTrue . 'css/all.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset($themeTrue . 'css/flatpickr-min.css') }}"/>
    <link rel="stylesheet" href="{{ asset($themeTrue . 'css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset($themeTrue . 'css/select2.min.css') }}"/>
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset($themeTrue . 'css/user-style.css') }}"/>
    <!-- owl carousel -->
    <link rel="stylesheet" href="{{asset($themeTrue.'css/owl.carousel.min.css')}}"/>
    <link rel="stylesheet" href="{{asset($themeTrue.'css/owl.theme.default.min.css')}}"/>
    <link rel="stylesheet" href="{{asset($themeTrue.'css/apexcharts.css')}}"/>
    <link href="{{asset($themeTrue.'css/fancy-box-carusol.css')}}" rel="stylesheet">
    @stack('css-lib')
    @stack('style')
    @laravelPWA
</head>
<body onload="preloaderFunction()" >


<div id="preloader">
    <div class="load">
        <hr/>
        <hr/>
        <hr/>
        <hr/>
    </div>
</div>


<main id="main" class="main">
    @yield('content')
</main>


<!-- bootstrap -->
<script src="{{asset($themeTrue.'js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset($themeTrue.'js/flatpickr-min.js')}}"></script>
<!-- jquery cdn -->
<script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>

<script src="{{asset($themeTrue.'js/apexcharts.min.js')}}"></script>
<script src="{{ asset('assets/global/js/Chart.min.js') }}"></script>
<script src="{{asset($themeTrue.'js/user-script.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/select2.min.js') }}"></script>
<script src="{{asset($themeTrue.'js/owl.carousel.min.js')}}"></script>
<script src="{{asset($themeTrue.'js/waypoints.min.js')}}"></script>
<script src="{{ asset('assets/global/js/notiflix-aio-3.2.6.min.js') }}"></script>
<script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>
<script src="{{ asset('assets/global/js/vue.min.js') }}"></script>
<script src="{{ asset('assets/global/js/axios.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/jquery.uploadPreview.min.js') }}"></script>
<script src="{{asset($themeTrue.'js/custom_share.js')}}"></script>
@stack('js-lib')
@stack('script')
@include('plugins')

</body>
</html>





