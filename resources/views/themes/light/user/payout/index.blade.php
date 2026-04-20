@extends($theme.'layouts.user')
@section('page_title',__('Payout History'))

@section('content')

    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="javascript:void(0)">@lang('Dashboard')</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('Withdraw History')</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang('Withdraw History')</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header btn-area d-flex justify-content-end">
            <button type="button" class="btn btn-white" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch" aria-controls="offcanvasExample"><i class="fa-light fa-search me-1"></i>@lang('Filter') <span></span> </button>
        </div>
        <div class="card-body">
            <div class="table-responsive datatable-custom">
                <table class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>@lang('SL')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Charge')</th>
                        <th>@lang('Created time')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payouts as $key => $value)

                        <tr>
                            <td data-label="@lang('SL')">{{ $loop->index + 1 }}</td>
                            <td data-label="@lang('Transaction ID')">{{ __($value->trx_id) }}</td>
                            <td data-label="@lang('Amount')">{{ (getAmount($value->amount)).' '.$value->payout_currency_code }}</td>
                            <td data-label="@lang('Charge')" class="text-danger">{{ (getAmount($value->charge)).' '.$value->payout_currency_code }}</td>

                            <td data-label="@lang('Created time')"> {{ dateTime($value->created_at)}} </td>
                            <td data-label="@lang('Status')">
                                    {!! $value->getStatusBadge() !!}
                            </td>
                            <td>
                                @if($value->status == 0)
                                    <a href="{{ route('user.payout.confirm',$value->trx_id) }}" target="_blank"
                                       class="btn btn-white "><i class="fa-thin fa-square-check"></i> @lang('Confirm') <span></span></a>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    @empty

                    @endforelse
                    </tbody>
                </table>

            </div>
            @if(count($payouts??[]) == 0)
                <div class="row d-flex text-center justify-content-center">
                    <div class="col-4">
                        <img src="{{ asset('assets/admin/img/oc-error.svg') }}" id="no-data-image" class="no-data-image" alt="" srcset="">
                        <p>@lang('No data to show')</p>
                    </div>
                </div>
            @endif
        </div>

        <div  class="card-footer">
            <div class="pagination-section">
                <nav aria-label="...">
                    {{ $payouts->appends($_GET)->links($theme.'partials.user-pagination') }}
                </nav>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSearch" aria-labelledby="offcanvasExampleLabel" aria-modal="true" role="dialog">
        <div class="offcanvas-header d-flex justify-content-between">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel me-2">@lang('Pyout Filter')</h5>
            <button type="button" class="btn btn-white" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fa-light fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{route('user.payout.index')}}" method="GET">
                <div class="row g-4">
                    <div>
                        <label for="Title" class="form-label">@lang('Transaction ID')</label>
                        <input placeholder="Transaction ID" name="trx_id"  type="text" class="form-control form-control-sm">
                    </div>

                    <div>
                        <label for="CreatedAt" class="form-label"> @lang('Date Range') </label>
                        <input type="text" name="date_range" class="form-control date flatpickr-input active"  readonly="readonly" placeholder="@lang('Select Dates')">
                    </div>

                    <div class="btn-area">
                        <button type="submit" class="btn btn-primary">@lang('Filter') <span></span> </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('script')
    <script>
        'use strict';
        flatpickr(".date",{
            mode: "range",
        });
    </script>
@endpush

