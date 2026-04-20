<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}" rel="icon">


    <title>@yield('page_title') - {{ __(basicControl()->site_title) }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/hs-mega-menu.min.css') }}">
    <link rel="preload" href="{{ asset('assets/admin/css/theme.min.css') }}" data-hs-appearance="default"
          as="style">
    <link rel="preload" href="{{ asset('assets/admin/css/theme-dark.min.css') }}" data-hs-appearance="dark"
          as="style">

    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">

    @laravelPWA
    @stack('css-lib')

    @stack('style')
    <style data-hs-appearance-onload-styles>
        * {
            transition: unset !important;
        }

        body {
            opacity: 0;
        }
    </style>

    <script>
        window.hs_config = {
            "autopath": "@@autopath",
            "deleteLine": "hs-builder:delete",
            "deleteLine:build": "hs-builder:build-delete",
            "deleteLine:dist": "hs-builder:dist-delete",
            "previewMode": false,
            "startPath": "",
            "vars": {
                "themeFont": "https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap",
                "version": "?v=1.0"
            },
            "layoutBuilder": {
                "extend": {
                    "switcherSupport": true
                },
                "header": {
                    "layoutMode": "default",
                    "containerMode": "container-fluid"
                },
                "sidebarLayout": "default"
            },
            "themeAppearance": {
                "layoutSkin": "default",
                "sidebarSkin": "default",
                "styles": {
                    "colors": {
                        "primary": "#377dff",
                        "transparent": "transparent",
                        "white": "#fff",
                        "dark": "132144",
                        "gray": {
                            "100": "#f9fafc",
                            "900": "#1e2022"
                        }
                    },
                    "font": "Inter"
                }
            },
            "languageDirection": {
                "lang": "en"
            },
            "minifyCSSFiles": ["assets/css/theme.css", "assets/css/theme-dark.css"],
            "copyDependencies": {
                "dist": {
                    "*assets/js/theme-custom.js": ""
                },
                "build": {
                    "*assets/js/theme-custom.js": "",
                    "node_modules/bootstrap-icons/font/*fonts/**": "assets/css"
                }
            },
            "buildFolder": "",
            "replacePathsToCDN": {},
            "directoryNames": {
                "src": "./src",
                "dist": "./dist",
                "build": "./build"
            },
            "fileNames": {
                "dist": {
                    "js": "theme.min.js",
                    "css": "theme.min.css"
                },
                "build": {
                    "css": "theme.min.css",
                    "js": "theme.min.js",
                    "vendorCSS": "vendor.min.css",
                    "vendorJS": "vendor.min.js"
                }
            },
            "fileTypes": "jpg|png|svg|mp4|webm|ogv|json"
        }
    </script>
</head>

<body>
<script src="{{ asset('assets/admin/js/hs.theme-appearance.js') }}"></script>

<script src="{{ asset('assets/admin/js/hs-navbar-vertical-aside-mini-cache.js') }}"></script>

<!-- ========== HEADER ========== -->
<header id="header" class="navbar navbar-expand-lg navbar-bordered navbar-spacer-y-0 flex-lg-column">
    <div class="navbar-dark w-100 bg-dark py-2">
        <div class="container">
            <div class="navbar-nav-wrap">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('page') }}" aria-label="Front">
                    <img class="navbar-brand-logo" src="{{ logo(true) }}" alt="Logo">
                </a>
                <!-- End Logo -->

                <!-- Content Start -->
                <div class="navbar-nav-wrap-content-start">
                    <!-- Search Form -->
                    <div class="d-none d-lg-block">
                        <div class="dropdown ms-2">
                            <!-- Input Group -->
                            <div class="d-none d-lg-block">
                                <div
                                    class="input-group input-group-merge input-group-borderless input-group-hover-light navbar-input-group">
                                    <div class="input-group-prepend input-group-text">
                                        <i class="bi-search"></i>
                                    </div>

                                    <input type="search" class="js-form-search form-control global-search"
                                           placeholder="@lang('Search a menu')" aria-label="@lang('Search a menu')"
                                           data-hs-form-search-options='{
                                            "clearIcon": "#clearSearchResultsIcon",
                                            "dropMenuElement": "#searchDropdownMenu",
                                            "dropMenuOffset": 20,
                                            "toggleIconOnFocus": true,
                                            "activeClass": "focus"
                                            }'>
                                    <a class="input-group-append input-group-text" href="javascript:;">
                                        <i id="clearSearchResultsIcon" class="bi-x-lg" style="display: none;"></i>
                                    </a>
                                </div>
                            </div>

                            <button
                                class="js-form-search js-form-search-mobile-toggle btn btn-ghost-secondary btn-icon rounded-circle d-lg-none"
                                type="button"
                                data-hs-form-search-options='{
                           "clearIcon": "#clearSearchResultsIcon",
                           "dropMenuElement": "#searchDropdownMenu",
                           "dropMenuOffset": 20,
                           "toggleIconOnFocus": true,
                           "activeClass": "focus"
                         }'>
                                <i class="bi-search"></i>
                            </button>
                            <!-- End Input Group -->

                            <!-- Card Search Content -->
                            <div id="searchDropdownMenu"
                                 class="hs-form-search-menu-content dropdown-menu dropdown-menu-form-search navbar-dropdown-menu-borderless">
                                <div class="card">
                                    <!-- Body -->
                                    <div class="card-body-height">
                                        <div class="d-lg-none">
                                            <div class="input-group input-group-merge navbar-input-group mb-5">
                                                <div class="input-group-prepend input-group-text">
                                                    <i class="bi-search"></i>
                                                </div>

                                                <input type="search" class="form-control global-search"
                                                       placeholder="@lang('Search a menu')"
                                                       aria-label="@lang('Search a menu')">
                                                <a class="input-group-append input-group-text" href="javascript:;">
                                                    <i class="bi-x-lg"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <span class="dropdown-header">@lang('Result')</span>
                                        <div class="dropdown-divider"></div>
                                        <div class="content">

                                        </div>
                                    </div>
                                    <!-- End Body -->
                                </div>
                            </div>
                            <!-- End Card Search Content -->

                        </div>

                    </div>
                    <!-- End Search Form -->
                </div>
                <!-- End Content Start -->

                <!-- Content End -->
                <div class="navbar-nav-wrap-content-end">
                    <!-- Navbar -->
                    <ul class="navbar-nav">
                        @if(basicControl()->in_app_notification)
                            <li class="nav-item d-none d-md-inline-block" id="pushNotificationArea">
                                <!-- Notification -->
                                <div class="dropdown">
                                    <button type="button" class="btn btn-ghost-light btn-icon rounded-circle"
                                            id="navbarNotificationsDropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            data-bs-auto-close="outside" data-bs-dropdown-animation>
                                        <i class="bi-bell"></i>
                                        <span v-if="items.length > 0"
                                              class="btn-status btn-sm-status btn-status-danger"></span>
                                    </button>

                                    <div
                                        class="dropdown-menu dropdown-menu-end dropdown-card navbar-dropdown-menu navbar-dropdown-menu-borderless"
                                        aria-labelledby="navbarNotificationsDropdown" style="width: 25rem;">
                                        <div class="card">
                                            <!-- Header -->
                                            <div class="card-header card-header-content-between">
                                                <h4 class="card-title mb-0">@lang('Notifications')</h4>
                                            </div>
                                            <!-- End Header -->


                                            <!-- Body -->
                                            <div class="card-body-height">
                                                <!-- Tab Content -->
                                                <div class="tab-content" id="notificationTabContent">
                                                    <div class="tab-pane fade show active" id="notificationNavOne"
                                                         role="tabpanel" aria-labelledby="notificationNavOne-tab">
                                                        <!-- List Group -->
                                                        <ul class="list-group list-group-flush navbar-card-list-group">

                                                            <li class="list-group-item form-check-select"
                                                                v-for="(item, index) in items">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="d-flex ">
                                                                            <div
                                                                                class="avatar provider-notify d-flex align-items-center justify-content-center avatar-sm avatar-circle">
                                                                                <i class="bi-bell"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ms-n2">
                                                                        <p class="text-body fs-5">
                                                                            @{{ item.description.text }}</p>
                                                                        <small class="col-auto text-muted text-cap"
                                                                               v-cloak>@{{ item.formatted_date
                                                                            }}</small>
                                                                    </div>
                                                                </div>
                                                                <a class="stretched-link"
                                                                   :href="item.description.link"></a>
                                                            </li>
                                                        </ul>
                                                        <div class="text-center p-4" v-if="items.length < 1">
                                                            <img class="dataTables-image mb-3"
                                                                 src="{{ asset('assets/admin/img/oc-error.svg') }}"
                                                                 alt="Image Description"
                                                                 data-hs-theme-appearance="default">
                                                            <img class="dataTables-image mb-3"
                                                                 src="{{ asset('assets/admin/img/oc-error-light.svg') }}"
                                                                 alt="Image Description"
                                                                 data-hs-theme-appearance="dark">
                                                            <p class="mb-0">@lang('No Notifications Found')</p>
                                                        </div>
                                                        <!-- End List Group -->
                                                    </div>
                                                </div>
                                                <!-- End Tab Content -->
                                            </div>
                                            <!-- End Body -->

                                            <!-- Card Footer -->
                                            <a class="card-footer text-center" href="javascript:void(0)"
                                               v-if="items.length > 0" @click.prevent="readAll">
                                                @lang('Clear all notifications') <i class="bi-chevron-right"></i>
                                            </a>
                                            <!-- End Card Footer -->
                                        </div>
                                    </div>
                                </div>
                                <!-- End Notification -->
                            </li>

                        @endif
                        <li class="nav-item">
                            <!-- Style Switcher -->
                            <div class="dropdown ">
                                <button type="button" class="btn btn-ghost-light btn-icon rounded-circle"
                                        id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-dropdown-animation>

                                </button>

                                <div
                                    class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless"
                                    aria-labelledby="selectThemeDropdown">
                                    <a class="dropdown-item" href="javascript:void(0)" data-icon="bi-moon-stars"
                                       data-value="auto">
                                        <i class="bi-moon-stars me-2"></i>
                                        <span class="text-truncate"
                                              title="Auto (system default)">@lang('Auto (system default)')</span>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       data-icon="bi-brightness-high" data-value="default">
                                        <i class="bi-brightness-high me-2"></i>
                                        <span class="text-truncate"
                                              title="Default (light mode)">@lang('Default (light mode)')</span>
                                    </a>
                                    <a class="dropdown-item active" href="javascript:void(0)" data-icon="bi-moon"
                                       data-value="dark">
                                        <i class="bi-moon me-2"></i>
                                        <span class="text-truncate" title="Dark">@lang('Dark')</span>
                                    </a>
                                </div>
                            </div>

                            <!-- End Style Switcher -->
                        </li>

                        <li class="nav-item">
                            <!-- Account -->
                            <div class="dropdown">
                                <a class="navbar-dropdown-account-wrapper" href="javascript:;"
                                   id="accountNavbarDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                   data-bs-auto-close="outside" data-bs-dropdown-animation>
                                    <div class="avatar avatar-sm avatar-circle">
                                        <img class="avatar-img"
                                             src="{{ getFile(auth()->user()->image_driver, auth()->user()->image) }}"
                                             alt="Image Description">
                                        <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                    </div>
                                </a>

                                <div
                                    class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-account"
                                    aria-labelledby="accountNavbarDropdown" style="width: 20rem;">
                                    <div class="dropdown-item-text">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm avatar-circle">
                                                <img class="avatar-img"
                                                     src="{{ getFile(auth()->user()->image_driver, auth()->user()->image) }}"
                                                     alt="Image Description">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0">{{ auth()->user()->full_name }}</h5>
                                                <p class="card-text text-body">{{ auth()->user()->email }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item"
                                       href="{{ route('user.profile') }}">@lang('Profile &amp; account')</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item"
                                       href="{{ route('user.twostep.security') }}">@lang('2 FA Security')</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item"
                                       href="{{ route('user.show.user.kyc') }}">@lang('Verification Center')</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" id="installButton" onclick="event.preventDefault()"
                                       href="">@lang('Install PWA')</a>
                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        @lang('Sign out')
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                            <!-- End Account -->
                        </li>

                        <li class="nav-item">
                            <!-- Toggler -->
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#navbarDoubleLineContainerNavDropdown"
                                    aria-controls="navbarDoubleLineContainerNavDropdown" aria-expanded="false"
                                    aria-label="Toggle navigation">
                                    <span class="navbar-toggler-default">
                                        <i class="bi-list"></i>
                                    </span>
                                <span class="navbar-toggler-toggled">
                                        <i class="bi-x"></i>
                                    </span>
                            </button>
                            <!-- End Toggler -->
                        </li>
                    </ul>
                    <!-- End Navbar -->
                </div>
                <!-- End Content End -->
            </div>
        </div>
    </div>

    <div class="container">
        <nav class="js-mega-menu flex-grow-1">
            <!-- Collapse -->
            <div class="collapse navbar-collapse" id="navbarDoubleLineContainerNavDropdown">
                <ul class="navbar-nav" id="navbarVerticalMenu">
                    <!-- Dashboards -->
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive(['user.dashboard']) }}"
                           href="{{ route('user.dashboard') }}">
                            <i class="bi-house-door dropdown-item-icon"></i> @lang('Dashboard')
                        </a>
                    </li>
                    <!-- End Dashboards -->


                    @if(basicControl()->invest_status)
                        <li class="hs-has-sub-menu nav-item">
                            <a class="hs-mega-menu-invoker nav-link dropdown-toggle  {{ menuActive(['user.plans','user.plan.investment']) }}"
                               href="javascript:void(0)">
                                <i class="fal fa-cube dropdown-item-icon"></i>
                                @lang('Investment')
                            </a>
                            <div
                                class="hs-sub-menu dropdown-menu navbar-dropdown-menu-borderless hs-sub-menu-desktop-lg"
                                aria-labelledby="appsMegaMenu" style="min-width: 14rem; display: none;">

                                <a class="dropdown-item {{ menuActive(['user.plans']) }}"
                                   href="{{ route('user.plans') }}">@lang('Plan')</a>
                                <a class="dropdown-item {{ menuActive(['user.plan.investment']) }}"
                                   href="{{ route('user.plan.investment') }}">@lang('History')</a>
                            </div>
                        </li>

                    @endif
                    @if(basicControl()->trading_enabled)
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive(['user.trading.index']) }}"
                               href="{{ route('user.trading.index') }}">
                                <i class="fal fa-chart-line dropdown-item-icon"></i>
                                @lang('Trading')
                            </a>
                        </li>
                    @endif
                    @if(hasEcommerce())
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive(['user.orders','user.orderItems']) }}"
                               href="{{ route('user.orders') }}">
                                <i class="fal fa-shopping-basket dropdown-item-icon"></i>
                                @lang('Orders')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive(['user.wishlist']) }}"
                               href="{{ route('user.wishlist') }}">
                                <i class="fal fa-heart dropdown-item-icon" aria-hidden="true"></i>
                                @lang('Wishlist')
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ menuActive(['user.add.fund']) }}"
                           href="{{ route('user.add.fund') }}">
                            <i class="fal fa-wallet dropdown-item-icon"></i>
                            @lang('Deposit')
                        </a>
                    </li>

                    <li class="hs-has-sub-menu nav-item">
                        <a class="hs-mega-menu-invoker nav-link dropdown-toggle  {{ menuActive(['user.payout','user.payout.paystack','user.payout.flutterwave','user.payout.confirm','user.payout.index']) }}"
                           href="javascript:void(0)">
                            <i class="fal fa-hand-holding-dollar dropdown-item-icon"></i>
                            @lang('Withdraw')
                        </a>
                        <div class="hs-sub-menu dropdown-menu navbar-dropdown-menu-borderless hs-sub-menu-desktop-lg"
                             aria-labelledby="appsMegaMenu" style="min-width: 14rem; display: none;">

                            <a class="dropdown-item {{ menuActive(['user.payout']) }}"
                               href="{{ route('user.payout') }}">@lang('Withdraw')</a>

                            <a class="dropdown-item {{ menuActive(['user.payout.index']) }}"
                               href="{{ route('user.payout.index') }}">@lang('Withdraw List')</a>
                        </div>
                    </li>

                    <li class="hs-has-sub-menu nav-item">
                        <a id="appsMegaMenu"
                           class="hs-mega-menu-invoker nav-link dropdown-toggle {{menuActive(['user.fund.index','user.notification.permission','user.badges','user.referral.bonus','user.referral','user.ticket.view','user.ticket.create','user.ticket.list','user.transaction.list'])}}"
                           href="javascript:void(0)" role="button"><i
                                class="fa-duotone fa-thin fa-grid-2-plus dropdown-item-icon"></i>
                            @lang('More')</a>

                        <div class="hs-sub-menu dropdown-menu navbar-dropdown-menu-borderless hs-sub-menu-desktop-lg"
                             aria-labelledby="appsMegaMenu" style="min-width: 14rem; display: none;">
                            <a class="dropdown-item {{ menuActive(['user.fund.index']) }}"
                               href="{{ route('user.fund.index') }}">
                                    <span> <i
                                            class="fa-light fa-tasks-alt dropdown-item-icon"></i>@lang('Payment History')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Payment History')</small>
                            </a>
                            <a class="dropdown-item {{ menuActive(['user.transaction.list']) }}"
                               href="{{ route('user.transaction.list') }}">
                                    <span> <i
                                            class="fal fa-chart-line dropdown-item-icon"></i>@lang('Transactions')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Transactions')</small>
                            </a>

                            <a class="dropdown-item {{ menuActive(['user.ticket.list','user.ticket.view','user.ticket.create']) }}"
                               href="{{ route('user.ticket.list') }}">
                                    <span><i class="fa-light fa-headset dropdown-item-icon"></i>
                                        @lang('Support Ticket')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Support Ticket')</small>
                            </a>

                            <a class="dropdown-item {{ menuActive(['user.referral']) }}"
                               href="{{ route('user.referral') }}">
                                    <span> <i class="fa-solid fa-asterisk dropdown-item-icon"></i>
                                        @lang('Referral')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Referral')</small>
                            </a>

                            <a class="dropdown-item {{ menuActive(['user.referral.bonus']) }}"
                               href="{{ route('user.referral.bonus') }}">
                                    <span><i class="fa-sharp fa-thin fa-gift dropdown-item-icon"></i>
                                        @lang('Referral Bonus')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Referral Bonus')</small>
                            </a>

                            <a class="dropdown-item {{ menuActive(['user.badges']) }}"
                               href="{{ route('user.badges') }}">
                                    <span><i class="fal fa-badge dropdown-item-icon"></i>
                                        @lang('Badges')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Badges')</small>
                            </a>
                            <a class="dropdown-item {{ menuActive(['user.notification.permission']) }}"
                               href="{{ route('user.notification.permission') }}">
                                    <span><i class="fa-regular fa-bell dropdown-item-icon"></i>
                                        @lang('Notification Permission')</span>
                                <small class="d-none">@lang('More') &gt; @lang('Notification Permission')</small>
                            </a>
                        </div>
                    </li>


                </ul>

            </div>
            <!-- End Collapse -->
        </nav>
    </div>
