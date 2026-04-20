@extends('admin.layouts.app')
@section('page_title', __('Trading Orders'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-header-title">@lang('Trading Orders Monitor')</h1>
                    <p class="text-muted mb-0">@lang('Monitor all user buy/sell orders and broker execution status')</p>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Total Orders')</small>
                            <h4 class="mb-0" id="admin-trading-total-orders">0</h4>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Open Positions')</small>
                            <h4 class="mb-0" id="admin-trading-open-positions">0</h4>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Live Orders')</small>
                            <h4 class="mb-0" id="admin-trading-live-orders">0</h4>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Buy Volume')</small>
                            <h6 class="mb-0" id="admin-trading-buy-volume">{{ currencyPosition(0) }}</h6>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Sell Volume')</small>
                            <h6 class="mb-0" id="admin-trading-sell-volume">{{ currencyPosition(0) }}</h6>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">@lang('Fees')</small>
                            <h6 class="mb-0" id="admin-trading-fees">{{ currencyPosition(0) }}</h6>
                        </div>
                    </div>
                </div>
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">@lang('Symbol')</label>
                        <input type="text" name="symbol" class="form-control" value="{{ request('symbol') }}" placeholder="BTC/USD">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">@lang('Side')</label>
                        <select name="side" class="form-select">
                            <option value="">@lang('All')</option>
                            <option value="buy" @selected(request('side') === 'buy')>@lang('Buy')</option>
                            <option value="sell" @selected(request('side') === 'sell')>@lang('Sell')</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">@lang('Broker Status')</label>
                        <input type="text" name="status" class="form-control" value="{{ request('status') }}" placeholder="filled / accepted">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">@lang('TRX ID')</label>
                        <input type="text" name="trx_id" class="form-control" value="{{ request('trx_id') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">@lang('Filter')</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>@lang('Date')</th>
                        <th>@lang('User')</th>
                        <th>@lang('TRX')</th>
                        <th>@lang('Symbol')</th>
                        <th>@lang('Side')</th>
                        <th>@lang('Qty')</th>
                        <th>@lang('Net')</th>
                        <th>@lang('Live')</th>
                        <th>@lang('Broker')</th>
                        <th>@lang('Status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ dateTime($order->created_at, 'd M Y h:i A') }}</td>
                            <td>{{ optional($order->user)->firstname }} {{ optional($order->user)->lastname }}<br><small class="text-muted">{{ '@' . optional($order->user)->username }}</small></td>
                            <td>{{ $order->trx_id }}</td>
                            <td>{{ $order->symbol }}</td>
                            <td>
                                <span class="badge {{ $order->side === 'buy' ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }}">
                                    {{ strtoupper($order->side) }}
                                </span>
                            </td>
                            <td>{{ number_format($order->quantity, 8) }}</td>
                            <td>{{ currencyPosition($order->net_amount) }}</td>
                            <td>
                                <span class="badge {{ $order->is_live_order ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                    {{ $order->is_live_order ? 'LIVE' : 'INTERNAL' }}
                                </span>
                            </td>
                            <td>{{ $order->broker_name ?: '-' }}<br><small class="text-muted">{{ $order->broker_order_id ?: '-' }}</small></td>
                            <td>{{ $order->broker_status ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">@lang('No trading orders found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function () {
            const totalOrdersEl = document.getElementById('admin-trading-total-orders');
            const openPositionsEl = document.getElementById('admin-trading-open-positions');
            const liveOrdersEl = document.getElementById('admin-trading-live-orders');
            const buyVolumeEl = document.getElementById('admin-trading-buy-volume');
            const sellVolumeEl = document.getElementById('admin-trading-sell-volume');
            const feesEl = document.getElementById('admin-trading-fees');

            async function refreshAdminTradingSummary() {
                try {
                    const response = await axios.get("{{ route('admin.trading.orders.summary') }}");
                    const summary = response.data || {};
                    if (totalOrdersEl) totalOrdersEl.textContent = summary.total_orders ?? '0';
                    if (openPositionsEl) openPositionsEl.textContent = summary.open_positions ?? '0';
                    if (liveOrdersEl) liveOrdersEl.textContent = summary.live_orders ?? '0';
                    if (buyVolumeEl) buyVolumeEl.textContent = currencyPosition(summary.buy_volume ?? 0);
                    if (sellVolumeEl) sellVolumeEl.textContent = currencyPosition(summary.sell_volume ?? 0);
                    if (feesEl) feesEl.textContent = currencyPosition(summary.fees_collected ?? 0);
                } catch (error) {
                    // Keep silent to avoid admin toast spam.
                }
            }

            refreshAdminTradingSummary();
            setInterval(refreshAdminTradingSummary, 10000);
        })();
    </script>
@endpush

