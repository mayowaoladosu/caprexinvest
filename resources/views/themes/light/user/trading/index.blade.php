@extends(template() . 'layouts.user')
@section('page_title', trans('Trading Center'))

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-header-title">@lang('Trading Center')</h1>
                <p class="text-muted mb-0">@lang('Live trading with admin controls, BTC support, and broker execution')</p>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">@lang('Market Chart')</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="live-dot"></span>
                            <small class="text-muted">@lang('Live feed')</small>
                            <span class="badge bg-soft-secondary text-secondary" id="market-session-status">@lang('Checking session...')</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="height: 460px;">
                    @include(template() . 'partials.trading_chart_widget', [
                        'chartSymbol' => $settings['chart_symbol'],
                        'chartInterval' => $settings['chart_interval'],
                        'chartTheme' => $settings['chart_theme'],
                    ])
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">@lang('Depth / Orderbook')</h4>
                    <small class="text-muted">@lang('Live ladder')</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="text-success">@lang('Bid Price')</th>
                                <th class="text-success">@lang('Bid Size')</th>
                                <th class="text-danger">@lang('Ask Price')</th>
                                <th class="text-danger">@lang('Ask Size')</th>
                            </tr>
                            </thead>
                            <tbody id="orderbook-body">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">@lang('Waiting for market depth...')</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('Place Order')</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-soft-primary text-primary" id="wallet-balance-badge">@lang('Wallet'): {{ currencyPosition($user->balance) }}</span>
                        <span class="badge bg-soft-info text-info">@lang('Internal Trading Engine')</span>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="live-metric-box">
                                <small>@lang('Open Positions')</small>
                                <div class="fw-semibold" id="open-positions-count">{{ $positions->count() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="live-metric-box">
                                <small>@lang('Trading Invested')</small>
                                <div class="fw-semibold" id="trading-invested-total">{{ currencyPosition($positions->sum('invested_amount')) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="risk-panel mb-3">
                        <div class="risk-panel-header">
                            <span>@lang('Risk Panel')</span>
                            <small class="text-muted">@lang('Live')</small>
                        </div>
                        <div class="closed-pnl-tabs mb-2">
                            <button type="button" class="btn btn-sm active" data-pnl-range="all">@lang('All-time')</button>
                            <button type="button" class="btn btn-sm" data-pnl-range="today">@lang('Today')</button>
                            <button type="button" class="btn btn-sm" data-pnl-range="7d">@lang('7D')</button>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Equity')</span>
                            <strong id="risk-equity">{{ currencyPosition($user->balance) }}</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Market Value')</span>
                            <strong id="risk-market-value">{{ currencyPosition(0) }}</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Unrealized P/L')</span>
                            <strong id="risk-unrealized-pnl">{{ currencyPosition(0) }}</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Open P/L %')</span>
                            <strong id="risk-open-pnl-percent">0.00%</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Closed P/L')</span>
                            <strong id="risk-closed-pnl">{{ currencyPosition(0) }}</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Profit Wallet (Realized)')</span>
                            <strong id="risk-profit-wallet-realized">{{ currencyPosition($user->profit_balance) }}</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Margin Utilization')</span>
                            <strong id="risk-margin-utilization">0.00%</strong>
                        </div>
                        <div class="risk-item">
                            <span>@lang('Top Symbol Concentration')</span>
                            <strong id="risk-concentration">0.00%</strong>
                        </div>
                    </div>
                    <form action="{{ route('user.trading.order') }}" method="POST" id="trade-form">
                        @csrf
                        @php
                            $defaultSide = $settings['allow_buy'] ? 'buy' : ($settings['allow_sell'] ? 'sell' : 'buy');
                        @endphp
                        <input type="hidden" name="side" id="trade-side" value="{{ $defaultSide }}">

                        <div class="trade-side-toggle mb-3" role="group" aria-label="@lang('Order Side')">
                            <button type="button" class="btn btn-buy-side {{ $defaultSide === 'buy' ? 'active' : '' }}" data-side="buy" @disabled(!$settings['allow_buy'])>
                                <i class="fa-light fa-arrow-up-right me-1"></i>@lang('Buy')
                            </button>
                            <button type="button" class="btn btn-sell-side {{ $defaultSide === 'sell' ? 'active' : '' }}" data-side="sell" @disabled(!$settings['allow_sell'])>
                                <i class="fa-light fa-arrow-down-right me-1"></i>@lang('Sell')
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Symbol')</label>
                            <select class="form-select" name="symbol" id="symbol">
                                @foreach($settings['symbols'] as $symbol)
                                    <option value="{{ $symbol }}">{{ $symbol }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($settings['symbols'] as $symbol)
                                    <button type="button" class="btn btn-sm btn-outline-secondary symbol-chip" data-symbol="{{ $symbol }}">{{ $symbol }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Quantity')</label>
                            <input type="number" step="0.00000001" min="0.00000001" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Take Profit (optional)')</label>
                            <input type="number" step="0.00000001" min="0.00000001" name="take_profit_price" class="form-control" placeholder="@lang('Target exit price')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Stop Loss (optional)')</label>
                            <input type="number" step="0.00000001" min="0.00000001" name="stop_loss_price" class="form-control" placeholder="@lang('Risk limit price')">
                        </div>

                        <div class="trade-quote-box mb-3">
                            <div class="trade-quote-row">
                                <span>@lang('Live Price')</span>
                                <strong id="quote-price">--</strong>
                            </div>
                            <div class="trade-quote-row">
                                <span>@lang('Gross')</span>
                                <strong id="quote-gross">--</strong>
                            </div>
                            <div class="trade-quote-row">
                                <span>@lang('Fee') ({{ $settings['fee_percent'] }}%)</span>
                                <strong id="quote-fee">--</strong>
                            </div>
                            <div class="trade-quote-row border-0 pb-0">
                                <span id="quote-net-label">@lang('Estimated Cost')</span>
                                <strong id="quote-net">--</strong>
                            </div>
                            <div class="small text-muted mt-2 d-flex justify-content-between">
                                <span>@lang('Limits'): {{ currencyPosition($settings['min_order_amount']) }} - {{ currencyPosition($settings['max_order_amount']) }}</span>
                                <span>@lang('Updated'): <span id="quote-updated-at">--</span></span>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100" id="submit-trade-btn" type="submit">@lang('Execute Order')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('Your Positions')</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th>@lang('Symbol')</th>
                            <th>@lang('Qty')</th>
                            <th>@lang('Entry')</th>
                            <th>@lang('Mark')</th>
                            <th>@lang('Market Value')</th>
                            <th>@lang('Unrealized P/L')</th>
                            <th>@lang('ROI')</th>
                            <th>@lang('Quick Close')</th>
                        </tr>
                        </thead>
                        <tbody id="positions-table-body">
                        @forelse($positions as $position)
                            <tr class="position-row">
                                <td>{{ $position->symbol }}</td>
                                <td>{{ number_format($position->quantity, 8) }}</td>
                                <td>{{ number_format($position->average_price, 4) }}</td>
                                <td>--</td>
                                <td>--</td>
                                <td><span class="badge bg-soft-secondary text-secondary position-pnl">@lang('Loading...')</span></td>
                                <td><span class="badge bg-soft-secondary text-secondary">@lang('Loading...')</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger quick-close-btn"
                                            data-symbol="{{ $position->symbol }}"
                                            data-quantity="{{ number_format($position->quantity, 8, '.', '') }}">
                                        @lang('Close')
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">@lang('No open positions')</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('Recent Orders')</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th>@lang('Trx')</th>
                            <th>@lang('Symbol')</th>
                            <th>@lang('Side')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Realized P/L')</th>
                            <th>@lang('Lifecycle')</th>
                            <th>@lang('Status')</th>
                        </tr>
                        </thead>
                        <tbody id="recent-orders-table-body">
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->trx_id }}</td>
                                <td>{{ $order->symbol }}</td>
                                <td>
                                    <span class="badge {{ $order->side === 'buy' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                        {{ strtoupper($order->side) }}
                                    </span>
                                </td>
                                <td>{{ currencyPosition($order->net_amount) }}</td>
                                <td>
                                    @if($order->side === 'sell')
                                        <span class="badge {{ ($order->realized_pnl ?? 0) >= 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                            {{ ($order->realized_pnl ?? 0) >= 0 ? '+' : '-' }}{{ currencyPosition(abs((float) ($order->realized_pnl ?? 0))) }}
                                            @if($order->realized_pnl_percent !== null)
                                                ({{ number_format((float) $order->realized_pnl_percent, 2) }}%)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $lifecycle = $order->lifecycle_status ?: 'pending';
                                        $lifeClass = match($lifecycle) {
                                            'filled' => 'bg-soft-success text-success',
                                            'partial' => 'bg-soft-warning text-warning',
                                            'cancelled' => 'bg-soft-danger text-danger',
                                            default => 'bg-soft-secondary text-secondary',
                                        };
                                        $fillPercent = (float) $order->quantity > 0 ? min(100, ((float) ($order->filled_quantity ?? 0) / (float) $order->quantity) * 100) : 0;
                                    @endphp
                                    <span class="badge {{ $lifeClass }}">{{ strtoupper($lifecycle) }}</span>
                                    <small class="d-block text-muted">{{ number_format($fillPercent, 2) }}%</small>
                                </td>
                                <td>
                                    <span class="badge {{ $order->is_live_order ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                        {{ $order->is_live_order ? ($order->broker_status ?: 'LIVE') : 'INTERNAL' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">@lang('No orders yet')</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $orders->links(template() . 'partials.user-pagination') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">@lang('Realized P/L History')</h4>
                    <small class="text-muted">@lang('Last 30 days')</small>
                </div>
                <div class="card-body">
                    <canvas id="pnl-history-chart" height="110"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .trade-side-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .trade-side-toggle .btn {
            border: 1px solid #d8dee8;
            font-weight: 600;
            padding: 0.7rem 0.75rem;
            transition: all .2s ease;
        }

        .trade-side-toggle .btn-buy-side.active {
            background: #16a34a;
            border-color: #16a34a;
            color: #fff;
        }

        .trade-side-toggle .btn-sell-side.active {
            background: #dc2626;
            border-color: #dc2626;
            color: #fff;
        }

        .symbol-chip {
            border-radius: 20px;
            transition: all .2s ease;
        }

        .symbol-chip.active {
            background: #0f172a;
            border-color: #0f172a;
            color: #fff;
        }

        .trade-quote-box {
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 12px;
            background: #f8fafc;
        }

        .trade-quote-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 7px;
            margin-bottom: 7px;
            border-bottom: 1px dashed #dce3ef;
            font-size: 0.92rem;
        }

        .trade-quote-row strong {
            font-size: 0.98rem;
        }

        .risk-panel {
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 10px 12px;
            background: linear-gradient(145deg, #f8fafc 0%, #f1f6ff 100%);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        .risk-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .risk-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 0.9rem;
            border-bottom: 1px dashed #dce3ef;
        }

        .risk-item:last-child {
            border-bottom: none;
        }

        .closed-pnl-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .closed-pnl-tabs .btn {
            border: 1px solid #d8dee8;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 600;
            background: #fff;
            border-radius: 9px;
            padding: 0.35rem 0.45rem;
        }

        .closed-pnl-tabs .btn.active {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
        }

        .live-metric-box {
            border: 1px solid #e8edf5;
            border-radius: 10px;
            padding: 8px 10px;
            background: #f8fafc;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        .live-metric-box small {
            display: block;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 rgba(16, 185, 129, .7);
            animation: pulse 1.6s infinite;
        }

        .quick-close-btn {
            border-radius: 20px;
            font-weight: 600;
            padding: 0.25rem 0.7rem;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, .7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
    </style>
@endpush

@push('js-lib')
    <script src="{{ asset('assets/admin/js/chart.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function () {
            const symbolEl = document.getElementById('symbol');
            const quantityEl = document.getElementById('quantity');
            const tradeFormEl = document.getElementById('trade-form');
            const takeProfitInputEl = tradeFormEl?.querySelector('input[name="take_profit_price"]');
            const stopLossInputEl = tradeFormEl?.querySelector('input[name="stop_loss_price"]');
            const sideInputEl = document.getElementById('trade-side');
            const quotePriceEl = document.getElementById('quote-price');
            const quoteGrossEl = document.getElementById('quote-gross');
            const quoteFeeEl = document.getElementById('quote-fee');
            const quoteNetEl = document.getElementById('quote-net');
            const quoteNetLabelEl = document.getElementById('quote-net-label');
            const quoteUpdatedAtEl = document.getElementById('quote-updated-at');
            const marketSessionStatusEl = document.getElementById('market-session-status');
            const orderbookBodyEl = document.getElementById('orderbook-body');
            const walletBalanceBadgeEl = document.getElementById('wallet-balance-badge');
            const openPositionsCountEl = document.getElementById('open-positions-count');
            const tradingInvestedTotalEl = document.getElementById('trading-invested-total');
            const riskEquityEl = document.getElementById('risk-equity');
            const riskMarketValueEl = document.getElementById('risk-market-value');
            const riskUnrealizedPnlEl = document.getElementById('risk-unrealized-pnl');
            const riskOpenPnlPercentEl = document.getElementById('risk-open-pnl-percent');
            const riskClosedPnlEl = document.getElementById('risk-closed-pnl');
            const riskProfitWalletRealizedEl = document.getElementById('risk-profit-wallet-realized');
            const riskMarginUtilizationEl = document.getElementById('risk-margin-utilization');
            const riskConcentrationEl = document.getElementById('risk-concentration');
            const closedPnlTabButtons = document.querySelectorAll('[data-pnl-range]');
            const positionsTableBodyEl = document.getElementById('positions-table-body');
            const recentOrdersTableBodyEl = document.getElementById('recent-orders-table-body');
            const submitTradeBtnEl = document.getElementById('submit-trade-btn');
            const pnlHistoryCanvasEl = document.getElementById('pnl-history-chart');
            const sideButtons = document.querySelectorAll('.trade-side-toggle .btn');
            const symbolChips = document.querySelectorAll('.symbol-chip');
            let currentPrice = 0;
            let quoteTimer = null;
            const feePercent = Number(@json($settings['fee_percent']));
            const quoteCache = new Map();
            const pusherKey = @json(env('PUSHER_APP_KEY'));
            const pusherCluster = @json(env('PUSHER_APP_CLUSTER'));
            let activeClosedPnlRange = 'all';
            let latestRiskSnapshot = null;
            let pnlHistoryChart = null;

            function formatSignedCurrency(value) {
                const abs = Math.abs(value);
                const formatted = currencyPosition(abs);
                return (value >= 0 ? '+' : '-') + formatted;
            }

            function isCryptoSymbol(symbol) {
                return symbol.includes('/') || symbol.includes('BTC') || symbol.includes('ETH') || symbol.includes('USDT');
            }

            function updateMarketSessionLabel(symbol) {
                const symbolValue = (symbol || '').toUpperCase();
                if (isCryptoSymbol(symbolValue)) {
                    marketSessionStatusEl.className = 'badge bg-soft-success text-success';
                    marketSessionStatusEl.textContent = @json(__('Market Open (24/7 Crypto)'));
                    return;
                }

                const formatter = new Intl.DateTimeFormat('en-US', {
                    timeZone: 'America/New_York',
                    weekday: 'short',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                const parts = formatter.formatToParts(new Date());
                const weekday = parts.find(part => part.type === 'weekday')?.value || '';
                const hour = Number(parts.find(part => part.type === 'hour')?.value || 0);
                const minute = Number(parts.find(part => part.type === 'minute')?.value || 0);
                const totalMinutes = hour * 60 + minute;
                const isWeekday = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'].includes(weekday);
                const marketOpen = 9 * 60 + 30;
                const marketClose = 16 * 60;
                const isOpen = isWeekday && totalMinutes >= marketOpen && totalMinutes <= marketClose;

                if (isOpen) {
                    marketSessionStatusEl.className = 'badge bg-soft-success text-success';
                    marketSessionStatusEl.textContent = @json(__('Market Open (US Equities)'));
                } else {
                    marketSessionStatusEl.className = 'badge bg-soft-danger text-danger';
                    marketSessionStatusEl.textContent = @json(__('Market Closed (US Equities)'));
                }
            }

            function setActiveSymbolChip(symbol) {
                symbolChips.forEach((chip) => {
                    chip.classList.toggle('active', chip.dataset.symbol === symbol);
                });
            }

            function renderOrderbook(orderbook) {
                if (!orderbook || !Array.isArray(orderbook.bids) || !Array.isArray(orderbook.asks) || !orderbook.bids.length || !orderbook.asks.length) {
                    orderbookBodyEl.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">@lang('Orderbook unavailable')</td></tr>`;
                    return;
                }

                const levels = Math.max(orderbook.bids.length, orderbook.asks.length);
                const rows = [];

                for (let i = 0; i < levels; i++) {
                    const bid = orderbook.bids[i] || {};
                    const ask = orderbook.asks[i] || {};
                    rows.push(`<tr>
                        <td class="text-success">${bid.price !== undefined ? Number(bid.price).toFixed(4) : '-'}</td>
                        <td>${bid.size !== undefined ? Number(bid.size).toFixed(4) : '-'}</td>
                        <td class="text-danger">${ask.price !== undefined ? Number(ask.price).toFixed(4) : '-'}</td>
                        <td>${ask.size !== undefined ? Number(ask.size).toFixed(4) : '-'}</td>
                    </tr>`);
                }

                orderbookBodyEl.innerHTML = rows.join('');
            }

            function renderClosedPnlValue(risk) {
                if (!riskClosedPnlEl || !risk) {
                    return;
                }

                let value = Number(risk.closed_pnl_total ?? 0);
                if (activeClosedPnlRange === 'today') {
                    value = Number(risk.closed_pnl_today ?? 0);
                } else if (activeClosedPnlRange === '7d') {
                    value = Number(risk.closed_pnl_7d ?? 0);
                }

                riskClosedPnlEl.textContent = currencyPosition(value);
                riskClosedPnlEl.classList.toggle('text-success', value >= 0);
                riskClosedPnlEl.classList.toggle('text-danger', value < 0);
            }

            function updateRiskPanel(risk) {
                if (!risk) {
                    return;
                }

                latestRiskSnapshot = risk;
                if (riskEquityEl) {
                    riskEquityEl.textContent = currencyPosition(risk.equity ?? 0);
                }
                if (riskMarketValueEl) {
                    riskMarketValueEl.textContent = currencyPosition(risk.market_value_total ?? 0);
                }
                if (riskUnrealizedPnlEl) {
                    const pnlValue = Number(risk.unrealized_pnl_total ?? 0);
                    riskUnrealizedPnlEl.textContent = currencyPosition(pnlValue);
                    riskUnrealizedPnlEl.classList.toggle('text-success', pnlValue >= 0);
                    riskUnrealizedPnlEl.classList.toggle('text-danger', pnlValue < 0);
                }
                if (riskOpenPnlPercentEl) {
                    const openPnlPercent = Number(risk.open_pnl_percent ?? 0);
                    riskOpenPnlPercentEl.textContent = `${openPnlPercent.toFixed(2)}%`;
                    riskOpenPnlPercentEl.classList.toggle('text-success', openPnlPercent >= 0);
                    riskOpenPnlPercentEl.classList.toggle('text-danger', openPnlPercent < 0);
                }
                if (riskMarginUtilizationEl) {
                    riskMarginUtilizationEl.textContent = `${Number(risk.margin_utilization_percent ?? 0).toFixed(2)}%`;
                }
                if (riskConcentrationEl) {
                    riskConcentrationEl.textContent = `${Number(risk.top_symbol_concentration_percent ?? 0).toFixed(2)}%`;
                }
                if (riskProfitWalletRealizedEl) {
                    const realizedWallet = Number(risk.profit_wallet_realized ?? 0);
                    riskProfitWalletRealizedEl.textContent = currencyPosition(realizedWallet);
                    riskProfitWalletRealizedEl.classList.toggle('text-success', realizedWallet >= 0);
                    riskProfitWalletRealizedEl.classList.toggle('text-danger', realizedWallet < 0);
                }

                renderClosedPnlValue(risk);
            }

            function renderPnlHistoryChart(history) {
                if (!pnlHistoryCanvasEl || typeof Chart === 'undefined' || !history || !Array.isArray(history.labels)) {
                    return;
                }

                const labels = history.labels || [];
                const netSeries = (history.net || []).map((value) => Number(value || 0));
                const cumulativeSeries = (history.cumulative || []).map((value) => Number(value || 0));

                if (pnlHistoryChart) {
                    pnlHistoryChart.destroy();
                }

                pnlHistoryChart = new Chart(pnlHistoryCanvasEl, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                type: 'bar',
                                label: @json(__('Daily Realized P/L')),
                                data: netSeries,
                                backgroundColor: netSeries.map((value) => value >= 0 ? 'rgba(16, 185, 129, 0.45)' : 'rgba(239, 68, 68, 0.45)'),
                                borderColor: netSeries.map((value) => value >= 0 ? 'rgba(16, 185, 129, 0.9)' : 'rgba(239, 68, 68, 0.9)'),
                                borderWidth: 1,
                                yAxisID: 'y'
                            },
                            {
                                type: 'line',
                                label: @json(__('Cumulative Realized P/L')),
                                data: cumulativeSeries,
                                borderColor: 'rgba(37, 99, 235, 0.9)',
                                borderWidth: 2,
                                pointRadius: 1.6,
                                pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                                tension: 0.25,
                                yAxisID: 'y'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return `${context.dataset.label}: ${currencyPosition(context.parsed.y || 0)}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                ticks: {
                                    callback: function (value) {
                                        return currencyPosition(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function renderPositions(positions) {
                if (!Array.isArray(positions) || positions.length === 0) {
                    positionsTableBodyEl.innerHTML = `<tr><td colspan="8" class="text-center text-muted">@lang('No open positions')</td></tr>`;
                    return;
                }

                const html = positions.map((position) => {
                    const unrealizedPnl = Number(position.unrealized_pnl || 0);
                    const roi = Number(position.unrealized_roi_percent || 0);
                    const pnlClass = unrealizedPnl >= 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                    const roiClass = roi >= 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                    return `<tr class="position-row"
                                data-symbol="${position.symbol}"
                                data-quantity="${position.quantity}"
                                data-average-price="${position.average_price}">
                                <td>${position.symbol}</td>
                                <td>${Number(position.quantity).toFixed(8)}</td>
                                <td>${Number(position.average_price).toFixed(4)}</td>
                                <td>${Number(position.mark_price || 0).toFixed(4)}</td>
                                <td>${currencyPosition(position.market_value || 0)}</td>
                                <td><span class="badge ${pnlClass}">${formatSignedCurrency(unrealizedPnl)}</span></td>
                                <td><span class="badge ${roiClass}">${roi >= 0 ? '+' : ''}${roi.toFixed(2)}%</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger quick-close-btn"
                                        data-symbol="${position.symbol}"
                                        data-quantity="${Number(position.quantity).toFixed(8)}">
                                        @lang('Close')
                                    </button>
                                </td>
                            </tr>`;
                }).join('');

                positionsTableBodyEl.innerHTML = html;
            }

            function renderRecentOrders(orders) {
                if (!Array.isArray(orders) || orders.length === 0) {
                    recentOrdersTableBodyEl.innerHTML = `<tr><td colspan="7" class="text-center text-muted">@lang('No orders yet')</td></tr>`;
                    return;
                }

                const html = orders.map((order) => {
                    const sideClass = order.side === 'buy' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                    const orderStatus = order.is_live_order ? (order.broker_status || 'LIVE') : 'INTERNAL';
                    const statusClass = order.is_live_order ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning';
                    const lifecycle = String(order.lifecycle_status || 'pending').toLowerCase();
                    const lifecycleClass = lifecycle === 'filled'
                        ? 'bg-soft-success text-success'
                        : lifecycle === 'partial'
                            ? 'bg-soft-warning text-warning'
                            : lifecycle === 'cancelled'
                                ? 'bg-soft-danger text-danger'
                                : 'bg-soft-secondary text-secondary';
                    const fillPercent = Number(order.quantity || 0) > 0
                        ? Math.min(100, (Number(order.filled_quantity || 0) / Number(order.quantity || 0)) * 100)
                        : 0;
                    const realizedPnl = Number(order.realized_pnl || 0);
                    const realizedPnlLabel = realizedPnl >= 0 ? `+${currencyPosition(Math.abs(realizedPnl))}` : `-${currencyPosition(Math.abs(realizedPnl))}`;
                    const realizedClass = realizedPnl >= 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                    const realizedPercent = order.realized_pnl_percent !== null && order.realized_pnl_percent !== undefined
                        ? ` (${Number(order.realized_pnl_percent).toFixed(2)}%)`
                        : '';

                    return `<tr>
                                <td>${order.trx_id}</td>
                                <td>${order.symbol}</td>
                                <td><span class="badge ${sideClass}">${String(order.side).toUpperCase()}</span></td>
                                <td>${currencyPosition(order.net_amount)}</td>
                                <td>${String(order.side).toLowerCase() === 'sell' ? `<span class="badge ${realizedClass}">${realizedPnlLabel}${realizedPercent}</span>` : '<span class="text-muted">--</span>'}</td>
                                <td>
                                    <span class="badge ${lifecycleClass}">${lifecycle.toUpperCase()}</span>
                                    <small class="d-block text-muted">${fillPercent.toFixed(2)}%</small>
                                </td>
                                <td><span class="badge ${statusClass}">${orderStatus}</span></td>
                            </tr>`;
                }).join('');

                recentOrdersTableBodyEl.innerHTML = html;
            }

            async function fetchTradingSnapshot() {
                try {
                    const response = await axios.get("{{ route('user.trading.snapshot') }}");
                    const snapshot = response.data || {};
                    if (snapshot.enabled === false) {
                        return;
                    }

                    if (walletBalanceBadgeEl && snapshot.wallet_balance !== undefined) {
                        walletBalanceBadgeEl.textContent = `@lang('Wallet'): ${currencyPosition(snapshot.wallet_balance)}`;
                    }
                    if (openPositionsCountEl && snapshot.open_positions_count !== undefined) {
                        openPositionsCountEl.textContent = snapshot.open_positions_count;
                    }
                    if (tradingInvestedTotalEl && snapshot.trading_invested_total !== undefined) {
                        tradingInvestedTotalEl.textContent = currencyPosition(snapshot.trading_invested_total);
                    }
                    updateRiskPanel(snapshot.risk || null);
                    renderPnlHistoryChart(snapshot.pnl_history || null);

                    renderPositions(snapshot.positions || []);
                    renderRecentOrders(snapshot.recent_orders || []);
                } catch (error) {
                    // Silent fail to avoid noisy UX in live polling.
                }
            }

            async function fetchQuote() {
                try {
                    submitTradeBtnEl.disabled = true;
                    const response = await axios.get("{{ route('user.trading.quote') }}", {
                        params: {symbol: symbolEl.value}
                    });
                    currentPrice = Number(response.data.price || 0);
                    quoteCache.set(symbolEl.value, currentPrice);
                    quotePriceEl.textContent = currencyPosition(currentPrice);
                    quoteUpdatedAtEl.textContent = new Date().toLocaleTimeString();
                    renderOrderbook(response.data.orderbook || null);
                    updateSummary();
                    updateMarketSessionLabel(symbolEl.value);
                    submitTradeBtnEl.disabled = false;
                } catch (error) {
                    currentPrice = 0;
                    quotePriceEl.textContent = '--';
                    quoteGrossEl.textContent = '--';
                    quoteFeeEl.textContent = '--';
                    quoteNetEl.textContent = '--';
                    quoteUpdatedAtEl.textContent = '--';
                    marketSessionStatusEl.className = 'badge bg-soft-warning text-warning';
                    marketSessionStatusEl.textContent = @json(__('Feed unavailable'));
                    renderOrderbook(null);
                    submitTradeBtnEl.disabled = true;
                }
            }

            function updateSummary() {
                const qty = Number(quantityEl.value || 0);
                if (!currentPrice || !qty) {
                    quoteGrossEl.textContent = '--';
                    quoteFeeEl.textContent = '--';
                    quoteNetEl.textContent = '--';
                    return;
                }

                const gross = qty * currentPrice;
                const fee = gross * (feePercent / 100);
                const isBuy = sideInputEl.value === 'buy';
                const net = isBuy ? gross + fee : gross - fee;

                quoteGrossEl.textContent = currencyPosition(gross);
                quoteFeeEl.textContent = currencyPosition(fee);
                quoteNetEl.textContent = currencyPosition(net);
                quoteNetLabelEl.textContent = isBuy ? @json(__('Estimated Cost')) : @json(__('Estimated Receive'));
            }

            function setSide(side) {
                sideInputEl.value = side;
                sideButtons.forEach((button) => {
                    button.classList.toggle('active', button.dataset.side === side);
                });
                updateSummary();
            }

            function initWebsocketStream() {
                if (!pusherKey || typeof Pusher === 'undefined') {
                    return;
                }

                try {
                    const pusher = new Pusher(pusherKey, {
                        cluster: pusherCluster || 'mt1',
                        forceTLS: true
                    });

                    const channel = pusher.subscribe('trading.ticks');
                    channel.bind('tick.updated', function (payload) {
                        if (!payload || String(payload.symbol || '').toUpperCase() !== String(symbolEl.value || '').toUpperCase()) {
                            return;
                        }

                        if (payload.price) {
                            currentPrice = Number(payload.price || 0);
                            quotePriceEl.textContent = currencyPosition(currentPrice);
                            quoteUpdatedAtEl.textContent = new Date().toLocaleTimeString();
                            quoteCache.set(symbolEl.value, currentPrice);
                            updateSummary();
                        }

                        if (payload.orderbook) {
                            renderOrderbook(payload.orderbook);
                        }
                    });
                } catch (error) {
                    // Polling remains active as fallback.
                }
            }

            sideButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    if (this.disabled) {
                        return;
                    }
                    setSide(this.dataset.side);
                });
            });

            symbolChips.forEach((chip) => {
                chip.addEventListener('click', function () {
                    symbolEl.value = this.dataset.symbol;
                    setActiveSymbolChip(this.dataset.symbol);
                    fetchQuote();
                });
            });

            symbolEl.addEventListener('change', function () {
                setActiveSymbolChip(symbolEl.value);
                fetchQuote();
            });
            quantityEl.addEventListener('input', updateSummary);

            closedPnlTabButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    activeClosedPnlRange = this.dataset.pnlRange || 'all';
                    closedPnlTabButtons.forEach((item) => item.classList.toggle('active', item === this));
                    renderClosedPnlValue(latestRiskSnapshot);
                });
            });

            const submitTradeOrder = async (formData) => {
                submitTradeBtnEl.disabled = true;

                try {
                    const response = await axios.post(tradeFormEl.action, formData, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    Notiflix.Notify.success(response.data.message || @json(__('Order executed successfully.')));
                    quantityEl.value = '';
                    updateSummary();
                    quoteCache.clear();
                    fetchQuote();
                    if (response.data && response.data.snapshot) {
                        const snapshot = response.data.snapshot;
                        if (walletBalanceBadgeEl && snapshot.wallet_balance !== undefined) {
                            walletBalanceBadgeEl.textContent = `@lang('Wallet'): ${currencyPosition(snapshot.wallet_balance)}`;
                        }
                        if (openPositionsCountEl && snapshot.open_positions_count !== undefined) {
                            openPositionsCountEl.textContent = snapshot.open_positions_count;
                        }
                        if (tradingInvestedTotalEl && snapshot.trading_invested_total !== undefined) {
                            tradingInvestedTotalEl.textContent = currencyPosition(snapshot.trading_invested_total);
                        }
                        updateRiskPanel(snapshot.risk || null);
                        renderPnlHistoryChart(snapshot.pnl_history || null);
                        renderPositions(snapshot.positions || []);
                        renderRecentOrders(snapshot.recent_orders || []);
                    }
                    return true;
                } catch (error) {
                    const response = error.response || {};
                    const errorData = response.data || {};

                    if (errorData.errors && typeof errorData.errors === 'object') {
                        const firstErrorKey = Object.keys(errorData.errors)[0];
                        const firstErrorMessage = firstErrorKey ? errorData.errors[firstErrorKey][0] : null;
                        Notiflix.Notify.failure(firstErrorMessage || @json(__('Validation failed. Please check your inputs.')));
                    } else {
                        Notiflix.Notify.failure(errorData.message || @json(__('Unable to place order right now.')));
                    }
                    return false;
                } finally {
                    submitTradeBtnEl.disabled = false;
                }
            };

            positionsTableBodyEl.addEventListener('click', async function (event) {
                const closeBtn = event.target.closest('.quick-close-btn');
                if (!closeBtn) {
                    return;
                }

                const symbol = closeBtn.dataset.symbol;
                const quantity = closeBtn.dataset.quantity;
                if (!symbol || !quantity) {
                    Notiflix.Notify.failure(@json(__('Unable to close this position.')));
                    return;
                }

                const confirmed = window.confirm(@json(__('Close this position now with a market sell order?')));
                if (!confirmed) {
                    return;
                }

                symbolEl.value = symbol;
                quantityEl.value = Number(quantity).toFixed(8);
                if (takeProfitInputEl) takeProfitInputEl.value = '';
                if (stopLossInputEl) stopLossInputEl.value = '';
                setActiveSymbolChip(symbol);
                setSide('sell');
                updateSummary();
                await fetchQuote();

                const formData = new FormData(tradeFormEl);
                formData.set('symbol', symbol);
                formData.set('side', 'sell');
                formData.set('quantity', Number(quantity).toFixed(8));
                if (takeProfitInputEl) formData.set('take_profit_price', '');
                if (stopLossInputEl) formData.set('stop_loss_price', '');
                await submitTradeOrder(formData);
            });

            tradeFormEl.addEventListener('submit', async function (event) {
                event.preventDefault();
                const formData = new FormData(tradeFormEl);
                await submitTradeOrder(formData);
            });

            setActiveSymbolChip(symbolEl.value);
            updateMarketSessionLabel(symbolEl.value);
            setSide(sideInputEl.value);
            initWebsocketStream();
            fetchQuote();
            fetchTradingSnapshot();
            quoteTimer = setInterval(function () {
                quoteCache.clear();
                fetchQuote();
                fetchTradingSnapshot();
            }, 7000);
        })();
    </script>
@endpush

