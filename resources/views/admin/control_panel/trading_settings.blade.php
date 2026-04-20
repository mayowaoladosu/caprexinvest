@extends('admin.layouts.app')
@section('page_title', __('Trading Settings'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Settings')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Trading Settings')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Trading Settings')</h1>
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
            <div class="col-lg-9">
                <div class="card mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">@lang('Production Trading Control')</h4>
                            <p class="text-muted mb-0">@lang('Configure live broker integration, BTC pairs, risk and chart behavior')</p>
                        </div>
                        <a href="{{ route('admin.trading.orders.index') }}" class="btn btn-outline-primary">
                            <i class="fa-light fa-list-check me-1"></i>@lang('View Trading Orders')
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.trading.settings.update') }}" method="POST">
                            @csrf
                            @php
                                $savedSymbols = basicControl()->trading_symbols;
                                if (is_string($savedSymbols)) {
                                    $decodedSymbols = json_decode($savedSymbols, true);
                                    $savedSymbols = is_array($decodedSymbols) ? $decodedSymbols : [];
                                }
                                $defaultSymbols = [
                                    'NASDAQ:AAPL', 'NASDAQ:MSFT', 'NASDAQ:NVDA', 'NASDAQ:TSLA', 'NASDAQ:AMZN',
                                    'NASDAQ:META', 'NASDAQ:GOOGL', 'NASDAQ:AMD', 'NYSE:JPM', 'NYSE:XOM',
                                    'NYSE:V', 'NYSE:MA', 'NYSE:WMT', 'NYSE:KO', 'NYSE:NKE',
                                    'BTC/USD', 'ETH/USD', 'SOL/USD', 'LTC/USD', 'XRP/USD',
                                    'DOGE/USD', 'AVAX/USD', 'BNB/USD', 'DOT/USD', 'MATIC/USD',
                                    'EUR/USD', 'GBP/USD', 'USD/JPY', 'XAU/USD',
                                ];
                                $cryptoPack = ['BTC/USD', 'ETH/USD', 'SOL/USD', 'LTC/USD', 'XRP/USD', 'DOGE/USD', 'AVAX/USD', 'BNB/USD', 'DOT/USD', 'MATIC/USD'];
                                $usEquityPack = ['NASDAQ:AAPL', 'NASDAQ:MSFT', 'NASDAQ:NVDA', 'NASDAQ:TSLA', 'NASDAQ:AMZN', 'NASDAQ:META', 'NASDAQ:GOOGL', 'NASDAQ:AMD', 'NYSE:JPM', 'NYSE:XOM', 'NYSE:V', 'NYSE:MA', 'NYSE:WMT', 'NYSE:KO', 'NYSE:NKE'];
                                $macroPack = ['EUR/USD', 'GBP/USD', 'USD/JPY', 'XAU/USD'];
                            @endphp

                            <h5 class="mb-3">@lang('Trading Engine')</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Enable Trading')</label>
                                    <select class="form-select" name="trading_enabled">
                                        <option value="1" @selected(basicControl()->trading_enabled)>@lang('Enabled')</option>
                                        <option value="0" @selected(!basicControl()->trading_enabled)>@lang('Disabled')</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Allow Buy')</label>
                                    <select class="form-select" name="trading_allow_buy">
                                        <option value="1" @selected(basicControl()->trading_allow_buy)>@lang('Enabled')</option>
                                        <option value="0" @selected(!basicControl()->trading_allow_buy)>@lang('Disabled')</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Allow Sell')</label>
                                    <select class="form-select" name="trading_allow_sell">
                                        <option value="1" @selected(basicControl()->trading_allow_sell)>@lang('Enabled')</option>
                                        <option value="0" @selected(!basicControl()->trading_allow_sell)>@lang('Disabled')</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Show chart on user dashboard')</label>
                                    <select class="form-select" name="trading_dashboard_chart_enabled">
                                        <option value="1" @selected(basicControl()->trading_dashboard_chart_enabled)>@lang('Yes')</option>
                                        <option value="0" @selected(!basicControl()->trading_dashboard_chart_enabled)>@lang('No')</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Show chart on website home page')</label>
                                    <select class="form-select" name="trading_homepage_chart_enabled">
                                        <option value="1" @selected(basicControl()->trading_homepage_chart_enabled)>@lang('Yes')</option>
                                        <option value="0" @selected(!basicControl()->trading_homepage_chart_enabled)>@lang('No')</option>
                                    </select>
                                </div>
                            </div>

                            <h5 class="mb-3">@lang('Chart + Symbols')</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Default Chart Symbol')</label>
                                    <input type="text" class="form-control" name="trading_chart_symbol"
                                           value="{{ old('trading_chart_symbol', basicControl()->trading_chart_symbol ?: 'BINANCE:BTCUSDT') }}"
                                           placeholder="BINANCE:BTCUSDT">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Chart Interval')</label>
                                    <input type="text" class="form-control" name="trading_chart_interval"
                                           value="{{ old('trading_chart_interval', basicControl()->trading_chart_interval ?: '60') }}"
                                           placeholder="60 / D / 240">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Chart Theme')</label>
                                    <select class="form-select" name="trading_chart_theme">
                                        <option value="dark" @selected(basicControl()->trading_chart_theme === 'dark')>@lang('Dark')</option>
                                        <option value="light" @selected(basicControl()->trading_chart_theme === 'light')>@lang('Light')</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Allowed Symbols (comma separated)')</label>
                                    <textarea class="form-control" name="trading_symbols" id="trading_symbols" rows="6"
                                              placeholder="NASDAQ:AAPL, NASDAQ:TSLA, BTC/USD, ETH/USD">{{ old('trading_symbols', collect($savedSymbols ?: $defaultSymbols)->implode(', ')) }}</textarea>
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary js-symbol-pack" data-pack="{{ implode(', ', $defaultSymbols) }}">@lang('Load Diversified Set')</button>
                                        <button type="button" class="btn btn-sm btn-outline-warning js-symbol-pack" data-pack="{{ implode(', ', $cryptoPack) }}">@lang('Add Crypto Pack')</button>
                                        <button type="button" class="btn btn-sm btn-outline-info js-symbol-pack" data-pack="{{ implode(', ', $usEquityPack) }}">@lang('Add US Equity Pack')</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary js-symbol-pack" data-pack="{{ implode(', ', $macroPack) }}">@lang('Add FX + Gold Pack')</button>
                                    </div>
                                    <small class="text-muted d-block mt-2">@lang('Supports comma, space, or newline. Format examples: NASDAQ:AAPL, BTC/USD, EUR/USD. Users can only trade symbols listed here.')</small>
                                </div>
                            </div>

                            <h5 class="mb-3">@lang('Risk + Fees')</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Minimum Order Amount')</label>
                                    <input type="number" class="form-control" name="trading_min_order_amount"
                                           value="{{ old('trading_min_order_amount', basicControl()->trading_min_order_amount ?? 10) }}"
                                           min="0.01" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Maximum Order Amount')</label>
                                    <input type="number" class="form-control" name="trading_max_order_amount"
                                           value="{{ old('trading_max_order_amount', basicControl()->trading_max_order_amount ?? 100000) }}"
                                           min="0.01" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Fee Percent (%)')</label>
                                    <input type="number" class="form-control" name="trading_fee_percent"
                                           value="{{ old('trading_fee_percent', basicControl()->trading_fee_percent ?? 0) }}"
                                           min="0" step="0.01">
                                </div>
                            </div>

                            <h5 class="mb-3">@lang('Broker Integration (Alpaca)')</h5>
                            <div class="alert alert-soft-warning">
                                <strong>@lang('Security note:')</strong>
                                @lang('API credentials are encrypted before being stored. Keep access to admin panel restricted.')
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">@lang('Broker Enabled')</label>
                                    <select class="form-select" name="broker_enabled">
                                        <option value="1" @selected(optional($brokerConfig)->is_enabled)>@lang('Yes')</option>
                                        <option value="0" @selected(!optional($brokerConfig)->is_enabled)>@lang('No')</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">@lang('Provider')</label>
                                    <select class="form-select" name="broker_provider">
                                        <option value="alpaca" @selected(optional($brokerConfig)->provider === 'alpaca')>Alpaca</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">@lang('Mode')</label>
                                    <select class="form-select" name="broker_mode">
                                        <option value="paper" @selected(optional($brokerConfig)->mode === 'paper')>@lang('Paper')</option>
                                        <option value="live" @selected(optional($brokerConfig)->mode === 'live')>@lang('Live')</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">@lang('Stocks Data Feed')</label>
                                    <select class="form-select" name="broker_data_feed">
                                        <option value="iex" @selected(optional($brokerConfig)->data_feed === 'iex')>IEX</option>
                                        <option value="sip" @selected(optional($brokerConfig)->data_feed === 'sip')>SIP</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('API Key')</label>
                                    <input type="password" class="form-control" name="broker_api_key" autocomplete="new-password" placeholder="@lang('Leave blank to keep current key')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('API Secret')</label>
                                    <input type="password" class="form-control" name="broker_api_secret" autocomplete="new-password" placeholder="@lang('Leave blank to keep current secret')">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">@lang('Custom Base URL (optional)')</label>
                                    <input type="url" class="form-control" name="broker_custom_base_url"
                                           value="{{ old('broker_custom_base_url', optional($brokerConfig)->custom_base_url) }}"
                                           placeholder="https://paper-api.alpaca.markets">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn btn-primary px-4" type="submit">@lang('Save Trading Configuration')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function () {
            const symbolField = document.getElementById('trading_symbols');
            const packButtons = document.querySelectorAll('.js-symbol-pack');

            function normalizeSymbol(value) {
                return value.trim().toUpperCase();
            }

            function tokenize(value) {
                return value
                    .split(/[\s,;]+/)
                    .map(normalizeSymbol)
                    .filter(Boolean);
            }

            function mergeSymbols(currentValue, packValue) {
                const currentSymbols = tokenize(currentValue);
                const packSymbols = tokenize(packValue);
                const merged = [...new Set([...currentSymbols, ...packSymbols])];
                return merged.join(', ');
            }

            packButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    symbolField.value = mergeSymbols(symbolField.value, this.dataset.pack || '');
                });
            });
        })();
    </script>
@endpush

