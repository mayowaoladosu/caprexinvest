@extends(template().'layouts.user')
@section('page_title',trans('Dashboard'))
@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-header-title">@lang('Dashboard')</h1>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Page Header -->

    @if(basicControl()->push_notification)
        <div id="firebase-app">
            <div class="p-3 mb-5 alert alert-soft-dark mb-4 mb-lg-7" role="alert"
                 v-if="notificationPermission == 'default' && !is_notification_skipped" v-cloak>
                <div class="alert-box d-flex flex-wrap align-items-center">
                    <div class="flex-shrink-0">
                        <img class="avatar avatar-xl"
                             src="{{ asset('assets/admin/img/oc-megaphone.svg') }}"
                             alt="Image Description" data-hs-theme-appearance="default">
                        <img class="avatar avatar-xl"
                             src="{{ asset('assets/admin/img/oc-megaphone-light.svg') }}"
                             alt="Image Description" data-hs-theme-appearance="dark">
                    </div>

                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-1">@lang("Attention!")</h3>
                        <div class="d-flex align-items-center">
                            <p class="mb-0 text-body"> @lang('Please allow your browser to get instant push notification. Allow it from notification setting.')</p>
                            <button id="allow-notification" class="btn btn-sm btn-primary mx-2"><i
                                    class="fa fa-check-circle"></i> @lang('Allow me')</button>
                        </div>
                    </div>
                    <button type="button" class="btn-close"
                            @click.prevent="skipNotification" data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            </div>
            <div class="alert alert-soft-dark mb-4 mb-lg-7" role="alert"
                 v-if="notificationPermission == 'denied' && !is_notification_skipped" v-cloak>
                <div class="d-flex align-items-center mt-4">
                    <div class="flex-shrink-0">
                        <img class="avatar avatar-xl"
                             src="{{ asset('assets/admin/img/oc-megaphone.svg') }}"
                             alt="Image Description" data-hs-theme-appearance="default">
                        <img class="avatar avatar-xl"
                             src="{{ asset('assets/admin/img/oc-megaphone-light.svg') }}"
                             alt="Image Description" data-hs-theme-appearance="dark">
                    </div>

                    <div class="flex-grow-1 ms-3">
                        <h3 class=" mb-1">@lang("Attention!")</h3>
                        <div class="d-flex align-items-center">
                            <p class="mb-0 text-body"> @lang("Please allow your browser to get instant push notification. Allow it from notification setting.")</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" @click.prevent="skipNotification" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif


    <!-- Stats -->
    <div class="row row-cols-1 row-cols-md-2 record">
        <div class="col-md-12">
            <div class="row ">
                <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                    <div class="card pb-2 card-hover-shadow text-center h-100  statRecord">
                        <div class="card-progress-wrap">
                            <div class="progress card-progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="card-body d-flex justify-content-start align-items-center gap-2">
                            <div class="">
                                <div class="icon icon-lg icon-soft-dark icon-circle">
                                    <i class="fa-light fa-wallet"></i>
                                </div>
                            </div>
                            <div class="">
                            <span
                                class="h5 d-flex justify-content-start text-muted mb-1" id="dashboard-wallet-balance">{{currencyPosition($user->balance+0)}}</span>
                                <span class="fw-semibold ">@lang('Balance') </span>
                            </div>
                        </div>

                    </div>
                </div>
                @if(basicControl()->invest_status)
                    <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                        <div class="card pb-2 card-hover-shadow text-center h-100  statRecord">
                            <div class="card-progress-wrap">
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="card-body d-flex justify-content-start align-items-center gap-2">
                                <div class="">
                                    <div class="icon icon-lg icon-soft-dark icon-circle">
                                        <i class="fa-light fa-wallet"></i>
                                    </div>
                                </div>
                                <div class="">
                            <span
                                class="h5 d-flex justify-content-start text-muted mb-1" id="dashboard-profit-balance">{{currencyPosition($user->profit_balance+0)}}</span>
                                    <span class="fw-semibold ">@lang('Profit Balance') </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                        <div class="card pb-2 card-hover-shadow text-center h-100 statRecord">

                            <div class="card-progress-wrap">
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="card-body d-flex justify-content-start align-items-center gap-2">
                                <div class="">
                                    <div class="icon icon-lg icon-soft-dark icon-circle">
                                        <i class="fa-light fa-file-chart-pie"></i>
                                    </div>
                                </div>
                                <div class="">
                                <span
                                    class="h5 d-flex justify-content-start text-muted mb-1">{{currencyPosition($user->total_profit+0)}}</span>
                                    <span class="fw-semibold ">@lang('Total Profit') </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                        <div class="card pb-2 card-hover-shadow text-center h-100 statRecord">

                            <div class="card-progress-wrap">
                                <div class="progress card-progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="card-body d-flex justify-content-start align-items-center gap-2">
                                <div class="">
                                    <div class="icon icon-lg icon-soft-dark icon-circle">
                                        <i class="fa-light fa-chart-network"></i>
                                    </div>
                                </div>
                                <div class="">
                                <span
                                    class="h5 d-flex justify-content-start text-muted mb-1">{{currencyPosition($user->total_invest+0)}}</span>
                                    <span class="fw-semibold ">@lang('Total Invest') </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    @if($rank)
                        <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                            <div class="card pb-2 card-hover-shadow text-center h-100 statRecord">

                                <div class="card-progress-wrap">
                                    <div class="progress card-progress">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="card-body d-flex justify-content-start align-items-center gap-2">
                                    <div class="">
                                        <div class="icon icon-lg icon-soft-dark icon-circle">
                                            <i class="fal fa-badge-check"></i>
                                        </div>
                                    </div>
                                    <div class="">
                                <span
                                    class="h5 d-flex justify-content-start text-muted mb-1">{{$rank->rank_name}}</span>
                                        <span class="fw-semibold ">@lang('Current Badge')</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                @endif
                <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                    <div class="card pb-2 card-hover-shadow text-center h-100 statRecord">

                        <div class="card-progress-wrap">
                            <div class="progress card-progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="card-body d-flex justify-content-start align-items-center gap-2">
                            <div class="">
                                <div class="icon icon-lg icon-soft-dark icon-circle">
                                    <i class="bi bi-wallet-fill"></i>
                                </div>
                            </div>
                            <div class="">
                            <span
                                class="h5 d-flex justify-content-start text-muted mb-1">{{currencyPosition($total_deposit+0)}}</span>
                                <span class="fw-semibold ">@lang('Total Deposit') </span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                    <div class="card pb-2 card-hover-shadow text-center h-100 statRecord">

                        <div class="card-progress-wrap">
                            <div class="progress card-progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="card-body d-flex justify-content-start align-items-center gap-2">
                            <div class="">
                                <div class="icon icon-lg icon-soft-dark icon-circle">
                                    <i class="fa-light fa-money-bill-wave"></i>
                                </div>
                            </div>
                            <div class="">
                                <span
                                    class="h5 d-flex justify-content-start text-muted mb-1">{{currencyPosition($total_withdraw+0)}}</span>
                                <span class="fw-semibold ">@lang('Total Payout')</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col mb-3 mb-lg-5 col-lg-3 col-md-6">
                    <div class="card pb-2 card-hover-shadow text-center h-100 statRecord">

                        <div class="card-progress-wrap">
                            <div class="progress card-progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="card-body d-flex justify-content-start align-items-center gap-2">
                            <div class="">
                                <div class="icon icon-lg icon-soft-dark icon-circle">
                                    <i class="fal fa-user-headset"></i>
                                </div>
                            </div>
                            <div class="">
                                <span
                                    class="h5 d-flex justify-content-start text-muted mb-1">{{$tickets}}</span>
                                <span class="fw-semibold ">@lang('Total Ticket')</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(basicControl()->trading_enabled)
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card card-hover-shadow h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">@lang('Trading Invested')</small>
                        <h4 class="mb-0" id="dashboard-trading-invested">{{ currencyPosition(0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card card-hover-shadow h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">@lang('Open Trading Positions')</small>
                        <h4 class="mb-0" id="dashboard-open-positions">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card card-hover-shadow h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">@lang('Trading Orders')</small>
                        <h4 class="mb-0" id="dashboard-trading-orders">0</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- End Stats -->

    <div class="row mb-5">
        <div class="col-md-12">
            <h5 class="mb-20">@lang('Recent Activity (7 Days)')</h5>
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-xxl-3  col-md-6 col-sm-6">
                            <div class="transaction-box activity-card">
                                <div class="icon-box">
                                    <i class="fa-light fa-headset"></i>
                                </div>
                                <div class="text-box">
                                    <h5 class="mb-0">{{$recentTickets}}</h5>
                                    <p class="mb-0">@lang('Support')</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3  col-md-6 col-sm-6">
                            <div class="transaction-box activity-card">
                                <div class="icon-box">
                                    <i class="fa-light fa-money-bill-wave"></i>
                                </div>
                                <div class="text-box">
                                    <h5 class="mb-0">{{currencyPosition($recent_withdraw)}}</h5>
                                    <p class="mb-0">@lang('Withdraw')</p>
                                </div>
                            </div>
                        </div>
                        @if(basicControl()->invest_status)
                            <div class="col-xxl-3  col-md-6 col-sm-6 ">
                                <div class="transaction-box activity-card">
                                    <div class="icon-box">
                                        <i class="fal fa-notes-medical"></i>
                                    </div>
                                    <div class="text-box">
                                        <h5 class="mb-0">{{currencyPosition($recent_plan_invest)}}</h5>
                                        <p class="mb-0">@lang('Invest')</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-xxl-3  col-md-6 col-sm-6">
                            <div class="transaction-box activity-card">
                                <div class="icon-box">
                                    <i class="fal fa-chart-line"></i>
                                </div>
                                <div class="text-box">
                                    <h5 class="mb-0">{{currencyPosition($recent_deposit)}}</h5>
                                    <p class="mb-0">@lang('Deposit')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header card-header-content-sm-between">
                    @if(basicControl()->invest_status)
                    <h4 class="card-header-title mb-2 mb-sm-0">@lang('Invest History & Referral Bonus')</h4>
                    @else
                        <h4 class="card-header-title mb-2 mb-sm-0">@lang('Referral Bonus')</h4>
                    @endif

                    <!-- Daterangepicker -->
                    <button id="js-daterangepicker-predefined"
                            class="btn btn-ghost-secondary btn-sm dropdown-toggle">
                        <i class="bi-calendar-week"></i>
                        <span class="js-daterangepicker-predefined-preview ms-1"></span>
                    </button>
                    <!-- End Daterangepicker -->
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Chart -->
                    <div class="chartjs-custom" style="height: 20rem;">
                        <canvas id="updatingLineChart"
                                data-hs-chartjs-options='{
                  "type": "line",
                  "data": {
                     "labels": [],
                     "datasets": [{
                      "backgroundColor": ["rgba(55, 125, 255, .5)", "rgba(255, 255, 255, .2)"],
                      "borderColor": "#377dff",
                      "borderWidth": 2,
                      "pointRadius": 0,
                      "hoverBorderColor": "#377dff",
                      "pointBackgroundColor": "#377dff",
                      "pointBorderColor": "#fff",
                      "pointHoverRadius": 0,
                      "tension": 0.4
                    },
                    {
                      "backgroundColor": ["rgba(0, 201, 167, .5)", "rgba(255, 255, 255, .2)"],
                      "borderColor": "#00c9a7",
                      "borderWidth": 2,
                      "pointRadius": 0,
                      "hoverBorderColor": "#00c9a7",
                      "pointBackgroundColor": "#00c9a7",
                      "pointBorderColor": "#fff",
                      "pointHoverRadius": 0,
                      "tension": 0.4
                    }

                    ]
                  },
                  "options": {
                    "gradientPosition": {"y1": 200},
                     "scales": {
                        "y": {
                          "grid": {
                            "color": "#e7eaf3",
                            "drawBorder": false,
                            "zeroLineColor": "#e7eaf3"
                          },
                          "ticks": {
                            "min": 0,
                            "max": 100,
                            "stepSize": 20,
                            "color": "#97a4af",
                            "font": {
                              "family": "Open Sans, sans-serif"
                            },
                            "padding": 10,
                            "postfix": "k"
                          }
                        },
                        "x": {
                          "grid": {
                            "display": false,
                            "drawBorder": false
                          },
                          "ticks": {
                            "color": "#97a4af",
                            "font": {
                              "size": 12,
                              "family": "Open Sans, sans-serif"
                            },
                            "padding": 5
                          }
                        }
                    },
                    "plugins": {
                      "tooltip": {
                        "enabled": false,
                        "prefix": "{{basicControl()->currency_symbol}}",
                        "postfix": "",
                        "hasIndicator": true,
                        "mode": "index",
                        "intersect": false,
                        "lineMode": true,
                        "lineWithLineColor": "rgba(19, 33, 68, 0.075)"
                      },
                      "datalabels" : {
                        "display" : false
                      }
                    },
                    "hover": {
                      "mode": "nearest",
                      "intersect": true
                    }
                  }
                }'>
                        </canvas>
                    </div>
                    <div class="row justify-content-center">
                        @if(basicControl()->invest_status)
                            <div class="col-auto">
                                <span class="legend-indicator bg-primary"></span> @lang('Invest History')
                            </div>
                            <!-- End Col -->


                            <div class="col-auto">
                                <span class="legend-indicator bg-info"></span> @lang('Referral Bonus')
                            </div>
                        @else
                            <div class="col-auto">
                                <span class="legend-indicator bg-info"></span> @lang('Referral Bonus')
                            </div>
                        @endif


                        <!-- End Col -->
                    </div>
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>
        <div class="col-lg-6 mb-3 mb-lg-5">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header card-header-content-sm-between">
                    <h4 class="card-header-title mb-2 mb-sm-0">@lang('Transactions')</h4>

                    <!-- Daterangepicker -->
                    <button id="js-daterangepicker-predefined2"
                            class="btn btn-ghost-secondary btn-sm dropdown-toggle">
                        <i class="bi-calendar-week"></i>
                        <span class="js-daterangepicker-predefined-preview2 ms-1"></span>
                    </button>
                    <!-- End Daterangepicker -->
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Chart -->
                    <div class="chartjs-custom mx-auto" style="height: 20rem;">
                        <canvas class="js-chart-datalabels"></canvas>
                    </div>
                    <!-- End Chart -->


                    <!-- End Row -->
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>
        <!-- End Col -->

        <div class="col-lg-12 mb-3 mb-lg-5">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header card-header-content-sm-between">
                    <h4 class="card-header-title mb-2 mb-sm-0">@lang('Gold Price History')</h4>

                    <!-- Daterangepicker -->
                    <button id="js-daterangepicker-predefined3"
                            class="btn btn-ghost-secondary btn-sm dropdown-toggle">
                        <i class="bi-calendar-week"></i>
                        <span class="js-daterangepicker-predefined-preview3 ms-1"></span>
                    </button>
                    <!-- End Daterangepicker -->
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Chart -->
                    <div class="chartjs-custom mx-auto" style="height: 20rem;">
                        <canvas class="js-chart-gold"></canvas>
                    </div>
                    <!-- End Chart -->


                    <!-- End Row -->
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>
    </div>
    @if(basicControl()->trading_enabled && basicControl()->trading_dashboard_chart_enabled)
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-header-title mb-0">@lang('Live Trading Market')</h4>
                        <a href="{{ route('user.trading.index') }}" class="btn btn-sm btn-primary">@lang('Open Trading')</a>
                    </div>
                    <div class="card-body" style="height: 460px;">
                        @include(template() . 'partials.trading_chart_widget', [
                            'chartSymbol' => basicControl()->trading_chart_symbol ?: 'NASDAQ:AAPL',
                            'chartInterval' => basicControl()->trading_chart_interval ?: 'D',
                            'chartTheme' => basicControl()->trading_chart_theme ?: 'dark',
                        ])
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- End Row -->
@endsection

@push('js-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/chart.min.js') }}"></script>
    <script src="{{asset('assets/admin/js/chartjs-plugin-datalabels.min.js')}}"></script>
