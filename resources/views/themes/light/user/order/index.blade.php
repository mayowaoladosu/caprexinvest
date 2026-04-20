@extends($theme.'layouts.user')
@section('page_title',trans('Orders'))
@section('content')
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                       href="{{route('user.dashboard')}}">@lang("Dashboard")</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@lang("Orders")</li>
                    </ol>
                </nav>
                <h1 class="page-header-title">@lang("Orders")</h1>
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
                        <th scope="col">@lang('Order')</th>
                        <th scope="col">@lang('Date')</th>
                        <th scope="col">@lang('Total')</th>
                        <th scope="col">@lang('Status')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $key => $order)
                        <tr>
                            <td data-label="@lang('Order')">{{$order->order_number}}</td>
                            <td data-label="@lang('Date')"> {{dateTime($order->created_at)}} </td>
                            <td data-label="@lang('Total')" class="font-bold">{{currencyPosition($order->total + 0)}}</td>
                            <td data-label="@lang('Status')">{!! $order->orderStatus() !!}</td>
                            <td data-label="@lang('Action')">
                                <a href="{{route('user.orderItems',$order->id)}}"
                                   class="btn btn-white">
                                    <i class="fal fa-eye"></i>
                                    @lang('View') <span></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            @if(count($orders??[]) == 0)
                <div class="row d-flex text-center justify-content-center">
                    <div class="col-4">
                        <img src="{{ asset('assets/admin/img/oc-error.svg') }}" id="no-data-image" class="no-data-image" alt="" srcset="">
                        <p>@lang('No data to show')</p>
                    </div>
                </div>
            @endif

        </div>

        <div class="card-footer d-flex justify-content-end">
            <div class="pagination-section">
                <nav aria-label="...">
                    {{ $orders->appends($_GET)->links($theme.'partials.user-pagination') }}
                </nav>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSearch" aria-labelledby="offcanvasExampleLabel" aria-modal="true" role="dialog">
        <div class="offcanvas-header d-flex justify-content-between">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel me-2">@lang('Order Filter')</h5>
            <button type="button" class="btn btn-white btn-sm" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fa-light fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{route('user.orders')}}" method="GET">
                <div class="row g-4">
                    <div>
                        <label for="Title" class="form-label">@lang('Order ID')</label>
                        <input placeholder="Order ID" name="order_id"  type="text" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label for="CreatedAt" class="form-label"> @lang('Date Range') </label>
                        <input type="text" name="date_range" class="form-control date flatpickr-input active"  readonly="readonly" placeholder="@lang('Select Dates')">
                    </div>

                    <div class="form-group">
                        <label class="modal-label" for="balance_type">@lang('Status')</label>
                        <select class="form-control" name="status" data-select2-id="select2-data-1-sjyd" tabindex="-1" aria-hidden="true">
                            <option value="all">@lang('All')</option>
                            <option
                                value="4">@lang('Pending')</option>
                            <option
                                value="1">@lang('Order Placed')</option>
                            <option value="2">@lang('Delivered')</option>
                            <option value="3">@lang('Canceled')</option>
                        </select>
                    </div>

                    <div class="btn-area">
                        <button type="submit" class="btn btn-primary"> <i class="fa-light fa-search me-2"></i> @lang('Apply') <span></span> </button>
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
