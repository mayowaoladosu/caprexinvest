@if (hasEcommerce())
    <div class="col-12">
        <div class="card mb-3 mb-lg-5">
            <div class="card-header card-header-content-sm-between">
                <h4 class="card-header-title mb-2 mb-sm-0">@lang('Product Sales') </h4>
                <div class="d-grid d-sm-flex gap-2">

                    <button id="js-daterangepicker-predefined" class="btn btn-white btn-sm dropdown-toggle">
                        <i class="bi-calendar-week"></i>
                        <span class="js-daterangepicker-predefined-preview ms-1"></span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row col-lg-divider" id="salesRevenueChart">
                    <div class="col-lg-9 mb-5 mb-lg-0">
                        <div class="chartjs-custom mb-4">
                            <canvas id="ecommerce-sales-revenue" style="height: 300px"></canvas>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-sm-6 col-lg-12">
                                <div class="d-flex justify-content-center flex-column" style="min-height: 9rem;">
                                    <h6 class="card-subtitle">Revenue</h6>
                                    <span
                                        class="d-block display-4 text-dark mb-1 me-3 revenueData">{{ currencyPosition(0) }}</span>
                                </div>

                                <hr class="d-none d-lg-block my-0">
                            </div>

                            <div class="col-sm-6 col-lg-12">
                                <div class="d-flex justify-content-center flex-column" style="min-height: 9rem;">
                                    <h6 class="card-subtitle">Orders</h6>
                                    <span class="d-block display-4 text-dark mb-1 me-3 orderData">{{ 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('css-lib')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/tom-select.bootstrap5.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/global/css/daterangepicker.css') }}">
    @endpush

    @push('js-lib')
        <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
        <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
    @endpush

    @push('script')
        <script>
            $(document).on('ready', function() {
                HSCore.components.HSTomSelect.init('.js-select');
                $('.js-daterangepicker').daterangepicker();

                $('.js-daterangepicker-times').daterangepicker({
                    timePicker: true,
                    startDate: moment().startOf('hour'),
                    endDate: moment().startOf('hour').add(32, 'hour'),
                    locale: {
                        format: 'M/DD hh:mm A'
                    }
                });

                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(
                        start.format('MMM D') + ' - ' + end.format('MMM D, YYYY')
                    );
                    $('#js-daterangepicker-predefined-country .js-daterangepicker-predefined-preview-country').html(
                        start.format('MMM D') + ' - ' + end.format('MMM D, YYYY')
                    );
                }

                $('#js-daterangepicker-predefined').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    }
                }, cb);

                $('#js-daterangepicker-predefined-country').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    }
                }, cb);

                cb(start, end);

                // Initialize HSChartJS and store Chart instance
                const chartElement = document.getElementById('ecommerce-sales-revenue');
                HSCore.components.HSChartJS.init(chartElement, {
                    type: 'line', // 👈 Changed from 'bar' to 'line'
                    data: {
                        labels: [],
                        datasets: []
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e7eaf3',
                                    drawBorder: false
                                },
                                ticks: {
                                    stepSize: 100,
                                    color: '#97a4af',
                                    font: {
                                        size: 12,
                                        family: 'Open Sans, sans-serif'
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
                                    color: '#97a4af',
                                    font: {
                                        size: 12,
                                        family: 'Open Sans, sans-serif'
                                    },
                                    padding: 5
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                hasIndicator: true,
                                lineMode: true,
                                postfix: ''
                            },
                            legend: {
                                labels: {
                                    font: {
                                        family: 'Open Sans, sans-serif'
                                    },
                                    color: '#1e2022'
                                }
                            }
                        },
                        elements: {
                            line: {
                                tension: 0.4, // 👈 Smooth curves
                                borderWidth: 3
                            },
                            point: {
                                radius: 5,
                                hoverRadius: 7,
                                backgroundColor: '#fff',
                                borderWidth: 2
                            }
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        }
                    }
                });

                // ✅ Store actual chart instance
                const chartInstance = Chart.getChart(chartElement);

                function fetchDataAndUpdateChart() {
                    const selectedStore = $('.js-select').val();
                    const dateRange = $('#js-daterangepicker-predefined').data('daterangepicker');
                    const startDate = dateRange.startDate.format('YYYY-MM-DD');
                    const endDate = dateRange.endDate.format('YYYY-MM-DD');

                    $.ajax({
                        url: '{{ route('admin.get.salesRevenueHistory') }}',
                        method: 'GET',
                        data: {
                            store: selectedStore,
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(data) {
                            if (!chartInstance) return;

                            // Update Chart Data
                            chartInstance.data.labels = data.labels || [];
                            chartInstance.data.datasets = data.datasets || [];
                            chartInstance.update();

                            // Totals
                            let totalEarned = data.datasets[0]?.data.reduce((a, b) => a + parseFloat(b), 0) || 0;

                            let totalOrders = data.datasets[1]?.data.reduce((a, b) => a + parseFloat(b), 0) || 0;

                            document.querySelector('.revenueData').innerHTML = currencyPosition(
                                totalEarned);
                            document.querySelector('.orderData').innerHTML = totalOrders;
                        },
                        error: function(error) {
                            console.error('Error fetching chart data:', error);
                        }
                    });
                }

                function currencyPosition(amount) {
                    var currencyPosition = @json(basicControl()->is_currency_position);
                    var hasSpace = @json(basicControl()->has_space_between_currency_and_amount);
                    var currencySymbol = @json(basicControl()->currency_symbol);
                    var baseCurrency = @json(basicControl()->base_currency);
                    amount = parseFloat(amount).toFixed(2);

                    if (currencyPosition === 'left' && hasSpace) {
                        return currencySymbol + '  ' + amount;
                    } else if (currencyPosition === 'left' && !hasSpace) {
                        return currencySymbol + ' ' + amount;
                    } else if (currencyPosition === 'right' && hasSpace) {
                        return amount + '  ' + baseCurrency;
                    } else {
                        return amount + '  ' + baseCurrency;
                    }
                }

                // Event listeners
                $('.js-select').on('change', fetchDataAndUpdateChart);
                $('#js-daterangepicker-predefined').on('apply.daterangepicker', fetchDataAndUpdateChart);

                // Initial load
                fetchDataAndUpdateChart();
            });
        </script>
    @endpush
@endif