</header>
<!-- ========== END HEADER ========== -->

<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container">
        @yield('content')
    </div>
    <!-- End Content -->
</main>

<script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/jquery-migrate.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>

<!-- JS Implementing Plugins -->
<script src="{{ asset('assets/admin/js/hs-navbar-vertical-aside.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/hs-form-search.min.js') }}"></script>
<script src="{{ asset('assets/global/js/notiflix-aio-3.2.6.min.js') }}"></script>
<script src="{{ asset('assets/global/js/vue.min.js') }}"></script>
<script src="{{ asset('assets/global/js/axios.min.js') }}"></script>
<script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/theme.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/hs-mega-menu.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/js-switch-element.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/main.js') }}"></script>
@stack('js-lib')
@stack('script')
@include('plugins')

<script>
    function currencyPosition(amount) {
        var currencyPosition = @json(basicControl()->is_currency_position);
        var has_space_between_currency_and_amount = @json(basicControl()->has_space_between_currency_and_amount);
        var currency_symbol = @json(basicControl()->currency_symbol);
        var base_currency = @json(basicControl()->base_currency);
        amount = parseFloat(amount).toFixed(2);
        if (currencyPosition === 'left' && has_space_between_currency_and_amount) {
            return currency_symbol + '  ' + amount;
        } else if (currencyPosition === 'left' && !has_space_between_currency_and_amount) {
            return currency_symbol + ' ' + amount;
        } else if (currencyPosition === 'right' && has_space_between_currency_and_amount) {
            return amount + '  ' + base_currency;
        } else {
            return amount + '  ' + base_currency;
        }
    }
