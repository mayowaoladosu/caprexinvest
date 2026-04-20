@extends(template().'layouts.user')
@section('page_title',__('Two Step Security'))

@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Two Step Security")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Two Step Security")</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                @if(auth()->user()->two_fa == 1)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header  d-flex flex-wrap justify-content-between align-items-center">
                                <h5 class="mb-2">@lang("Two Factor Authenticator")</h5>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#regenerateModal" class="btn btn-white">
                                    @lang("Regenerate")</button>
                            </div>
                            <div class="card-body">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="tooltipExample" class="form-control" value="{{$secret}}">

                                    <a class="js-clipboard input-group-append input-group-text" href="javascript:;" data-bs-toggle="tooltip" title="Copy to clipboard!"
                                       data-hs-clipboard-options='{
                                               "type": "tooltip",
                                               "successText": "Copied!",
                                               "contentTarget": "#tooltipExample"
                                             }'>
                                        <i class="bi-clipboard"></i>
                                    </a>
                                </div>
                                <div class="mx-auto text-center mt-3 mb-2">
                                    <img class="w-40 mx-auto"
                                         src="https://quickchart.io/chart?cht=qr&chs=150x150&chl={{$qrCodeUrl}}">
                                </div>

                                <div class="form-group mx-auto text-center mt-3">
                                    <a href="javascript:void(0)" class="w-100 btn btn-primary"
                                       data-bs-toggle="modal"
                                       data-bs-target="#disableModal">@lang('Disable Two Factor Authenticator')</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header  d-flex flex-wrap justify-content-between align-items-center">
                                <h5 class="mb-2">@lang("Two Factor Authenticator")</h5>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#regenerateModal" class="btn btn-white">@lang("Regenerate")</button>
                            </div>
                            <div class="card-body">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="tooltipExample" class="form-control" value="{{$secret}}">

                                    <a class="js-clipboard input-group-append input-group-text" href="javascript:;" data-bs-toggle="tooltip" title="Copy to clipboard!"
                                       data-hs-clipboard-options='{
                                               "type": "tooltip",
                                               "successText": "Copied!",
                                               "contentTarget": "#tooltipExample"
                                             }'>
                                        <i class="bi-clipboard"></i>
                                    </a>
                                </div>
                                <div class="mx-auto text-center mt-3 mb-2">
                                    <img class="w-40 mx-auto"
                                         src="https://quickchart.io/chart?cht=qr&chs=150x150&chl={{$qrCodeUrl}}">
                                </div>

                                <div class="form-group mx-auto text-center mt-3">
                                    <a href="javascript:void(0)" class="btn btn-primary w-100"
                                       data-bs-toggle="modal"
                                       data-bs-target="#enableModal">@lang('Enable Two Factor Authenticator')</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-6 text-center">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-uppercase my-3">@lang('Use Google Authenticator to Scan the QR code  or use the code')</h6>
                            <p class="p-2">@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.')</p>
                            <a class="btn btn-outline-primary mt-3"
                               href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en"
                               target="_blank">@lang('DOWNLOAD APP')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enable Modal -->
    <div class="modal fade user-modal" id="enableModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Verify Your OTP')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('user.twoStepEnable')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="input-box col-md-12">
                            <input type="hidden" name="key" value="{{$secret}}">
                            <input type="text" class="form-control" name="code"
                                   placeholder="@lang('Enter Google Authenticator Code')" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Verify')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Disable Modal -->
    <div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="planModalLabel">@lang('Verify Your OTP to Disable')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('user.twoStepDisable')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="input-box col-12">
                                <input type="password" class="form-control" name="password" placeholder="@lang('Enter Your Password')" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button class="btn btn-primary" type="submit">@lang('Verify')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Regenerate Modal -->
    <div class="modal fade" id="regenerateModal" tabindex="-1" aria-labelledby="regenerateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="regenerateModalLabel">@lang('Re-generate Confirmation')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('user.twoStepRegenerate')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @lang("Are you want to Re-generate Authenticator ?")
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                        <button class="btn btn-primary" type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js-lib')
    <script src="{{asset('assets/admin/js/clipboard.min.js')}}"> </script>
@endpush

@push('script')
    <script>
        (function() {
            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            HSCore.components.HSClipboard.init('.js-clipboard')
        })();
    </script>
@endpush

