@extends('admin.layouts.app')
@section('page_title', __('Gold Rate Api Key'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:void(0);">
                                    @lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Settings')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Gold Rate Api Key')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Gold Rate Api Key')</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('admin.control_panel.components.sidebar', [
                    'settings' => config('generalsettings.settings'),
                    'suffix' => 'Settings',
                ])
            </div>
            <div class="col-lg-6">
                <div class="d-grid gap-3 gap-lg-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h2 class="card-title h4">@lang('Metal Price Api Configuration')</h2>
                        </div>
                        <div class="card-body">
                            <form action="{{route('admin.gold_price.api_key.update')}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="agora_app_id">@lang('Site Url')</label>
                                    <input type="text"  class="form-control" value="https://metalpriceapi.com/" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="api_key">@lang('Api Key')</label>
                                    <input type="text" name="api_key" id="api_key" class="form-control" value="{{basicControl()->metal_price_api_key}}"
                                           placeholder="Metal Price Api Key">
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary">@lang('Update')</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.edit_btn', function() {
            let App = $(this).data('App');
            let route = $(this).data('route');
            $('.edit_map_App').val(App)
            $('#editRoute').attr('action', route);
        })

        $(document).on('click', '.set', function() {
            let url = $(this).data('route');
            let value = $(this).data('value');
            $('.method_value').val(value);
            $('.setRoute').attr('action', url);
        })
    </script>
@endpush