</script>
<script>
    (function () {
        // INITIALIZATION OF BOOTSTRAP DROPDOWN
        // =======================================================
        HSBsDropdown.init()


        // INITIALIZATION OF MEGA MENU
        // =======================================================
        new HSMegaMenu('.js-mega-menu', {
            desktop: {
                position: 'left'
            }
        })
        // INITIALIZATION OF FORM SEARCH
        // =======================================================
        new HSFormSearch('.js-form-search')
    })()
</script>


<script>
    window.addEventListener('load', () => {
        const isFirefox = navigator.userAgent.includes('Firefox');
        let deferredPrompt = null;

        if (!isFirefox && 'BeforeInstallPromptEvent' in window) {

            window.addEventListener('beforeinstallprompt', (e) => {

                e.preventDefault();
                deferredPrompt = e;

                const installButton = document.getElementById('installButton');
                let pwaStatus = localStorage.getItem('pwa_install');

                if (pwaStatus === 'install') {
                    localStorage.removeItem('pwa_install')
                }

                if (pwaStatus !== 'not_install' && pwaStatus !== 'install') {
                    $("#pwaInstallPopup").show(); // Show your modal
                }

                if (installButton) {
                    installButton.addEventListener('click', async () => {
                        if (!deferredPrompt) {
                            alert('No installation prompt available.');
                            return; // Stop further execution if no prompt is available
                        }
                        deferredPrompt.prompt();
                        const {outcome} = await deferredPrompt.userChoice;
                        deferredPrompt = null;
                        if (outcome === 'accepted') {
                            localStorage.setItem('pwa_install', 'install');
                            installButton.remove()
                        } else {
                            localStorage.setItem('pwa_install', 'not_install');
                        }
                    });
                }
            });

        } else if (isFirefox) {
            const installButton = document.getElementById('installButton');
            let pwaStatus = localStorage.getItem('pwa_install');
            if (pwaStatus !== 'not_install' && pwaStatus !== 'install') {
                $("#pwaInstallPopup").show(); // Show your modal with Firefox-specific instructions
            }

            installButton.addEventListener('click', () => {
                // Show custom instructions for Firefox
                alert("To install the app on Firefox, use a mobile device. Open the browser menu (three dots in the top-right corner) and select 'Add to Home Screen'.");
                localStorage.setItem('pwa_install', 'install');
            });
        }
        let pwaStatus = localStorage.getItem('pwa_install');
        if (pwaStatus === 'install') {
            const installButton1 = document.getElementById('installButton');
            if (installButton1) {
                installButton1.remove(); // Remove the button if required
            }
        }

    });
