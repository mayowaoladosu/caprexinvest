@extends($theme.'layouts.user')
@section('page_title',__('Confirm Withdraw'))

@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="{{route('user.dashboard')}}">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active"
                            aria-current="page">@lang("Confirm Withdraw")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Confirm Withdraw")</h1>
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
                                <form action="{{ route('user.payout.confirm',$payout->trx_id) }}" method="post"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-4">
                                        @if($payoutMethod->supported_currency)
                                            <div class="col-md-12">
                                                <div class="input-box search-currency-dropdown">
                                                    <label class="form-label"
                                                           for="from_wallet">@lang('Select Bank Currency')</label>
                                                    <input type="text" name="currency_code"
                                                           placeholder="Selected"
                                                           autocomplete="off"
                                                           value="{{ $payout->payout_currency_code }}"
                                                           class="form-control transfer-currency @error('currency_code') is-invalid @enderror">

                                                    @error('currency_code')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        @if($payoutMethod->code == 'paypal')
                                            <div class="row">
                                                <div class="col-md-12 mt-4">
                                                    <div class="form-group search-currency-dropdown">
                                                        <label class="form-label"
                                                               for="from_wallet">@lang('Select Recipient Type')</label>
                                                        <select id="from_wallet" name="recipient_type"
                                                                class="form-control form-control-sm" required>
                                                            <option value="" disabled=""
                                                                    selected="">@lang('Select Recipient')</option>
                                                            <option value="EMAIL">@lang('Email')</option>
                                                            <option value="PHONE">@lang('phone')</option>
                                                            <option
                                                                value="PAYPAL_ID">@lang('Paypal Id')</option>
                                                        </select>
                                                        @error('recipient_type')
                                                        <span class="text-danger">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endif


                                        @if(isset($payoutMethod->inputForm))

                                            @foreach($payoutMethod->inputForm as $key => $value)
                                                @if($value->type == 'text')
                                                    <div class="input-box mt-3">
                                                        <label class="form-label"
                                                               for="{{ $value->field_name }}">@lang($value->field_label)</label>
                                                        <input type="text" name="{{ $value->field_name }}"
                                                               placeholder="{{ __(snake2Title($value->field_name)) }}"
                                                               autocomplete="off"
                                                               value="{{ old(snake2Title($value->field_name)) }}"
                                                               class="form-control @error($value->field_name) is-invalid @enderror">
                                                        <div class="invalid-feedback">
                                                            @error($value->field_name) @lang($message) @enderror
                                                        </div>
                                                    </div>
                                                @elseif($value->type == 'textarea')
                                                    <div class="input-box">
                                                        <label class="form-label"
                                                               for="{{ $value->field_name }}">@lang($value->field_label)</label>
                                                        <textarea
                                                            class="form-control @error($value->field_name) is-invalid @enderror"
                                                            name="{{$value->field_name}}"
                                                            rows="5">{{ old($value->field_name) }}</textarea>
                                                        <div
                                                            class="invalid-feedback">@error($value->field_name) @lang($message) @enderror</div>
                                                    </div>
                                                @elseif($value->type == 'file')
                                                    <div class="input-box mt-3 mb-4">
                                                        <div id="image-preview" class="image-preview">
                                                            <label class="form-label" for="image-upload"
                                                                   id="image-label">@lang($value->field_label)</label>
                                                            <input type="file" name="{{ $value->field_name }}"
                                                                   class="form-control @error($value->field_name) is-invalid @enderror"
                                                                   id="image-upload"/>
                                                        </div>
                                                        <div class="invalid-feedback d-block">
                                                            @error($value->field_name) @lang($message) @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="input-box col-12">
                                            <button type="submit"
                                                    class="btn btn-primary">@lang('Confirm')</button>
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
                                        <span
                                            class="text-info fw-semibold">{{ __($payoutMethod->name) }} </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Request Amount')</span>
                                        <span
                                            class="text-dark fw-semibold">{{ (getAmount($payout->amount)) }} {{ $payout->payout_currency_code }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Percentage')</span>
                                        <span
                                            class="text-dark">{{ (getAmount($payout->percentage)) }} {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Charge Percentage')</span>
                                        <span
                                            class="text-dark">{{ (getAmount($payout->charge_percentage)) }} {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Charge Fixed')</span>
                                        <span
                                            class="text-dark">{{ (getAmount($payout->charge_fixed)) }} {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Charge')</span>
                                        <span
                                            class="text-danger">{{ (getAmount($payout->charge)) }} {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Net Amount')</span>
                                        <span
                                            class="text-info fw-semibold">{{ (getAmount($payout->net_amount)) }} {{ $payout->payout_currency_code }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Amount In Base Currency')</span>
                                        <span
                                            class="text-success fw-semibold">{{ (getAmount($payout->amount_in_base_currency)) }} {{ basicControl()->base_currency }}</span>
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

@push('extra_scripts')
    <script src="{{ asset('assets/dashboard/js/jquery.uploadPreview.min.js') }}"></script>
@endpush
@push('script')
    <script>
        'use strict'
        $(document).on("change",'#image-upload',function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    </script>
@endpush
@push('style')
    <style>

        .image-input {
            position: relative;
            width:30%;
            padding: 20px;
            border: 2px dashed #ddd;
            cursor: pointer;
        }

        .image-input input#image-upload {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .image-input #image-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .image-input #image-label i {
            font-size: 24px;
            color: var(--primary-color);
            opacity: 0.5;
        }
    </style>
@endpush
@push('script')
    <script type="text/javascript">

    </script>

    @if ($errors->any())
        @php
            $collection = collect($errors->all());
            $errors = $collection->unique();
        @endphp
        <script>
            "use strict";
            @foreach ($errors as $error)
            Notiflix.Notify.failure("{{ trans($error) }}");
            @endforeach
        </script>
    @endif
@endpush