@endpush

@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/tom-select.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        $(document).on('ready', function () {


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            function cb2(start, end) {
                $('#js-daterangepicker-predefined3 .js-daterangepicker-predefined-preview3').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            function cb3(start, end) {
                $('#js-daterangepicker-predefined2 .js-daterangepicker-predefined-preview2').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            $('#js-daterangepicker-predefined2').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb2);

            $('#js-daterangepicker-predefined3').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb3);

            cb(moment().startOf('month'), moment().endOf('month'));
            cb2(moment().startOf('month'), moment().endOf('month'));
            cb3(moment().startOf('month'), moment().endOf('month'));


            (() => {
                Chart.register(ChartDataLabels);

                let goldChart = null;
                const chartColor = '#FFD700';
                const currencySymbol = '{{ basicControl()->currency_symbol }}';

                const updateGoldChart = async () => {
                    try {

                        let dateRange = $('#js-daterangepicker-predefined3').data('daterangepicker');

                        let startDate = dateRange.startDate.format('YYYY-MM-DD');
                        let endDate = dateRange.endDate.format('YYYY-MM-DD');


                        const response = await axios.get('{{ route("user.gold.chart.data") }}', {
                            params: { start_date: startDate, end_date: endDate }
                        });

                        const { labels, rates } = response.data;

                        const datasets = [{
                            label: 'Gold Price ({{basicControl()->base_currency}})',
                            data: rates,
                            backgroundColor: chartColor + '80',
                            borderColor: chartColor,
                            borderWidth: 2,
                            borderRadius: 6,
                            color: '#fff'
                        }];

                        const ctx = document.querySelector('.js-chart-gold').getContext('2d');

                        if (goldChart) {
                            goldChart.data.labels = labels;
                            goldChart.data.datasets = datasets;
                            goldChart.update();
                        } else {
                            goldChart = new Chart(ctx, {
                                type: 'bar',
                                data: { labels, datasets },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 600 },
                                    plugins: {
                                        tooltip: {
                                            enabled: true,
                                            backgroundColor: '#333',
                                            titleColor: '#fff',
                                            bodyColor: '#fff',
                                            cornerRadius: 6,
                                            callbacks: {
                                                label: context => `${currencySymbol}${context.raw}`
                                            }
                                        },
                                        legend: { display: false },
                                        datalabels: { display: false }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: '#666', font: { size: 12 } }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: 'rgba(0,0,0,0.05)' },
                                            ticks: {
                                                color: '#666',
                                                font: { size: 12 },
                                                callback: value => `${currencySymbol}${value}`
                                            }
                                        }
                                    }
                                },
                                plugins: [ChartDataLabels] // still loaded, but disabled above
                            });

                        }
                    } catch (error) {
                        console.error('Error fetching gold data:', error);
                    }
                };

                updateGoldChart();

                $('#js-daterangepicker-predefined3').on('apply.daterangepicker', updateGoldChart);

            })();

            (() => {
                Chart.register(ChartDataLabels);

                let transactionChart = null; // single chart instance
                const chartColor = '#7000f2';
                const currencySymbol = '{{ basicControl()->currency_symbol }}'; // use your PHP helper

                const updateTransactionChart = async () => {
                    try {
                        const dateRange = $('#js-daterangepicker-predefined2').data('daterangepicker');
                        const startDate = dateRange.startDate.format('YYYY-MM-DD');
                        const endDate = dateRange.endDate.format('YYYY-MM-DD');

                        const response = await axios.get('{{ route("user.transaction.history") }}', {
                            params: {start_date: startDate, end_date: endDate}
                        });

                        const {transactions, labels} = response.data;

                        const datasets = [{
                            label: 'Transactions',
                            data: transactions,
                            backgroundColor: chartColor + '80', // 50% opacity
                            borderColor: chartColor,
                            borderWidth: 2,
                            borderRadius: 6,
                            color: '#fff'
                        }];

                        const ctx = document.querySelector('.js-chart-datalabels').getContext('2d');

                        if (transactionChart) {
                            // Update existing chart
                            transactionChart.data.labels = labels;
                            transactionChart.data.datasets = datasets;
                            transactionChart.update();
                        } else {
                            // Initialize chart
                            transactionChart = new Chart(ctx, {
                                type: 'bar',
                                data: {labels, datasets},
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {duration: 600},
                                    plugins: {
                                        tooltip: {
                                            enabled: true,
                                            backgroundColor: '#333',
                                            titleColor: '#fff',
                                            bodyColor: '#fff',
                                            cornerRadius: 6,
                                            callbacks: {
                                                label: context => `${currencySymbol}${context.raw}`
                                            }
                                        },
                                        legend: {display: false},
                                        datalabels: { display: false }

                                    },
                                    scales: {
                                        x: {
                                            grid: {display: false},
                                            ticks: {color: '#666', font: {size: 12}}
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: {color: 'rgba(0,0,0,0.05)'},
                                            ticks: {
                                                color: '#666',
                                                font: {size: 12},
                                                callback: value => `${currencySymbol}${value}`
                                            }
                                        }
                                    }
                                },
                                plugins: [ChartDataLabels]
                            });
                        }
                    } catch (error) {
                        console.error('Error fetching data:', error);
                    }
                };

                // Initial load
                updateTransactionChart();

                // Update chart on date range change
                $('#js-daterangepicker-predefined2').on('apply.daterangepicker', updateTransactionChart);
            })();


            (function () {
                // INITIALIZATION OF UPDATING CHARTJS
                // =======================================================


                HSCore.components.HSChartJS.init(document.querySelector('#updatingLineChart'), {
                    data: {
                        datasets: [
                            {
                                data: []
                            }
                        ]
                    }
                });

                async function updateLineChart() {
                    let dateRange = $('#js-daterangepicker-predefined').data('daterangepicker');

                    let startDate = dateRange.startDate.format('YYYY-MM-DD');
                    let endDate = dateRange.endDate.format('YYYY-MM-DD');

                    try {
                        const response = await axios.get('{{route('user.invest.history')}}', {
                            params: {
                                start_date: startDate,
                                end_date: endDate
                            }
                        });
                        // Update chart with new data
                        if (response.data && response.data.labels) {
                            let investStatus = Boolean('{{basicControl()->invest_status?true:false}}')
                            const updatingLineChart = HSCore.components.HSChartJS.getItem('updatingLineChart')
                            updatingLineChart.data.labels = response.data.labels;
                            if(investStatus){
                                updatingLineChart.data.datasets[0].data = response.data.investData;
                            }

                            console.log(response.data)
                            updatingLineChart.data.datasets[1].data = response.data.referral_bonus;
                            updatingLineChart.update()
                        }

                    } catch (error) {
                        console.error("Error fetching investment history:", error);
                    }
                }

                updateLineChart();
                $('#js-daterangepicker-predefined').on('apply.daterangepicker', updateLineChart);
            })()

        });


    </script>
    @if(basicControl()->trading_enabled)
        <script>
            (function () {
                const walletEl = document.getElementById('dashboard-wallet-balance');
                const profitEl = document.getElementById('dashboard-profit-balance');
                const investedEl = document.getElementById('dashboard-trading-invested');
                const positionsEl = document.getElementById('dashboard-open-positions');
                const ordersEl = document.getElementById('dashboard-trading-orders');

                async function refreshTradingDashboardSnapshot() {
                    try {
                        const response = await axios.get("{{ route('user.trading.snapshot') }}");
                        const snapshot = response.data || {};
                        if (snapshot.enabled === false) {
                            return;
                        }

                        if (walletEl && snapshot.wallet_balance !== undefined) {
                            walletEl.textContent = currencyPosition(snapshot.wallet_balance);
                        }
                        if (profitEl && snapshot.profit_balance !== undefined) {
                            profitEl.textContent = currencyPosition(snapshot.profit_balance);
                        }
                        if (investedEl && snapshot.trading_invested_total !== undefined) {
                            investedEl.textContent = currencyPosition(snapshot.trading_invested_total);
                        }
                        if (positionsEl && snapshot.open_positions_count !== undefined) {
                            positionsEl.textContent = snapshot.open_positions_count;
                        }
                        if (ordersEl && snapshot.trading_orders_total !== undefined) {
                            ordersEl.textContent = snapshot.trading_orders_total;
                        }
                    } catch (error) {
                        // Prevent dashboard noise on transient failures.
                    }
                }

                refreshTradingDashboardSnapshot();
                setInterval(refreshTradingDashboardSnapshot, 10000);
            })();
        </script>
    @endif
@endpush



@if(basicControl()->push_notification &&  $firebaseNotify)
    @push('script')
        <script type="module">

            import {initializeApp} from "https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js";
            import {
                getMessaging,
                getToken,
                onMessage
            } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js";

            const firebaseConfig = {
                apiKey: "{{$firebaseNotify['apiKey']}}",
                authDomain: "{{$firebaseNotify['authDomain']}}",
                projectId: "{{$firebaseNotify['projectId']}}",
                storageBucket: "{{$firebaseNotify['storageBucket']}}",
                messagingSenderId: "{{$firebaseNotify['messagingSenderId']}}",
                appId: "{{$firebaseNotify['appId']}}",
                measurementId: "{{$firebaseNotify['measurementId']}}"
            };

            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('{{ getProjectDirectory() }}' + '/firebase-messaging-sw.js', {scope: './'})
                    .then(function (registration) {

                        requestPermissionAndGenerateToken(registration);
                    })
                    .catch(function (error) {

                    });
            }

            onMessage(messaging, (payload) => {
                if (payload.data && (payload.data.foreground || parseInt(payload.data.foreground) === 1)) {
                    const title = payload.notification?.title || "Notification";
                    const options = {
                        body: payload.notification?.body || "",
                        icon: payload.notification?.icon || "/default-icon.png",
                    };
                    new Notification(title, options);
                }
            });

            function requestPermissionAndGenerateToken(registration) {
                document.addEventListener("click", function (event) {
                    if (event.target.id === 'allow-notification') {
                        Notification.requestPermission().then((permission) => {
                            if (permission === 'granted') {
                                getToken(messaging, {
                                    serviceWorkerRegistration: registration,
                                    vapidKey: "{{$firebaseNotify['vapidKey']}}"
                                }).then((token) => {

                                    $.ajax({
                                        url: "{{ route('user.save.token') }}",
                                        method: "post",
                                        data: {token: token},
                                        success: function (res) {

                                        },
                                        error: function (error) {

                                        }
                                    });
                                    window.newApp.notificationPermission = 'granted';
                                }).catch((error) => {

                                });
                            } else {
                                console.log('denied');
                                window.newApp.notificationPermission = 'denied';
                            }
                        });
                    }
                });
            }

        </script>
        <script>
            window.newApp = new Vue({
                el: "#firebase-app",
                data: {
                    user_foreground: '',
                    user_background: '',
                    notificationPermission: Notification.permission,
                    is_notification_skipped: sessionStorage.getItem('is_notification_skipped') == '1'
                },
                mounted() {
                    sessionStorage.clear();
                    this.user_foreground = "{{$firebaseNotify['user_foreground']}}";
                    this.user_background = "{{$firebaseNotify['user_background']}}";
                },
                methods: {
                    skipNotification() {
                        sessionStorage.setItem('is_notification_skipped', '1')
                        this.is_notification_skipped = true;
                    }
                }
            });
        </script>
    @endpush
@endif