</script>
<script>
    (function () {
        // STYLE SWITCHER
        // =======================================================
        const $dropdownBtn = document.getElementById('selectThemeDropdown') // Dropdowon trigger
        const $variants = document.querySelectorAll(
            `[aria-labelledby="selectThemeDropdown"] [data-icon]`) // All items of the dropdown

        // Function to set active style in the dorpdown menu and set icon for dropdown trigger
        const setActiveStyle = function () {
            $variants.forEach($item => {
                if ($item.getAttribute('data-value') === HSThemeAppearance.getOriginalAppearance()) {
                    $dropdownBtn.innerHTML = `<i class="${$item.getAttribute('data-icon')}" />`
                    return $item.classList.add('active')
                }

                if (HSThemeAppearance.getOriginalAppearance() === 'auto' && getSystemTheme() === 'dark') {

                    document.getElementById('pushChatArea')?.classList.add('bg-dark')
                } else if (HSThemeAppearance.getOriginalAppearance() === 'dark') {
                    document.getElementById('pushChatArea')?.classList.add('bg-dark')
                } else {
                    document.getElementById('pushChatArea')?.classList.remove('bg-dark')
                }

                $item.classList.remove('active')
            })
        }

        function getSystemTheme() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        // Add a click event to all items of the dropdown to set the style
        $variants.forEach(function ($item) {
            $item.addEventListener('click', function () {
                HSThemeAppearance.setAppearance($item.getAttribute('data-value'))
            })
        })

        // Call the setActiveStyle on load page
        setActiveStyle()

        // Add event listener on change style to call the setActiveStyle function
        window.addEventListener('on-hs-appearance-change', function () {
            setActiveStyle()
        })
    })()
