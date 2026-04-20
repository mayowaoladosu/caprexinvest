@extends('admin.layouts.app')
@section('page_title',__('Withdraw Log'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="javascript:void(0);">@lang('Dashboard')</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="javascript:void(0);">
                                    @lang('Withdrawals')
                                </a>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Withdrawals')</h1>
                </div>
            </div>
        </div>

        <div class="alert alert-soft-dark mb-5" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <img class="avatar avatar-xl alert_image"
                         src="{{ asset('assets/admin/img/oc-megaphone.svg') }}"
                         alt="Image Description" data-hs-theme-appearance="default">
                    <img class="avatar avatar-xl alert_image"
                         src="{{ asset('assets/admin/img/oc-megaphone-light.svg') }}"
                         alt="Image Description" data-hs-theme-appearance="dark">
                </div>

                <div class="flex-grow-1 ms-3">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 text-body">@lang("This withdrawal request has been created by ".optional($payout->user)->firstname . ' '. optional($payout->user)->lastname.". Their current main balance is ".currencyPosition(optional($payout->user)->balance) . " and profit balance is ".currencyPosition(optional($payout->user)->profit_balance))</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.payout.update',$payout->id) }}" method="post" enctype="multipart/form-data">
            <div class="row">
                @csrf
                <div class="col-lg-8">
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-header-title">@lang('Withdraw information')</h4>
                            <a href="{{ route('admin.payout.log') }}" class="btn btn-primary"
                               id="clearLocalStorageData">@lang("Withdraw List")</a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-4">
                                    <label for="amountLabel" class="form-label">@lang('Amount')
                                        ({{basicControl()->base_currency}})
                                    </label>
                                    <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                           name="amount" id="amountLabel" placeholder="amount"
                                           value="{{ old('amount',$payout->amount_in_base_currency+0) }}" readonly>
                                    @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="charge" class="form-label">@lang('Charge')
                                        ({{basicControl()->base_currency}})
                                    </label>
                                    <input type="text" class="form-control @error('charge') is-invalid @enderror"
                                           name="charge" id="charge" placeholder="amount"
                                           value="{{ old('charge',$payout->charge_in_base_currency+0) }}" readonly>
                                    @error('charge')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="input-box">
                                        <label for="">@lang('Withdraw Method')</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="method"
                                                   value="{{old('method',optional($payout->method)->name)}}"
                                                   class="form-control" placeholder="withdraw method" readonly>
                                        </div>
                                        @error('method')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-box">
                                        <label for="">@lang('Transaction Id')</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="transaction_id"
                                                   value="{{old('transaction_id',$payout->trx_id)}}"
                                                   class="form-control" placeholder="transaction id"
                                                   aria-describedby="basic-addon2" readonly>
                                        </div>
                                        @error('transaction_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                @if(optional($payout->method)->code == 'paypal')
                                    <div class="col-md-6">
                                        <div class="tom-select-custom">
                                            <select class="form-select js-select" name="recipient_type" required>
                                                <option value="" disabled=""
                                                        selected="">@lang('Select Recipient')</option>
                                                <option value="EMAIL">@lang('Email')</option>
                                                <option value="PHONE">@lang('phone')</option>
                                                <option value="PAYPAL_ID">@lang('Paypal Id')</option>
                                            </select>
                                            @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                                @if(isset(optional($payout->method)->inputForm))
                                    @foreach(optional($payout->method)->inputForm as $key => $value)
                                        @if($value->type != 'file')
                                            <div class="col-md-6">
                                                <div class="input-box">
                                                    <label for="">@lang($value->field_label)</label>
                                                    <div class="input-group mb-3">
                                                        <input type="{{$value->type}}" name="{{ $value->field_name }}"
                                                               value="{{old($value->field_name,$payout->information->{$value->field_name}->field_value??'')}}"
                                                               class="form-control" placeholder="{{ __(snake2Title($value->field_name)) }}"
                                                               >
                                                    </div>
                                                    @error($value->field_name)
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($value->type == 'file')
                                            <div class="col-12">
                                                <label for="">@lang($value->field_label)</label>
                                                <label class="form-check form-check-dashed"
                                                       for="{{ $value->field_name }}" id="content_img">
                                                    <img id="contentImg"
                                                         class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                         src="{{isset($payout->information->{$value->field_name})?getFile($payout->information->{$value->field_name}->field_driver,$payout->information->{$value->field_name}->field_value):asset("assets/admin/img/oc-browse-file.svg") }}"
                                                         alt="Image Description"
                                                         data-hs-theme-appearance="default">
                                                    <img id="contentImg"
                                                         class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                         src="{{isset($payout->information->{$value->field_name})?getFile($payout->information->{$value->field_name}->field_driver,$payout->information->{$value->field_name}->field_value):asset('assets/admin/img/oc-browse-file.svg')}}"
                                                         alt="Image Description"
                                                         data-hs-theme-appearance="dark">
                                                    <span
                                                        class="d-block">@lang("Browse your file here")</span>
                                                    <input type="file" name="{{ $value->field_name }}"
                                                           class="js-file-attach form-check-input"
                                                           id="{{ $value->field_name }}"
                                                           data-hs-file-attach-options='{
                                                                      "textTarget": "#contentImg",
                                                                      "mode": "image",
                                                                      "targetAttr": "src",
                                                                      "allowTypes": [".png", ".jpeg", ".jpg"]
                                                                   }'>
                                                </label>
                                                @error($value->field_name)
                                                <span class="invalid-feedback d-block" role="alert">
                                            {{ $message }}
                                            </span>
                                                @enderror
                                            </div>

                                            @error("thumbnail")
                                            <span class="invalid-feedback d-block" role="alert">
                                            {{ $message }}
                                            </span>
                                            @enderror
                                        @endif
                                    @endforeach
                                @endif
                            </div>

                        </div>
                        <!-- Body -->
                    </div>

                    <div class="card  mb-5">
                        <div class="card-header">
                            <h5 class="card-header-title">@lang('Feedback')</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <textarea class="summernote" name="feedback"> {{old('feedback',$payout->feedback)}}</textarea>
                                    @error('feedback')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">@lang('Status')</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="tom-select-custom">
                                    <select class="form-select js-select" name="status">
                                        <option value="2" @selected($payout->status == 2)>@lang('Approved')</option>
                                        <option value="3" @selected($payout->status == 3)>@lang('Rejected')</option>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-5">
                        <div class="card-header bg-white">
                            <h4 class="card-header-title">@lang('Publish')</h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <button class="btn btn-primary mb-3" type="submit" name="save_status"
                                        value="1">@lang('Save')</button>
                                <button class="btn btn-info ms-3 mb-3" type="submit" name="save_status"
                                        value="2">@lang('Save & Exit')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-bs5.min.css') }}">
@endpush
@push('js-lib')
    <script src="{{ asset('assets/admin/js/summernote-bs5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/hs-file-attach.min.js') }}"></script>
@endpush

@push('script')
    <script>


        (function () {
            new HSFileAttach('.js-file-attach')
        })();

        $(document).ready(function () {
            $('.summernote').summernote({
                height: 200,
                callbacks: {
                    onBlurCodeview: function () {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });

        })
    </script>
@endpush



