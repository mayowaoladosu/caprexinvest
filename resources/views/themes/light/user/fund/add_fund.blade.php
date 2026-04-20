@extends(template().'layouts.user')
@section('page_title',trans('Deposit'))
@section('content')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Deposit")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Deposit")</h1>
            </div>
        </div>
    </div>
    <form action="{{route('payment.request')}}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('Select Payment Method')</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-scroll">
                            <div class="payment-list">
                                @foreach($gateways as $method)
                                    <label class="payment-method selectPayment">
                                        <input type="radio" name="gateway_id" value="{{ $method->id }}" class="selectPayment">
                                        <div class="payment-content">
                                            <div class="gateway-img">
                                                <img src="{{ getFile($method->driver,$method->image) }}" class="img-fluid">
                                            </div>
                                            <div class="gateway-content">
                                                <h5>{{ $method->name }}</h5>
                                                <span class="gateway-description">@lang($method->description)</span>
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
                        <h5 class="mb-0">@lang('Payment Summary')</h5>
                    </div>
                    <div class="card-body">
                        <!-- Currency Exchange Section -->
                        <div class="mb-3">
                            <label class="form-label">@lang('Select Currency')</label>
                            <select class="form-select supported_currency">
                                <option value="">@lang('Select Currency')</option>
                            </select>
                            <span class="text-danger d-block currency-field"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Enter Amount')</label>
                            <input type="number" name="amount" class="form-control amount_field" placeholder="@lang('Enter amount')" step="0.0001">
                            <span class="text-danger d-block required-amount"></span>
                        </div>

                        <ul class="list-group list-group-flush">

                        </ul>

                        <button class="btn btn-dark w-100 mt-3">@lang('Make Payment')</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection

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
@push('script')
    <script>

        var gateway_id;
        $(document).on('click', '.selectPayment', function () {
            let id = $(this).val();

            if(!id){
                return;
            }
            gateway_id = id;
            $.ajax({
                url: "{{ route('supported.currency') }}",
                data: {
                    gateway: id
                },
                type: "GET",
                success: function (response) {
                    let select = $('.supported_currency');
                    select.empty();
                    if (response.data === "") {
                        select.attr('name', 'supported_currency');
                        let markup = `<option value="USD">USD</option>`;
                        select.append(markup);
                    }
                    if (Number(response.currencyType) === 1) {
                        select.attr('name', 'supported_currency');
                        let markup = '<option value="">Select Currency</option>';
                        select.append(markup);
                        $(response.data).each(function (index, value) {
                            let markup = `<option value="${value}">${value}</option>`;
                            select.append(markup);
                        });
                    }
                    if (Number(response.currencyType) === 0) {
                        select.attr('name', 'supported_crypto_currency');
                        let markup2 = '<option value="">Select Crypto Currency</option>';
                        select.append(markup2);
                        $(response.data).each(function (index, value) {
                            let markupOption = `<option value="${value}">${value}</option>`;
                            select.append(markupOption);
                        });
                    }
                },
                error: function (error) {
                    console.error('AJAX Error:', error);
                }
            })
        });

        $(document).on('change , input', '.supported_currency, .amount_field', function () {
            if ($('.supported_currency').attr('name') === 'supported_currency') {
                validation($('.supported_currency').val(), null)
            } else {
                validation(null, $('.supported_currency').val())
            }
        })

        function validation(currency, cryptoCurrency) {
            let amount = Number($('.amount_field').val());
            if(!amount){
                $('.required-amount').text('Amount field is required')
                return;
            }else {
                $('.required-amount').text('')
            }

            if(currency == null && cryptoCurrency == null){
                $('.currency-field').text("Currency field is required");
                return;
            }else {
                $('.currency-field').text("");
            }


            $.ajax({
                method: 'GET',
                url: "{{ route('deposit.checkAmount') }}",
                dataType: "json",
                data: {
                    'amount': amount,
                    'selected_currency': currency,
                    'select_gateway': gateway_id,
                    'selectedCryptoCurrency': cryptoCurrency,
                },
                success: function (response) {
                    if (response.status) {
                        let base_currency = "{{basicControl()->base_currency}}"
                        generatePaymentSummary(response, base_currency);
                    } else {
                        $('.errorMessage').text(response.message)
                    }
                }
            })
        }

        function generatePaymentSummary(response, currency) {
            let html = `<li class="list-group-item d-flex justify-content-between">
                          <span> {{ __('Amount In') }} ${response.currency}</span>
                           <span>${response.amount} ${response.currency}</span>
                       </li>
                        <li class="list-group-item d-flex justify-content-between">
                        <span >  {{ __('Charge') }}</span>

                        <span class="text-danger">${response.charge} ${response.currency}</span>
                       </li>
                        <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('Payable Amount') }}</span>

                            <span >${response.payable_amount} ${response.currency}</span>
                       </li>
                       <li class="list-group-item d-flex justify-content-between">
                       <span>{{ __('Payable Amount') }} <sub>(In Base Currency)</sub></span>

                            <span>${response.amount_in_base_currency} ${currency}</span>
                       </li> `

            $('.list-group').html(html)
        }
    </script>
@endpush