</script>

@if(basicControl()->in_app_notification)
    <script>
        'use strict';
        let pushNotificationArea = new Vue({
            el: "#pushNotificationArea",
            data: {
                items: [],
            },
            beforeMount() {
                this.getNotifications();
                this.pushNewItem();
            },
            methods: {
                getNotifications() {
                    let app = this;
                    axios.get("{{ route('user.push.notification.show') }}")
                        .then(function (res) {
                            app.items = res.data;
                        })
                },
                readAt(id, link) {
                    let app = this;
                    let url = "{{ route('user.push.notification.readAt', 0) }}";
                    url = url.replace(/.$/, id);
                    axios.get(url)
                        .then(function (res) {
                            if (res.status) {
                                app.getNotifications();
                                if (link !== '#') {
                                    window.location.href = link
                                }
                            }
                        })
                },
                readAll() {
                    let app = this;
                    let url = "{{ route('user.push.notification.readAll') }}";
                    axios.get(url)
                        .then(function (res) {
                            if (res.status) {
                                app.items = [];
                            }
                        })
                },
                pushNewItem() {
                    let app = this;
                    Pusher.logToConsole = false;
                    let pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                        encrypted: true,
                        cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
                    });
                    let channel = pusher.subscribe('user-notification.' + "{{ Auth::id() }}");
                    channel.bind('App\\Events\\UserNotification', function (data) {
                        app.items.unshift(data.message);
                    });
                    channel.bind('App\\Events\\UpdateUserNotification', function (data) {
                        app.getNotifications();
                    });
                }
            }
        });
    </script>
