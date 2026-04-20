@extends($theme.'layouts.user')
@section('page_title',trans('Notification Permission'))
@section('content')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="{{route('user.dashboard')}}">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Notification Permission")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Notification Permission")</h1>
            </div>
        </div>
    </div>

    <div class=" row">
        <div class="col-12">
            <form action="{{route('user.notification.permission.update')}}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                        <button class="btn btn-white checkAll" type="button"><i
                                class="fa-regular fa-square-check"></i> @lang('Check All')</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive datatable-custom">
                            <table class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 45%;" scope="col">@lang('Type')</th>
                                    <th scope="col">✉️ @lang('Email')</th>
                                    <th scope="col">✉️ @lang('SMS')</th>
                                    <th scope="col">🖥 @lang('Push')</th>
                                    <th class="text-start" scope="col"> 👩🏻‍💻 @lang('In App')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($allTemplates as $item)
                                    <tr>
                                        <td data-label="Type">
                                            <div class="d-flex align-items-center">
                                                <span>{{$item->name}}</span>
                                            </div>

                                        </td>
                                        <td data-label="✉️ Email">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       role="switch" name="email_key[]"
                                                       value="{{$item->template_key ?? ""}}"
                                                       {{ !$item->status['mail'] ? 'disabled':'' }}
                                                       id="emailSwitch"
                                                    {{ in_array($item->template_key, optional($user->notifypermission)->template_email_key ?? []) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td data-label="✉️ SMS">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       role="switch" name="sms_key[]"
                                                       value="{{$item->template_key ?? ""}}"
                                                       {{ !$item->status['sms'] ? 'disabled':'' }}
                                                       id="smsSwitch"
                                                    {{ in_array($item->template_key, optional($user->notifypermission)->template_sms_key ?? []) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td data-label="🖥 Browser">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       role="switch"
                                                       name="push_key[]"
                                                       value="{{ $item->template_key ?? "" }}"
                                                       {{ !$item->status['push'] ? 'disabled' : '' }}
                                                       id="pushSwitch"
                                                    {{ in_array($item->template_key, optional($user->notifypermission)->template_push_key ?? []) ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td data-label="👩🏻‍💻 App">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       role="switch"
                                                       name="in_app_key[]"
                                                       value="{{$item->template_key ?? ""}}"
                                                       id="appSwitch"
                                                    {{!$item->status['in_app'] ? 'disabled':''}}
                                                    {{ in_array($item->template_key, optional($user->notifypermission)->template_in_app_key ?? []) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-danger"
                                            colspan="100%">@lang('No Data Found.')</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-white">
                        <button type="submit" class="btn btn-primary">@lang('Save changes')<span></span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.checkAll', function () {
            $('.form-check-input').each(function () {
                if (!$(this).prop('disabled')) {
                    $(this).prop('checked', true);
                }
            });
        });
    </script>
@endpush


