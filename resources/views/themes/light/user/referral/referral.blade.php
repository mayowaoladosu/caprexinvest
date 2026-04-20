@extends($theme.'layouts.user')
@section('page_title',trans('Referral'))
@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang('Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('Referral')</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang('Referral')</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row mb-3">
                <div class="col-xl-6 col-lg-12 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="commission d-flex align-items-center justify-content-between">
                                        <div> <h5>@lang('Total Commission')</h5>
                                            <p>{{currencyPosition(auth()->user()->total_commission)}}</p></div>
                                        <div><i class="fa-duotone fa-sack-dollar"></i></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="commission d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5>@lang('Level')</h5>
                                            <p>{{count($directReferralUsers)}}</p>
                                        </div>
                                        <div><i class="fa-light fa-user-plus"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="qna">
                                <h4>@lang('Want to get more commission')?</h4>
                                <p>@lang('Refer your friends and earn more commission')</p>
                            </div>
                            <div class=" share_link d-flex align-items-center">
                                <i class="fa-light fa-share-nodes"></i>
                                <input type="text" class="form-control border-0"  id="referralURL" value="{{route('register',['ref' => auth()->id()])}}" readonly>
                                <button class="copy_btn" onclick="copyFunction()"><i class="fa-regular fa-copy"></i></button>
                            </div>
                            <p class="refurlText">@lang('Copy this url and share with others')</p>

                            <ul id="socialShare">

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12">
                    <div class="card " id="depositPayoutHistoryCard">
                        <div class="card-header bg-white">
                            <h4 class="card-header-title">
                                @lang('Referral Bonus')
                            </h4>
                        </div>
                        <div class="card-box" >
                            <div  id="depositPayoutHistory"></div>
                        </div>
                    </div>

                </div>
            </div>
            @if(0 < count($directReferralUsers))
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive datatable-custom">
                            <table class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">@lang('Username')</th>
                                    <th scope="col">@lang('Level')</th>
                                    <th scope="col">@lang('Email')</th>
                                    <th scope="col">@lang('Phone Number')</th>
                                    <th scope="col">@lang('Joined At')</th>
                                </tr>
                                </thead>
                                <tbody class="block-statistics">
                                @foreach($directReferralUsers as $user)
                                    <tr id="user-{{ $user->id }}" data-level="0" data-loaded="false">
                                        <td data-label="@lang('Username')">
                                            <a href="javascript:void(0)"
                                               class="text-inherit {{ count(getDirectReferralUsers($user->id)) > 0 ? 'nextDirectReferral' : '' }}"
                                               data-id="{{ $user->id }}"
                                            >
                                                @if(count(getDirectReferralUsers($user->id)) > 0)
                                                    <i class="far fa-circle-down text-primary"></i>
                                                @endif
                                                @lang($user->username)
                                            </a>
                                        </td>
                                        <td data-label="@lang('Level')">
                                            @lang('Level 1')
                                        </td>
                                        <td data-label="@lang('Email')" class="">{{$user->email}}</td>
                                        <td data-label="@lang('Phone Number')">
                                            {{$user->phone}}
                                        </td>
                                        <td data-label="@lang('Joined At')">
                                            {{dateTime($user->created_at)}}
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection


@push('script')
    <script src="{{asset($themeTrue.'js/custom_share.js')}}"></script>
    <script src="{{asset($themeTrue.'js/apexcharts.min.js')}}"></script>
    <script>
        'use strict'
        $(document).on('click', '.nextDirectReferral', function () {
            let _this = $(this);
            let parentRow = _this.closest('tr');

            // Check if the downline is already loaded
            if (parentRow.data('loaded')) {
                return;
            }

            getDirectReferralUser(_this);
        });

        function getDirectReferralUser(_this) {

            Notiflix.Block.standard('.block-statistics');

            let userId = _this.data('id');
            let parentRow = _this.closest('tr');
            let currentLevel = parseInt(parentRow.data('level')) + 1;
            let downLabel = currentLevel + 1;

            setTimeout(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('user.myGetDirectReferralUser') }}",
                    method: 'POST',
                    data: {
                        userId: userId,
                    },
                    success: function (response) {

                        Notiflix.Block.remove('.block-statistics');
                        let directReferralUsers = response.data;

                        let referralData = '';

                        directReferralUsers.forEach(function (directReferralUser) {
                            referralData += `
                        <tr id="user-${directReferralUser.id}" data-level="${currentLevel}">
                            <td data-label="@lang('Username')" style="padding-left: ${currentLevel * 35}px;">
                                <a class="${directReferralUser.count_direct_referral > 0 ? 'nextDirectReferral' : ''}" href="javascript:void(0)" style="border-bottom: none !important;" data-id="${directReferralUser.id}">
                                    ${directReferralUser.count_direct_referral > 0 ? ' <i class="far fa-circle-down color-primary"></i>' : ''}
                                    ${directReferralUser.username}
                                </a>
                            </td>

                            <td data-label="@lang('Level')">
                                 <span class="text-dark">Level ${downLabel}</span>
                            </td>

                            <td data-label="@lang('Email')">
                                ${directReferralUser.email ? directReferralUser.email : '-'}
                            </td>
                            <td data-label="@lang('Phone Number')">
                                 ${directReferralUser.phone??'-'}
                            </td>

                            <td data-label="Joined At">
                                ${directReferralUser.joined_at}
                            </td>
                            </tr>`;
                        });

                        // Mark this row as having its downline loaded
                        parentRow.data('loaded', true);

                        $(`#user-${userId}`).after(referralData);
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            }, 100);
        }


    </script>
    <script>
        $('#socialShare').socialSharingPlugin({
            url: '{{route('register',['ref' => auth()->id()])}}',
            title: 'join and enjoy',
            description: $('meta[property="og:description"]').attr('content'),
            img: $('meta[property="og:image"]').attr('content'),
            enable: [ 'facebook','whatsapp', 'twitter','email', 'linkedin', 'pinterest','reddit','telegram']
        });

        function copyFunction() {
            var copyText = document.getElementById("referralURL");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            /*For mobile devices*/
            document.execCommand("copy");
            Notiflix.Notify.success(`Copied: ${copyText.value}`);
        }

        $(document).ready(function () {
            Notiflix.Block.standard('#depositPayoutHistoryCard');

            // Detect dark mode
            function isDarkMode() {
                return HSThemeAppearance.getOriginalAppearance() === 'dark';
            }

            // Function to get chart options based on mode
            function getChartOptions(darkMode) {
                return {
                    series: [{
                        name: "Bonus",
                        data: []
                    }],
                    chart: {
                        height: 410,
                        type: 'line',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        foreColor: darkMode ? '#e0e0e0' : '#333', // text color
                        background: 'transparent' // keep background transparent so it adapts
                    },
                    theme: {
                        mode: darkMode ? 'dark' : 'light'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    grid: {
                        borderColor: darkMode ? '#555' : '#ddd',
                        row: {
                            colors: darkMode ? ['#333', 'transparent'] : ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        }
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        labels: {
                            style: {
                                colors: darkMode ? '#ccc' : '#333'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: darkMode ? '#ccc' : '#333'
                            }
                        }
                    },
                    colors: ['#5B8FF9', '#6C757D'],
                    tooltip: {
                        theme: darkMode ? 'dark' : 'light',
                        y: {
                            formatter: function (val) {
                                return currencyPosition(val);
                            }
                        }
                    }
                };
            }

            // Create chart with initial mode
            let depositPayoutChart = new ApexCharts(
                document.querySelector("#depositPayoutHistory"),
                getChartOptions(isDarkMode())
            );
            depositPayoutChart.render();

            // Fetch data
            depositPayoutHistory();

            async function depositPayoutHistory() {
                await axios.get('{{route('user.referral.bonus.history')}}')
                    .then(function (response) {
                        let newSeries = [{
                            name: 'Bonus',
                            data: response.data.referrals
                        }];
                        depositPayoutChart.updateSeries(newSeries);
                        Notiflix.Block.remove('#depositPayoutHistoryCard');
                    })
                    .catch(function (error) {

                    });
            }

            // Listen for theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                let darkMode = e.matches;
                depositPayoutChart.updateOptions(getChartOptions(darkMode));
            });
        });


    </script>
@endpush
