@extends(template().'layouts.user')
@section('page_title','Withdraw')
@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Withdraw")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Withdraw")</h1>
            </div>
        </div>
    </div>
    <form action="{{ route('user.payout.request') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('Select Withdraw Method')</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-scroll">
                            <div class="payment-list">
                                @foreach($payoutMethod as $method)
                                    <label class="payment-method selectPayoutMethod">
                                        <input type="radio" name="payout_method_id" value="{{ $method->id }}" class="selectPayoutMethod">
                                        <div class="payment-content">
                                            <div class="gateway-img">
                                                <img src="{{ getFile($method->driver,$method->logo) }}" class="img-fluid">
                                            </div>
                                            <div class="gateway-content">
                                                <h5>{{ $method->name }}</h5>
                                                {{$method->description}}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header ">
                        <h5 class="mb-0">@lang('Withdraw Summary')</h5>
                    </div>
                    <div class="card-body">
                        <!-- Currency Exchange Section -->

                        <div class="mb-3">
                            <label class="form-label">@lang('Balance Type')</label>
                            <select class="form-select"  name="balance_type">
                                <option value="balance">@lang('Wallet Balance')
                                    - {{currencyPosition(auth()->guard('web')->user()->balance)}}</option>
                                <option value="profit_balance">@lang('Profit Balance')
                                    - {{currencyPosition(auth()->guard('web')->user()->profit_balance)}}</option>
                            </select>
                            <span class="text-danger d-block currency-field"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Select Currency')</label>
                            <select class="form-select supported_currency" id="supported_currency" name="supported_currency">
                                <option value="">@lang('Select Currency')</option>
                            </select>
                            <span class="text-danger d-block currency-field"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Enter Amount')</label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount" step="0.0001">
                            <span class="text-danger d-block required-amount"></span>
                        </div>

                        <ul class="list-group list-group-flush">

                        </ul>

                        <button class="btn btn-dark w-100 mt-3">@lang('Withdraw')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('script')
    <script>
        'use strict';

        let amountField = $('#amount');
        let amountStatus = false;
        var selectedPayoutMethod;

        function clearMessage(fieldId) {
            $(fieldId).removeClass('is-valid')
            $(fieldId).removeClass('is-invalid')
            $(fieldId).closest('div').find(".invalid-feedback").html('');
            $(fieldId).closest('div').find(".is-valid").html('');
            $(fieldId).closest('div').find(".required-amount").html('');
        }

        $(document).on('change', '.selectPayoutMethod', function () {
            let id = $(this).val();

            if(!id){
                return;
            }
            selectedPayoutMethod = id;
            supportCurrency(id);
        });

        function supportCurrency(selectedPayoutMethod) {
            if (!selectedPayoutMethod) {
                console.error('Selected Gateway is undefined or null.');
                return;
            }

            $('#supported_currency').empty();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('user.payout.supported.currency') }}",
                data: {gateway: selectedPayoutMethod},
                type: "GET",
                success: function (data) {

                    if (data === "") {
                        let markup = `<option value="USD">USD</option>`;
                        $('#supported_currency').append(markup);
                    }

                    let markup = '<option value="">Selected Currency</option>';
                    $('#supported_currency').append(markup);

                    $(data).each(function (index, value) {

                        let markup = `<option value="${value}">${value}</option>`;
                        $('#supported_currency').append(markup);
                    });
                },
                error: function (error) {
                    console.error('AJAX Error:', error);
                }
            });
        }


        $(document).on('change, input', "#amount, #supported_currency, .selectPayoutMethod", function (e) {

            console.log(selectedPayoutMethod);
            let amount = amountField.val();
            let selectedCurrency = $('#supported_currency').val();
            let currency_type = 1;

            if (!isNaN(amount) && amount > 0) {

                let fraction = amount.split('.')[1];
                let limit = currency_type == 0 ? 8 : 2;


                if (fraction && fraction.length > limit) {
                    amount = (Math.floor(amount * Math.pow(10, limit)) / Math.pow(10, limit)).toFixed(limit);
                    amountField.val(amount);
                }

                checkAmount(amount, selectedCurrency, selectedPayoutMethod)


            } else {
                clearMessage(amountField)
                $('.list-group').html('')
            }
        });


        function checkAmount(amount, selectedCurrency, selectedPayoutMethod) {

            $.ajax({
                method: "GET",
                url: "{{ route('user.payout.checkAmount') }}",
                dataType: "json",
                data: {
                    'amount': amount,
                    'selected_currency': selectedCurrency,
                    'selected_payout_method': selectedPayoutMethod,
                }
            }).done(function (response) {
                let amountField = $('#amount');
                if (response.status) {
                    clearMessage(amountField);
                    $(amountField).addClass('is-valid');
                    $(amountField).closest('div').find(".valid-feedback").html(response.message);
                    amountStatus = true;
                    let base_currency ="{{basicControl()->base_currency}}"
                    showCharge(response, base_currency);
                } else {
                    amountStatus = false;
                    $('.list-group').html('')
                    clearMessage(amountField);
                    $(amountField).addClass('is-invalid');
                    $(amountField).closest('div').find(".required-amount").html(response.message);
                }


            });
        }


        function showCharge(response, currency) {
            let txnDetails = `<li class="list-group-item d-flex justify-content-between">
							<span>{{ __('Amount In') }} ${response.currency} </span>
							<span class="text-success"> ${response.amount} ${response.currency}</span>
						</li>

						<li class="list-group-item d-flex justify-content-between">
							<span>{{ __('Charge') }}</span>
							<span class="text-danger">  ${response.charge} ${response.currency}</span>
						</li>


						<li class="list-group-item d-flex justify-content-between">
							<span>{{ __('Payout Amount') }}</span>
							<span class="text-info"> ${response.net_payout_amount} ${response.currency}</span>
						</li>

						<li class="list-group-item d-flex justify-content-between">
							<span>{{ __('Exchange Rate') }}</span>
							<span class="text-info"> 1 ${currency} <i class="fa-light fa-arrow-right-arrow-left fa-sm"></i>  ${response.conversion_rate} ${response.currency}</span>
						</li>


						<li class="list-group-item d-flex justify-content-between">
							<span>{{ __('Payout Amount') }} <sub>(In Base Currency)</sub></span>
							<span class="text-info"> ${response.amount_in_base_currency} ${currency}</span>
						</li>`;
            $('.list-group').html(txnDetails)
        }

    </script>
