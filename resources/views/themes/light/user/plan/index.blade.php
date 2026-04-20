@extends($theme.'layouts.user')
@section('page_title',trans('Investment Plan'))
@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="{{route('user.dashboard')}}">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Investment Plan")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Investment Plan")</h1>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <a href="{{ route('user.plans') }}" class="btn btn-sm {{ empty($assetType) ? 'btn-primary' : 'btn-outline-primary' }}">@lang('All Plans')</a>
                <a href="{{ route('user.plans', ['asset_type' => 'gold']) }}" class="btn btn-sm {{ ($assetType ?? '') === 'gold' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">@lang('Gold Plans')</a>
                <a href="{{ route('user.plans', ['asset_type' => 'btc']) }}" class="btn btn-sm {{ ($assetType ?? '') === 'btc' ? 'btn-info text-dark' : 'btn-outline-info' }}">@lang('BTC Plans')</a>
            </div>
        </div>
        @foreach($plans as $key => $plan)
            <div class="col-lg-3 col-md-6">

                <div class="pricing-single-three card">
                    <div class="card-header text-center">
                        <div class="mb-2">{!! $plan->getAssetBadge() !!}</div>

                        <span class="card-subtitle">@lang($plan->plan_name)</span>
                        <h2 class="card-title display-3 text-dark">
                            <span class="pricing-size">{{$plan->investAmount()}}</span>
                            <span class="fs-6 text-muted">/ {{$plan->getPlanPeriod()}}</span>
                        </h2>
                    </div>

                    <div class="card-body d-flex justify-content-center">
                        <!-- List Checked -->
                        <ul class="list-checked list-checked-primary mb-0">
                            {!! $plan->getDescription() !!}
                        </ul>
                        <!-- End List Checked -->
                    </div>

                    <div class="card-footer border-0 text-center">
                        <div class="d-grid mb-2">
                            <button
                                type="button"
                                class="btn btn-primary investNow"
                                data-id="{{$plan->id}}"
                                data-amount="{{$plan->amount_has_fixed?$plan->plan_price:0}}"
                                data-bs-toggle="modal"
                                data-return="{{$plan->number_of_profit_return?$plan->number_of_profit_return.' '.trans('Times'):trans('Lifetime Earning')}}"
                                data-maturity="{{$plan->maturity}}"
                                data-capital="{!! $plan->getUserCapitalBack() !!}"
                                data-bs-target="#InvestModal"
                            >@lang('Select plan')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="InvestModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalCenterTitle">@lang('Invest Now')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('user.investPlan')}}" method="post">
                    @csrf
                    <div class="modal-body">

                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold-500">@lang('Maturity')</div>
                                </div>
                                <span class="maturity"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold-500">@lang('Number of return')</div>
                                </div>
                                <span class="numberOfReturn"></span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold-500">@lang('Capital Back')</div>
                                </div>
                                <span class="capitalBack"></span>
                            </li>

                        </ul>


                        <div class="mb-3">
                            <label class="modal-label form-label"  for="balance_type">@lang('Select Wallet')</label>
                            <select class="form-control" name="balance_type" data-select2-id="select2-data-1-sjyd"
                                    tabindex="-1" aria-hidden="true">
                                <option
                                    value="balance">@lang('Wallet Balance')
                                    - {{currencyPosition(auth()->guard('web')->user()->balance + 0)}}</option>
                                <option
                                    value="profit">@lang('Profit Balance')
                                    - {{currencyPosition(auth()->guard('web')->user()->profit_balance + 0)}}</option>
                                <option value="checkout">@lang('Checkout')</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="modal-label form-label" for="balance_type">@lang('Enter Amount')</label>
                            <div class="input-group">
                                <input type="text" class="form-control invest-amount" name="amount" id="amount"
                                       value="{{old('amount')}}"
                                       onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" autocomplete="off"
                                       placeholder="@lang('e.g: 100')"/>
                                <span
                                    class="input-group-text show-currency bg-white">{{basicControl()->currency_symbol}}</span>
                            </div>
                        </div>
                        <input type="hidden" name="plan_id" class="plan-id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@lang('Close')<span></span>
                        </button>
                        <button type="submit" class="btn btn-primary planPurchaseSubmitButton">@lang('Make Payment')<span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

        $(document).on('click', '.planPurchaseSubmitButton', function () {
            $(this).attr('disabled', true);
            $('#InvestModal').modal('hide');
            setTimeout(() => {
                $(this).closest('form').submit();
            }, 200);
        })
        $(document).on('click', '.investNow', function () {
            $('.plan-id').val($(this).data('id'));
            let maturity = $(this).data('maturity');
            let numberOfReturn = $(this).data('return');
            let capitalBack = $(this).data('capital');
            let amount = $(this).data('amount');
            $('.maturity').text(`${maturity} ` + '{{trans('Days')}}');
            $('.numberOfReturn').text(`${numberOfReturn}`);
            $('.capitalBack').html(capitalBack);

            if(amount){
                $('#amount').val(amount)
            }else {
                $('#amount').val('')
            }
        })

    </script>
@endpush

@push('style')
    <style>
        .pricing-single .btn-1 {
            border-radius: 500px !important;
        }
    </style>
@endpush

