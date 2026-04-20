<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradingBrokerConfig;
use Illuminate\Http\Request;

class TradingSettingController extends Controller
{
    private const DEFAULT_TRADING_SYMBOLS = [
        'NASDAQ:AAPL', 'NASDAQ:MSFT', 'NASDAQ:NVDA', 'NASDAQ:TSLA', 'NASDAQ:AMZN',
        'NASDAQ:META', 'NASDAQ:GOOGL', 'NASDAQ:AMD', 'NYSE:JPM', 'NYSE:XOM',
        'NYSE:V', 'NYSE:MA', 'NYSE:WMT', 'NYSE:KO', 'NYSE:NKE',
        'BTC/USD', 'ETH/USD', 'SOL/USD', 'LTC/USD', 'XRP/USD',
        'DOGE/USD', 'AVAX/USD', 'BNB/USD', 'DOT/USD', 'MATIC/USD',
        'EUR/USD', 'GBP/USD', 'USD/JPY', 'XAU/USD',
    ];

    public function index()
    {
        $brokerConfig = TradingBrokerConfig::firstOrCreate([], [
            'provider' => 'alpaca',
            'mode' => 'paper',
            'is_enabled' => false,
            'data_feed' => 'iex',
        ]);

        return view('admin.control_panel.trading_settings', compact('brokerConfig'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'trading_chart_symbol' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9:\/\.\-_]+$/'],
            'trading_chart_interval' => 'required|string|max:10',
            'trading_chart_theme' => 'required|in:light,dark',
            'trading_symbols' => 'required|string|max:4000',
            'trading_min_order_amount' => 'required|numeric|min:0.01',
            'trading_max_order_amount' => 'required|numeric|gte:trading_min_order_amount',
            'trading_fee_percent' => 'required|numeric|min:0|max:100',
            'broker_provider' => 'required|in:alpaca',
            'broker_mode' => 'required|in:paper,live',
            'broker_data_feed' => 'required|in:iex,sip',
            'broker_custom_base_url' => 'nullable|url',
            'broker_api_key' => 'nullable|string|max:200',
            'broker_api_secret' => 'nullable|string|max:200',
        ]);

        $symbols = collect(preg_split('/[\s,;]+/', $validated['trading_symbols']))
            ->map(function ($symbol) {
                return strtoupper(trim($symbol));
            })
            ->filter(function ($symbol) {
                return preg_match('/^[A-Z0-9:\/\.\-_]{2,30}$/', $symbol);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (count($symbols) === 0) {
            $symbols = self::DEFAULT_TRADING_SYMBOLS;
        }

        $basic = basicControl();
        $basic->update([
            'trading_enabled' => $request->boolean('trading_enabled'),
            'trading_dashboard_chart_enabled' => $request->boolean('trading_dashboard_chart_enabled'),
            'trading_homepage_chart_enabled' => $request->boolean('trading_homepage_chart_enabled'),
            'trading_allow_buy' => $request->boolean('trading_allow_buy'),
            'trading_allow_sell' => $request->boolean('trading_allow_sell'),
            'trading_chart_symbol' => strtoupper($validated['trading_chart_symbol']),
            'trading_chart_interval' => strtoupper($validated['trading_chart_interval']),
            'trading_chart_theme' => $validated['trading_chart_theme'],
            'trading_symbols' => $symbols,
            'trading_min_order_amount' => $validated['trading_min_order_amount'],
            'trading_max_order_amount' => $validated['trading_max_order_amount'],
            'trading_fee_percent' => $validated['trading_fee_percent'],
        ]);

        $brokerConfig = TradingBrokerConfig::firstOrCreate();
        $brokerConfig->provider = $validated['broker_provider'];
        $brokerConfig->mode = $validated['broker_mode'];
        $brokerConfig->is_enabled = $request->boolean('broker_enabled');
        $brokerConfig->data_feed = $validated['broker_data_feed'];
        $brokerConfig->custom_base_url = $validated['broker_custom_base_url'] ?? null;
        if (!empty($validated['broker_api_key'])) {
            $brokerConfig->api_key = trim($validated['broker_api_key']);
        }
        if (!empty($validated['broker_api_secret'])) {
            $brokerConfig->api_secret = trim($validated['broker_api_secret']);
        }
        $brokerConfig->save();

        return back()->with('success', 'Trading settings updated successfully.');
    }
}