@endpush

@push('style')
    <style>
        .payment-method {
            display: block;
            margin: auto;
            text-align: center;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 8px;
            cursor: pointer;
            width: 95%;
            transition: all 0.3s ease-in-out;
        }

        .payment-list{
            height: 500px;
            overflow: auto;
            padding-right: 15px;
        }
        .payment-list::-webkit-scrollbar {
            width: 6px;
        }
        .payment-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .payment-list::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 5px;
        }

        .payment-method:hover, .payment-method input:checked + .payment-content {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -4px rgba(0, 0, 0, .1);
        }
        .payment-method:hover{
            transform: scaleX(1.05);
        }

        .payment-content img{
            width: 50px;
            height: 50px;
            min-width: 50px;
            min-height: 50px;
            border-radius: 50%;
        }

        .payment-method input {
            display: none;
        }

        .payment-content {
            display: flex ;
            align-items: center;
            width: 100%;
            height: 100%;
            padding: 15px;
            cursor: pointer;
            border-radius: 5px;
            gap: 15px;
            text-align: start;
        }

        .payment-content .gateway-content{
            max-width: 400px;
            padding-right: 30px;
        }

        .payment-content .gateway-content h5{
            font-size: 16px;
        }

        .payment-content img {
            height: 50px;
            max-width: 100%;
            margin-bottom: 8px;
        }

        .payment-content span {
            font-weight: 500;
            color: #333;
        }

        .gateway-description {
            opacity: .7;
            font-size: 15px;
            font-weight: 500;
            line-height: normal;
            margin-top: 7px;
            margin-bottom: 25px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush





