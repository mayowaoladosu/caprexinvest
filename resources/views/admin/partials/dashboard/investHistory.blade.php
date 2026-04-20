@if(basicControl()->invest_status)
    <div class="col-12">
        <div class="card mb-3 mb-lg-5" id="salesChart">
            <!-- Header -->
            <div class="card-header card-header-content-sm-between">
                <h4 class="card-header-title mb-2 mb-sm-0">@lang('This Month Investment')
                </h4>
                <div class="d-grid d-sm-flex gap-2">

                    <button id="js-daterangepicker-invest-predefined" class="btn btn-white btn-sm dropdown-toggle">
                        <i class="bi-calendar-week"></i>
                        <span class="js-daterangepicker-invest-predefined-preview ms-1"></span>
                    </button>
                </div>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body">
                <div class="row col-lg-divider">
                    <div class="col-lg-9 mb-5 mb-lg-0">
                        <!-- Bar Chart -->
                        <div class="chartjs-custom mb-4">
                            <canvas id="investHistory" class="sales-chart-height"></canvas>
                        </div>
                        <!-- End Bar Chart -->
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <span class="legend-indicator bg-primary"></span> @lang('Plan Invest')
                            </div>
                            <!-- End Col -->
                        </div>
                        <!-- End Row -->
                    </div>

                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-sm-6 col-lg-12">
                                <!-- Stats -->
                                <div class="d-flex justify-content-center flex-column" style="min-height: 9rem;">
                                    <h6 class="card-subtitle "><span
                                            class="selected-date-value">@lang('This Month')</span> @lang(' Invest')</h6>
                                    <span class="d-block display-4 text-dark mb-1 me-3 this-month-invest">0</span>
                                    <span class="d-block text-success">
                    </span>
                                </div>
                                <!-- End Stats -->

                                <hr class="d-none d-lg-block my-0">
                            </div>
                            <!-- End Col -->

                            <div class="col-sm-6 col-lg-12">
                                <!-- Stats -->
                                <div class="d-flex justify-content-center flex-column" style="min-height: 9rem;">
                                    <h6 class="card-subtitle">@lang('Total Invest')</h6>
                                    <span class="d-block display-4 text-dark mb-1 me-3 total-invest">0</span>
                                    <span class="d-block text-danger">

                    </span>
                                </div>
                                <!-- End Stats -->
                            </div>
                            <!-- End Col -->
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Body -->
        </div>
    </div>

    @push('css')
        <style>
            .bg-custom {
                --bs-bg-opacity: 1;
                background-color: #FF9500 !important;
            }
        </style>
    @endpush

    @push('script')
        <script>
            Notiflix.Block.standard('#salesChart');

            function cb2(start, end) {
                $('.js-daterangepicker-invest-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-invest-predefined').daterangepicker({
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

            cb2(moment().startOf('month'), moment().endOf('month'));


            HSCore.components.HSChartJS.init(document.querySelector('#investHistory'), {
                type: "bar",
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: "#377dff",
                        hoverBackgroundColor: "#377dff",
                        borderColor: "#377dff",
                        maxBarThickness: "10"
                    },
                        {
                            data: [],
                            backgroundColor: "#FF9500",
                            borderColor: "#e7eaf3",
                            maxBarThickness: "10"
                        }]
                },
                options: {
                    scales: {
                        y: {
                            grid: {
                                color: "#e7eaf3",
                                drawBorder: false,
                                zeroLineColor: "#e7eaf3"
                            },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 100,
                                color: "#97a4af",
                                font: {
                                    size: 12,
                                    family: "Open Sans, sans-serif"
                                },
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: "#97a4af",
                                font: {
                                    size: 12,
                                    family: "Open Sans, sans-serif"
                                },
                                padding: 5
                            },
                            categoryPercentage: 0.5,
                            maxBarThickness: "10"
                        }
                    },
                    cornerRadius: 2,
                    plugins: {
                        tooltip: {
                            prefix: "{{ basicControl()->currency_symbol }} ",
                            hasIndicator: true,
                            mode: "index",
                            intersect: false
                        }
                    },
                    hover: {
                        mode: "nearest",
                        intersect: true
                    }
                }
            });
            const SalesChart = HSCore.components.HSChartJS.getItem('investHistory');


            updateInvestHistory();

            async function updateInvestHistory() {
                let $url = "{{ route('admin.investHistory') }}"
                let dateRange = $('#js-daterangepicker-invest-predefined').data('daterangepicker');
                let startDate = dateRange.startDate.format('YYYY-MM-DD');
                let endDate = dateRange.endDate.format('YYYY-MM-DD');
                await axios.get($url, {
                    params: {
                        start_date: startDate,
                        end_date: endDate
                    }
                })
                    .then(function (res) {
                        SalesChart.data.labels = res.data.schedule
                        SalesChart.data.datasets[0].data = res.data.planInvest
                        SalesChart.update();
                        let dateRangeInvest = $('#js-daterangepicker-invest-predefined').data('daterangepicker');
                        let dateRangeValue = dateRangeInvest.chosenLabel ?? 'This Month'
                        $('.selected-date-value').text(dateRangeValue);
                        $('.this-month-invest').text(res.data.thisMonthInvest)
                        $('.total-invest').text(res.data.totalInvest)
                        Notiflix.Block.remove('#salesChart');

                    })
                    .catch(function (error) {

                    });
            }

            $('#js-daterangepicker-invest-predefined').on('apply.daterangepicker', updateInvestHistory);
        </script>
    @endpush
@endif
