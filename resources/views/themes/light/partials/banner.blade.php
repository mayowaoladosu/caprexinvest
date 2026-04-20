@if(!request()->is('/') && !request()->is('home-02'))
    <!-- banner section -->
    @if(!request()->is('/') && !request()->is('home-02'))
    <!-- banner section -->
    @if (isset($pageSeo) && $pageSeo)
        <!-- common banner -->
        <section class="common-banner">
            <div class="bg-layer" style="background: url({{$pageSeo['breadcrumb_image']}});"></div>
            <div class="container">
                <div class="common-banner-content">
                    <h3>{!! $pageSeo['page_title']??'' !!}</h3>
                    <div class="breadcrumb">
                        <ul>
                            <li class="breadcrumb-item active"><a href="{{route('page')}}">@lang('Home')</a></li>
                            <li class="breadcrumb-item"><i class="fa-sharp fa-light fa-angle-right"></i> {!! $pageSeo['page_title']??'' !!}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- common banner -->
    @endif
@endif

@endif
