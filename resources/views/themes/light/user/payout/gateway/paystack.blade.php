@extends($theme.'layouts.user')
@section('page_title',__('Confirm Withdraw'))

@section('content')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang('Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('Confirm Withdraw')</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang('Confirm Withdraw')</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <section class="profile-setting">
                <div class="row g-4 g-lg-5">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>@lang('Confirm Withdraw')</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('user.payout.paystack',$payout->trx_id) }}" method="post">
                                    @csrf

                                    <input type="hidden" class="type" name="type" value="">
                                    <div class="row g-4">
                                        @if ($payoutMethod->supported_currency)
                                            <div class="col-md-12">
                                                <div class="input-box search-currency-dropdown">
                                                    <label class="form-label" for="from_wallet">@lang('Select Bank Currency')</label>
                                                    <input type="text" name="currency_code" placeholder="Selected"
                                                           autocomplete="off" value="{{ $payout->payout_currency_code }}"
                                                           class="form-control transfer-currency @error('currency_code') is-invalid @enderror">

                                                    @error('currency_code')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-12 input-box dynamic-bank mx-1 d-none mb-2">
                                            <label class="form-label">@lang('Select Bank')</label>
                                            <select id="dynamic-bank" name="bank"
                                                    class="form-control js-example-basic-single">
                                            </select>
                                            @error('bank')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @if (isset($payoutMethod->inputForm))
                                            @foreach ($payoutMethod->inputForm as $key => $value)
                                                @if ($value->type == 'text')
                                                    <div class="input-box mt-3">
                                                        <label class="form-label"
                                                               for="{{ $value->field_name }}">@lang(snake2Title($value->field_name))</label>
                                                        <input type="text" name="{{ $value->field_name }}"
                                                               placeholder="{{ __(snake2Title($value->field_name)) }}"
                                                               autocomplete="off"
                                                               value="{{ old(snake2Title($value->field_name)) }}"
                                                               class="form-control @error($value->field_name)) is-invalid @enderror">
                                                        <div class="invalid-feedback">
                                                            @error($value->field_name)
                                                            @lang($message)
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @elseif($value->type == 'textarea')
                                                    <div class="input-box">
                                                        <label class="form-label"
                                                               for="{{ $value->field_name }}">@lang(snake2Title($value->field_name))</label>
                                                        <textarea class="form-control @error($value->field_name) is-invalid @enderror" name="{{ $value->field_name }}"
                                                                  rows="5">{{ old(snake2Title($value->field_name)) }}</textarea>
                                                        <div class="invalid-feedback">
                                                            @error($value->field_name)
                                                            @lang($message)
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @elseif($value->type == 'file')
                                                    <div class="input-box mt-3 mb-4">
                                                        <label class="col-form-label form-label">@lang('Choose File')</label>
                                                        <div id="image-preview" class="image-preview">
                                                            <label for="image-upload"
                                                                   id="image-label">@lang('Choose File')</label>
                                                            <input type="file" name="{{ $value->field_name }}"
                                                                   class="form-control @error($value->field_name) is-invalid @enderror"
                                                                   id="image-upload" />
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            @error($value->field_name)
                                                            @lang($message)
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if ($payoutMethod->automatic_payout_permisstion)
                                            <div class="form-check mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" name="automatic_payout_permisstion"
                                                           type="checkbox" value="1" />
                                                    <label class="form-check-label" for="tandc">
                                                        @lang('Automatic Payout')
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="input-box col-12">
                                            <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>@lang('Transaction Details')</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush list-group-no-gutters">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Payout Method')</span>
                                        <span class="text-info fw-semibold">{{ __($payoutMethod->name) }} </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Request Amount')</span>
                                        <span class="text-dark fw-semibold">{{ getAmount($payout->amount) }}
                                            {{ $payout->payout_currency_code }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Percentage')</span>
                                        <span class="text-dark">{{ getAmount($payout->percentage) }}
                                            {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Charge Percentage')</span>
                                        <span class="text-dark">{{ getAmount($payout->charge_percentage) }}
                                            {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Charge Fixed')</span>
                                        <span class="text-dark">{{ getAmount($payout->charge_fixed) }}
                                            {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Charge')</span>
                                        <span class="text-danger">{{ getAmount($payout->charge) }}
                                            {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Net Amount')</span>
                                        <span class="text-info fw-semibold">{{ getAmount($payout->net_amount) }}
                                            {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Amount In Base Currency')</span>
                                        <span
                                            class="text-success fw-semibold">{{ getAmount($payout->amount_in_base_currency) }}
                                            {{ basicControl()->base_currency }}</span>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection



@push('script')
    <script type="text/javascript">
        'use strict';

        let currency_code = "{{ old('currency_code') }}";
        if (currency_code) {
            getBankForm(currency_code);
        }

        $(document).ready(function() {
            let currencyCode = $(".transfer-currency").val();
            $('.dynamic-bank').addClass('d-none');
            getBankForm(currencyCode);


            $(document).on("change", "#dynamic-bank", function() {
                let type = $(this).find(':selected').data('type');
                $('.type').val(type);
            })

        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getBankForm(currencyCode) {
            $.ajax({
                url: "{{ route('user.payout.getBankList') }}",
                type: "post",
                data: {
                    currencyCode,
                },
                success: function(response) {
                    if (response.data != null) {
                        showBank(response.data)
                    }
                }
            });
        }

        function showBank(bankLists) {
            $('#dynamic-bank').html(``);
            let options = `<option disabled selected>@lang('Select Bank')</option>`;
            for (let i = 0; i < bankLists.length; i++) {
                options +=
                    `<option value="${bankLists[i].code}" data-type="${bankLists[i].type}">${bankLists[i].name}</option>`;
            }
            $('.dynamic-bank').removeClass('d-none');
            $('#dynamic-bank').html(options);
        }
    </script>
@endpush