@endif
<script>
    $(document).on('input', '.global-search', function () {
        var search = $(this).val().toLowerCase();
        if (search.length === 0) {
            $('#searchDropdownMenu').find('.content').html('');
            return false;
        }

        $('#searchDropdownMenu').find('.content').html('');

        // Search through all nav items and submenu items
        var match = $('#navbarVerticalMenu .nav-item').filter(function (idx, element) {
            // Check main nav items
            var mainItemText = $(element).find('> a').text().trim().toLowerCase();
            if (mainItemText.indexOf(search) >= 0) {
                return true;
            }

            // Check submenu items if exists
            var subItems = $(element).find('.dropdown-item');
            var subItemMatch = false;
            subItems.each(function () {
                var subItemText = $(this).text().trim().toLowerCase();
                if (subItemText.indexOf(search) >= 0) {
                    subItemMatch = true;
                    return false; // break loop
                }
            });

            return subItemMatch;
        });

        // Also include direct matches from submenu items
        var subMenuMatches = $('#navbarVerticalMenu .dropdown-item').filter(function () {
            return $(this).text().trim().toLowerCase().indexOf(search) >= 0;
        });

        if (match.length === 0 && subMenuMatches.length === 0) {
            $('#searchDropdownMenu').find('.content').append(
                `<div class="text-center p-4">
                <div class="dataTables-image oc-error mb-3" data-hs-theme-appearance="default"></div>
                <div class="dataTables-image oc-error-light mb-3" data-hs-theme-appearance="dark"></div>
                <p class="mb-0">@lang('No Result Found')</p>
            </div>`);
            return false;
        }

        // Process main menu matches
        match.each(function (index, element) {
            var mainLink = $(element).find('> a');
            var item_text = mainLink.text().trim();
            var item_url = mainLink.attr('href');
            var icon_element = mainLink.find('i');
            var icon_class = icon_element.attr('class')?.replace('dropdown-item-icon', '').trim();

            if (item_url && item_url !== '#' && item_url !== 'javascript:void(0)') {
                $('#searchDropdownMenu').find('.content').append(
                    `<a class="dropdown-item" href="${item_url}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <span class="icon icon-soft-dark icon-xs icon-circle">
                                <i class="${icon_class}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 text-truncate ms-2">
                            <span>${item_text}</span>
                        </div>
                    </div>
                </a>`);
            }
        });

        // Process submenu matches
        subMenuMatches.each(function (index, element) {
            var item_text = $(element).find('span').first().text().trim() || $(element).text().trim();
            var item_url = $(element).attr('href');
            var icon_element = $(element).find('i');
            var icon_class = icon_element.attr('class')?.replace('dropdown-item-icon', '').trim();

            if (item_url && item_url !== '#' && item_url !== 'javascript:void(0)') {
                $('#searchDropdownMenu').find('.content').append(
                    `<a class="dropdown-item" href="${item_url}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <span class="icon icon-soft-dark icon-xs icon-circle">
                                <i class="${icon_class}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 text-truncate ms-2">
                            <span>${item_text}</span>
                        </div>
                    </div>
                </a>`);
            }
        });
    });
</script>


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
