<!-- header -->
<header class="main-header header-style-two">

    <!-- Header Lower -->
    <div class="header-lower">
        <div class="container">
            <div class="inner-container d-flex align-items-center justify-content-between">
                <div class="header-left-column">
                    <div class="logo-box">
                        <div class="logo"><a href="{{route('page')}}"><img src="{{logo()}}" alt="@lang('logo')"></a></div>
                    </div>
                </div>
                <div class="header-right-column d-flex  align-items-center">
                    <div class="nav-outer">
                        <div class="mobile-nav-toggler"><img src="{{asset(template(true).'images/icons/icon-bar-two.png')}}"
                                                             alt="icon"></div>
                        <nav class="main-menu navbar-expand-md navbar-light">
                            <div class="collapse navbar-collapse show clearfix" id="navbarSupportedContent">
                                <ul class="navigation">
                                    {!! renderHeaderMenu(getHeaderMenuData()) !!}
                                    @if(basicControl()->trading_enabled)
                                        <li><a href="{{ url('/#live-market') }}">@lang('Trading')</a></li>
                                    @endif
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="header-right-btn-area">
                        <div class="header-user">
                            <a href="{{route('login')}}" class="cart-icon"><i class="fa-light fa-user"></i></a>
                        </div>
                        @if(hasEcommerce())
                            <div class="header-wishlist">
                                <a href="{{route('user.wishlist')}}" class="cart-icon"><i class="fa-light fa-heart"></i></a>
                                <div class="cart-badge wishlist-badge">{{$countUserWishlist}}</div>
                            </div>
                            <div class="header-cart">
                                <a href="" onclick="event.preventDefault()" class="cart-icon cart-toggler"><i
                                        class="fa-light fa-cart-shopping"></i></a>
                                <div class="cart-badge totalCartItems"> {{count(session('cart')??[])}} </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Lower -->

    <!-- sticky header -->
    <div class="sticky-header">
        <div class="header-upper">
            <div class="container">
                <div class="inner-container d-flex align-items-center justify-content-between">
                    <div class="left-column d-flex align-items-center">
                        <div class="logo-box">
                            <div class="logo"><a href="{{route('page')}}"><img src="{{logo()}}" alt="@lang('logo')"></a></div>
                        </div>
                    </div>

                    <div class="header-right-column d-flex align-items-center">
                        <div class="nav-outer gap-5 d-flex align-items-center">
                            <div class="mobile-nav-toggler"><img src="{{asset(template(true).'images/icons/icon-bar.png')}}"
                                                                 alt="icon"></div>
                            <nav class="main-menu navbar-expand-md navbar-light"></nav>
                        </div>
                        <div class="header-right-btn-area">

                            <div class="header-user">
                                <a href="{{route('login')}}" class="cart-icon"><i class="fa-light fa-user"></i></a>
                            </div>
                            @if(hasEcommerce())
                                <div class="header-wishlist">
                                    <a href="{{route('user.wishlist')}}" class="cart-icon"><i
                                            class="fa-light fa-heart"></i></a>
                                    <div class="cart-badge wishlist-badge">{{$countUserWishlist}}</div>
                                </div>
                                <div class="header-cart">
                                    <a href="" onclick="event.preventDefault()" class="cart-icon cart-toggler"><i
                                            class="fa-light fa-cart-shopping"></i></a>
                                    <div class="cart-badge totalCartItems">{{count(session('cart')??[])}}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sticky header -->

    <!-- mobile menu -->
    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <div class="close-btn"><span class="fal fa-times"></span></div>

        <nav class="menu-box">
            <div class="nav-logo"><a href="{{route('page')}}"><img src="{{logo()}}" alt="@lang('logo')"></a></div>
            <div class="menu-outer">
                <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header--></div>
            <!--Social Links-->
            <div class="social-media">
                <ul>
                    @foreach(collect($footer_section['multiple'])->toArray() as $item)
                        <li><a href="{{$item['media']->link??''}}"><i class="{{$item['media']->icon??''}}"></i></a></li>
                    @endforeach
                </ul>
            </div>
        </nav>
    </div>

    <!-- Bottom Mobile Menu -->
    <ul class="nav bottom-nav fixed-bottom d-lg-none">
        <li class="nav-item">
            <a class="nav-link mobile-nav-toggler" href="#"><i class="fa-light fa-list"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('user.plans')}}"><i class="fa-light fa-planet-ringed"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{route('page')}}"><i class="fa-light fa-house"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ basicControl()->trading_enabled ? url('/#live-market') : route('user.transaction.list') }}"><i class="fal fa-chart-line"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('login')}}"><i class="fa-light fa-user"></i></a>
        </li>
    </ul>
    <!-- Bottom Mobile Menu -->

</header>
<!-- header -->
