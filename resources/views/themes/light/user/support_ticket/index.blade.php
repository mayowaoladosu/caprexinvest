@extends($theme.'layouts.user')
@section('page_title',trans('Support Ticket'))
@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang('Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('Support Ticket')</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang('Support Ticket')</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-end border-0">
            <div class="btn-area">
                <a href="{{route('user.ticket.create')}}" class="btn btn-primary"><i class="fal fa-plus-circle"></i> @lang('New Ticket') <span></span></a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive datatable-custom">
                <table class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>@lang('SL')</th>
                        <th scope="col">@lang('Subject')</th>
                        <th scope="col">@lang('Status')</th>
                        <th scope="col">@lang('Last Reply')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tickets as $key => $ticket)
                        <tr>
                            <td data-label="@lang('SL')">{{ $loop->index + 1 }}</td>
                            <td data-label="@lang('Subject')">
                                    <span class="font-weight-bold">
                                        [{{ trans('Ticket#').$ticket->ticket }}] {{ str($ticket->subject)->limit(100) }}
                                    </span>
                            </td>
                            <td data-label="@lang('Status')">
                                {!! $ticket->getTicketStatusBadge() !!}
                            </td>
                            <td data-label="@lang('Last Reply')">
                                {{diffForHumans($ticket->last_reply) }}
                            </td>

                            <td data-label="@lang('Action')">
                                <a href="{{ route('user.ticket.view', $ticket->ticket) }}" class="btn btn-white"><i class="fal fa-eye"></i> @lang('View') <span></span></a>
                            </td>
                        </tr>
                    @empty

                    @endforelse
                    </tbody>
                </table>

            </div>
            @if(count($tickets??[]) == 0)
                <div class="row d-flex text-center justify-content-center">
                    <div class="col-4">
                        <img src="{{ asset('assets/admin/img/oc-error.svg') }}" id="no-data-image" class="no-data-image" alt="" srcset="">
                        <p>@lang('No data to show')</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="card-footer">
            <div class="pagination-section">
                <nav aria-label="...">
                    {{ $tickets->appends($_GET)->links($theme.'partials.user-pagination') }}
                </nav>
            </div>
        </div>
    </div>

@endsection
