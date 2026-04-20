<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Events\TradingTickUpdated;
use App\Models\TradingOrder;
use App\Models\TradingPosition;
use App\Models\Transaction;
use App\Services\Trading\MarketDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TradingController extends Controller
{
    private const DEFAULT_TRADING_SYMBOLS = [
        'NASDAQ:AAPL', 'NASDAQ:MSFT', 'NASDAQ:NVDA', 'NASDAQ:TSLA', 'NASDAQ:AMZN',
        'NASDAQ:META', 'NASDAQ:GOOGL', 'NASDAQ:AMD', 'NYSE:JPM', 'NYSE:XOM',
        'NYSE:V', 'NYSE:MA', 'NYSE:WMT', 'NYSE:KO', 'NYSE:NKE',
        'BTC/USD', 'ETH/USD', 'SOL/USD', 'LTC/USD', 'XRP/USD',
        'DOGE/USD', 'AVAX/USD', 'BNB/USD', 'DOT/USD', 'MATIC/USD',
        'EUR/USD', 'GBP/USD', 'USD/JPY', 'XAU/USD',
    ];

    public function __construct(
        private readonly MarketDataService $marketDataService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $settings = $this->tradingSettings();

        if (!$settings['enabled']) {
            return back()->with('error', 'Trading is currently disabled by admin.');
        }

        $positions = TradingPosition::where('user_id', $user->id)
            ->orderBy('symbol')
            ->get();

        $orders = TradingOrder::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view(template() . 'user.trading.index', compact('settings', 'positions', 'orders', 'user'));
    }

    public function quote(Request $request)
    {
        $settings = $this->tradingSettings();
        if (!$settings['enabled']) {
            return response()->json(['message' => 'Trading is disabled'], 422);
        }

        $request->validate(['symbol' => 'required|string|max:30']);
        $symbol = $this->normalizeUserSymbol($request->symbol);
        $allowedSymbols = $settings['symbols'];

        if (!in_array($symbol, $allowedSymbols, true)) {
            return response()->json(['message' => 'Symbol is not allowed'], 422);
        }

        $snapshot = $this->fetchMarketSnapshot($symbol);
        if ($snapshot === null || empty($snapshot['price'])) {
            return response()->json(['message' => 'Unable to fetch market price right now'], 422);
        }

        $this->broadcastTick($snapshot);

        return response()->json([
            'symbol' => $symbol,
            'price' => (float) $snapshot['price'],
            'bid' => $snapshot['bid'] ?? null,
            'ask' => $snapshot['ask'] ?? null,
            'spread' => $snapshot['spread'] ?? null,
            'source' => $snapshot['source'] ?? 'fallback',
            'orderbook' => $snapshot['orderbook'] ?? $this->marketDataService->syntheticOrderbook((float) $snapshot['price']),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    public function orderbook(Request $request)
    {
        $settings = $this->tradingSettings();
        if (!$settings['enabled']) {
            return response()->json(['message' => 'Trading is disabled'], 422);
        }

        $request->validate(['symbol' => 'required|string|max:30']);
        $symbol = $this->normalizeUserSymbol($request->symbol);
        if (!in_array($symbol, $settings['symbols'], true)) {
            return response()->json(['message' => 'Symbol is not allowed'], 422);
        }

        $snapshot = $this->fetchMarketSnapshot($symbol);
        if (!$snapshot || empty($snapshot['price'])) {
            return response()->json(['message' => 'Unable to build orderbook right now'], 422);
        }

        return response()->json([
            'symbol' => $symbol,
            'price' => (float) $snapshot['price'],
            'bid' => $snapshot['bid'] ?? null,
            'ask' => $snapshot['ask'] ?? null,
            'spread' => $snapshot['spread'] ?? null,
            'orderbook' => $snapshot['orderbook'] ?? $this->marketDataService->syntheticOrderbook((float) $snapshot['price']),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    public function snapshot(Request $request)
    {
        $settings = $this->tradingSettings();
        if (!$settings['enabled']) {
            return response()->json([
                'enabled' => false,
                'message' => 'Trading is disabled by admin.',
            ]);
        }

        return response()->json($this->buildUserTradingSnapshot(Auth::id()));
    }

    public function placeOrder(Request $request)
    {
        $settings = $this->tradingSettings();
        if (!$settings['enabled']) {
            return $this->orderResponse($request, false, 'Trading is currently disabled by admin.', 422);
        }

        $request->validate([
            'symbol' => ['required', 'string', 'max:30'],
            'side' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'take_profit_price' => 'nullable|numeric|min:0.00000001',
            'stop_loss_price' => 'nullable|numeric|min:0.00000001',
        ]);

        $symbol = $this->normalizeUserSymbol($request->symbol);
        if (!in_array($symbol, $settings['symbols'], true)) {
            return $this->orderResponse($request, false, 'Symbol is not allowed by admin configuration.', 422);
        }

        $side = $request->side;
        $quantity = (float) $request->quantity;
        $takeProfitPrice = $request->filled('take_profit_price') ? (float) $request->take_profit_price : null;
        $stopLossPrice = $request->filled('stop_loss_price') ? (float) $request->stop_loss_price : null;

        if ($side === 'buy' && !$settings['allow_buy']) {
            return $this->orderResponse($request, false, 'Buy orders are disabled by admin.', 422);
        }
        if ($side === 'sell' && !$settings['allow_sell']) {
            return $this->orderResponse($request, false, 'Sell orders are disabled by admin.', 422);
        }

        if ($takeProfitPrice !== null && $stopLossPrice !== null && $stopLossPrice >= $takeProfitPrice) {
            return $this->orderResponse($request, false, 'Stop loss must be lower than take profit.', 422);
        }

        $marketSnapshot = $this->fetchMarketSnapshot($symbol);
        $price = $marketSnapshot['price'] ?? null;
        if ($price === null) {
            return $this->orderResponse($request, false, 'Unable to fetch current price. Please try again.', 422);
        }

        $grossAmount = round($quantity * $price, 2);
        if ($grossAmount < $settings['min_order_amount'] || $grossAmount > $settings['max_order_amount']) {
            return $this->orderResponse($request, false, 'Order amount is outside admin limits.', 422);
        }

        $feeAmount = round($grossAmount * ($settings['fee_percent'] / 100), 2);
        $netAmount = $side === 'buy'
            ? round($grossAmount + $feeAmount, 2)
            : round($grossAmount - $feeAmount, 2);

        if ($netAmount <= 0) {
            return $this->orderResponse($request, false, 'Invalid order net amount.', 422);
        }

        $isLiveOrder = false;
        $brokerOrderId = null;
        $brokerStatus = null;
        $brokerName = null;
        $filledQuantity = $quantity;
        $remainingQuantity = 0.0;
        $lifecycleStatus = 'filled';

        $user = Auth::user();

        try {
            DB::transaction(function () use (
                $user,
                $symbol,
                $side,
                $quantity,
                $price,
                $grossAmount,
                $feeAmount,
                $netAmount,
                $takeProfitPrice,
                $stopLossPrice,
                $isLiveOrder,
                $brokerName,
                $brokerOrderId,
                $brokerStatus,
                $filledQuantity,
                $remainingQuantity,
                $lifecycleStatus
            ) {
                $orderPositionId = null;
                $realizedPnl = 0.0;
                $realizedPnlPercent = null;
                $walletImpactAmount = 0.0;
                $walletImpactType = $side === 'buy' ? '-' : '+';

                $position = TradingPosition::where('user_id', $user->id)
                    ->where('symbol', $symbol)
                    ->lockForUpdate()
                    ->first();

                if ($side === 'buy') {
                    if ($user->balance < $netAmount) {
                        throw new \RuntimeException('Insufficient wallet balance for this buy order.');
                    }

                    $existingQty = (float) optional($position)->quantity;
                    $existingInvested = (float) optional($position)->invested_amount;
                    $newQty = $existingQty + $quantity;
                    $newInvested = round($existingInvested + $grossAmount, 2);
                    $newAvgPrice = $newQty > 0 ? round($newInvested / $newQty, 8) : 0;

                    if (!$position) {
                        $position = new TradingPosition([
                            'user_id' => $user->id,
                            'symbol' => $symbol,
                        ]);
                    }

                    $position->quantity = $newQty;
                    $position->invested_amount = $newInvested;
                    $position->average_price = $newAvgPrice;
                    $position->save();
                    $orderPositionId = $position->id;

                    $user->balance = round($user->balance - $netAmount, 2);
                    $walletImpactAmount = $netAmount;
                    $walletImpactType = '-';
                    $user->save();
                } else {
                    if (!$position || (float) $position->quantity < $quantity) {
                        throw new \RuntimeException('Not enough quantity in your position to sell.');
                    }

                    $remainingQty = round(((float) $position->quantity - $quantity), 8);
                    $entryCost = round((float) $position->average_price * $quantity, 2);
                    $remainingInvested = round(max(0, (float) $position->invested_amount - $entryCost), 2);
                    $realizedPnl = round($grossAmount - $entryCost - $feeAmount, 2);
                    $realizedPnlPercent = $entryCost > 0 ? round(($realizedPnl / $entryCost) * 100, 2) : null;

                    // Wallet accounting policy:
                    // - Return principal (entry cost) to wallet balance
                    // - Route realized profit to profit_balance
                    // - Let realized loss reduce wallet return amount
                    $walletCredit = $realizedPnl > 0
                        ? $entryCost
                        : round(max(0, $entryCost + $realizedPnl), 2);

                    if ($remainingQty <= 0) {
                        // Position is fully closed, keep order FK nullable to avoid orphan FK error.
                        $position->delete();
                        $orderPositionId = null;
                    } else {
                        $position->quantity = $remainingQty;
                        $position->invested_amount = $remainingInvested;
                        $position->average_price = $remainingQty > 0 ? round($remainingInvested / $remainingQty, 8) : 0;
                        $position->save();
                        $orderPositionId = $position->id;
                    }

                    $user->balance = round($user->balance + $walletCredit, 2);
                    if ($realizedPnl > 0) {
                        $user->profit_balance = round((float) $user->profit_balance + $realizedPnl, 2);
                    }
                    $walletImpactAmount = $walletCredit;
                    $walletImpactType = '+';
                    $user->save();
                }

                $order = TradingOrder::create([
                    'user_id' => $user->id,
                    'trading_position_id' => $orderPositionId,
                    'symbol' => $symbol,
                    'side' => $side,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'gross_amount' => $grossAmount,
                    'fee_amount' => $feeAmount,
                    'net_amount' => $netAmount,
                    'realized_pnl' => $realizedPnl,
                    'realized_pnl_percent' => $realizedPnlPercent,
                    'trx_id' => 'TRD-' . strtoupper(Str::random(12)),
                    'take_profit_price' => $takeProfitPrice,
                    'stop_loss_price' => $stopLossPrice,
                    'is_live_order' => $isLiveOrder,
                    'broker_name' => $brokerName,
                    'broker_order_id' => $brokerOrderId,
                    'broker_status' => $brokerStatus,
                    'filled_quantity' => $filledQuantity,
                    'remaining_quantity' => $remainingQuantity,
                    'lifecycle_status' => $lifecycleStatus,
                    'last_synced_at' => now(),
                ]);

                Transaction::create([
                    'transactional_id' => $order->id,
                    'transactional_type' => TradingOrder::class,
                    'user_id' => $user->id,
                    'amount' => $walletImpactAmount,
                    'balance' => $user->balance,
                    'charge' => $feeAmount,
                    'trx_type' => $walletImpactType,
                    'remarks' => sprintf('Trading %s order (%s)%s', $side, $symbol, $isLiveOrder ? ' [LIVE]' : ''),
                    'trx_id' => $order->trx_id,
                ]);
            });
        } catch (\RuntimeException $exception) {
            return $this->orderResponse($request, false, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            return $this->orderResponse($request, false, 'Order failed. Please try again. ' . $exception->getMessage(), 500);
        }

        return $this->orderResponse(
            $request,
            true,
            'Order executed successfully.',
            200,
            [
                'snapshot' => $this->buildUserTradingSnapshot($user->id),
            ]
        );
    }

    private function tradingSettings(): array
    {
        $basic = basicControl();
        $symbols = $basic->trading_symbols;

        if (is_string($symbols)) {
            $decoded = json_decode($symbols, true);
            $symbols = is_array($decoded) ? $decoded : [];
        }

        $symbols = collect($symbols ?: self::DEFAULT_TRADING_SYMBOLS)
            ->map(function ($symbol) {
                return strtoupper(trim((string) $symbol));
            })
            ->filter(function ($symbol) {
                return preg_match('/^[A-Z0-9:\/\.\-_]{2,30}$/', $symbol);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'enabled' => (bool) $basic->trading_enabled,
            'allow_buy' => (bool) $basic->trading_allow_buy,
            'allow_sell' => (bool) $basic->trading_allow_sell,
            'chart_symbol' => $basic->trading_chart_symbol ?: 'NASDAQ:AAPL',
            'chart_interval' => $basic->trading_chart_interval ?: 'D',
            'chart_theme' => $basic->trading_chart_theme ?: 'dark',
            'symbols' => $symbols,
            'fee_percent' => (float) $basic->trading_fee_percent,
            'min_order_amount' => (float) $basic->trading_min_order_amount,
            'max_order_amount' => (float) $basic->trading_max_order_amount,
        ];
    }

    private function fetchMarketSnapshot(string $symbol): ?array
    {
        $cacheKey = 'trading_market_snapshot_' . md5($symbol);

        return Cache::remember($cacheKey, now()->addSeconds(8), function () use ($symbol) {
            $fallback = $this->marketDataService->fallbackQuoteSnapshot($symbol);
            if (!$fallback || empty($fallback['price'])) {
                return null;
            }

            $price = (float) $fallback['price'];
            $bid = isset($fallback['bid']) ? (float) $fallback['bid'] : null;
            $ask = isset($fallback['ask']) ? (float) $fallback['ask'] : null;

            return [
                'symbol' => strtoupper($symbol),
                'price' => $price,
                'bid' => $bid,
                'ask' => $ask,
                'spread' => ($bid !== null && $ask !== null) ? round($ask - $bid, 8) : null,
                'source' => $fallback['source'] ?? 'fallback',
                'orderbook' => $this->marketDataService->syntheticOrderbook($price, $bid, $ask, 10),
            ];
        });
    }

    private function normalizeUserSymbol(string $symbol): string
    {
        return strtoupper(trim($symbol));
    }

    private function orderResponse(Request $request, bool $success, string $message, int $statusCode = 200, array $extra = [])
    {
        $payload = array_merge([
            'success' => $success,
            'message' => $message,
        ], $extra);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($payload, $statusCode);
        }

        if ($success) {
            return back()->with('success', $message);
        }

        return back()->with('error', $message);
    }

    private function buildUserTradingSnapshot(int $userId): array
    {
        $user = Auth::user();

        $positions = TradingPosition::query()
            ->where('user_id', $userId)
            ->orderBy('symbol')
            ->get(['symbol', 'quantity', 'average_price', 'invested_amount']);

        $recentOrders = TradingOrder::query()
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get(['trx_id', 'symbol', 'side', 'net_amount', 'realized_pnl', 'realized_pnl_percent', 'is_live_order', 'broker_status', 'lifecycle_status', 'filled_quantity', 'remaining_quantity', 'quantity', 'created_at']);

        $marketValueTotal = 0.0;
        $unrealizedPnlTotal = 0.0;
        $symbolExposure = [];
        $positionMetrics = [];
        foreach ($positions as $position) {
            $snapshot = $this->fetchMarketSnapshot($position->symbol);
            $latestPrice = (float) ($snapshot['price'] ?? $position->average_price ?? 0);
            $marketValue = round(((float) $position->quantity) * $latestPrice, 2);
            $unrealized = round($marketValue - (float) $position->invested_amount, 2);
            $roiPercent = (float) $position->invested_amount > 0
                ? round(($unrealized / (float) $position->invested_amount) * 100, 2)
                : 0.0;
            $marketValueTotal += $marketValue;
            $unrealizedPnlTotal += $unrealized;
            $symbolExposure[$position->symbol] = ($symbolExposure[$position->symbol] ?? 0) + $marketValue;
            $positionMetrics[$position->symbol] = [
                'mark_price' => $latestPrice,
                'market_value' => $marketValue,
                'unrealized_pnl' => $unrealized,
                'unrealized_roi_percent' => $roiPercent,
            ];
        }

        $tradingInvestedTotal = (float) $positions->sum('invested_amount');
        $equity = round((float) $user->balance + $marketValueTotal, 2);
        $marginUtilization = $equity > 0 ? round(($tradingInvestedTotal / $equity) * 100, 2) : 0;
        $openPnlPercent = $tradingInvestedTotal > 0 ? round(($unrealizedPnlTotal / $tradingInvestedTotal) * 100, 2) : 0;
        $closedPnlTotal = (float) TradingOrder::query()
            ->where('user_id', $userId)
            ->where('side', 'sell')
            ->sum('realized_pnl');
        $closedPnlToday = (float) TradingOrder::query()
            ->where('user_id', $userId)
            ->where('side', 'sell')
            ->whereDate('created_at', now()->toDateString())
            ->sum('realized_pnl');
        $closedPnl7d = (float) TradingOrder::query()
            ->where('user_id', $userId)
            ->where('side', 'sell')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('realized_pnl');
        $realizedProfitWallet = (float) $user->profit_balance;
        $historyStart = now()->subDays(29)->startOfDay();
        $historyRows = TradingOrder::query()
            ->where('user_id', $userId)
            ->where('side', 'sell')
            ->where('created_at', '>=', $historyStart)
            ->selectRaw('DATE(created_at) as trade_date')
            ->selectRaw('SUM(realized_pnl) as net_pnl')
            ->selectRaw('SUM(CASE WHEN realized_pnl > 0 THEN realized_pnl ELSE 0 END) as profit_pnl')
            ->selectRaw('SUM(CASE WHEN realized_pnl < 0 THEN ABS(realized_pnl) ELSE 0 END) as loss_pnl')
            ->groupBy('trade_date')
            ->orderBy('trade_date')
            ->get();

        $historyMap = $historyRows->keyBy('trade_date');
        $historyLabels = [];
        $historyNetSeries = [];
        $historyProfitSeries = [];
        $historyLossSeries = [];
        $historyCumulativeSeries = [];
        $cumulativePnl = 0.0;

        for ($dayOffset = 29; $dayOffset >= 0; $dayOffset--) {
            $date = now()->subDays($dayOffset);
            $dateKey = $date->toDateString();
            $historyLabels[] = $date->format('M d');

            $daily = $historyMap->get($dateKey);
            $net = (float) data_get($daily, 'net_pnl', 0);
            $profit = (float) data_get($daily, 'profit_pnl', 0);
            $loss = (float) data_get($daily, 'loss_pnl', 0);

            $cumulativePnl += $net;
            $historyNetSeries[] = round($net, 2);
            $historyProfitSeries[] = round($profit, 2);
            $historyLossSeries[] = round($loss, 2);
            $historyCumulativeSeries[] = round($cumulativePnl, 2);
        }
        arsort($symbolExposure);
        $topExposureValue = (float) (reset($symbolExposure) ?: 0);
        $concentration = $marketValueTotal > 0 ? round(($topExposureValue / $marketValueTotal) * 100, 2) : 0;

        return [
            'enabled' => true,
            'wallet_balance' => (float) $user->balance,
            'profit_balance' => (float) $user->profit_balance,
            'open_positions_count' => $positions->count(),
            'trading_invested_total' => $tradingInvestedTotal,
            'trading_orders_total' => TradingOrder::query()->where('user_id', $userId)->count(),
            'trading_buy_volume' => (float) TradingOrder::query()->where('user_id', $userId)->where('side', 'buy')->sum('gross_amount'),
            'trading_sell_volume' => (float) TradingOrder::query()->where('user_id', $userId)->where('side', 'sell')->sum('gross_amount'),
            'risk' => [
                'equity' => $equity,
                'market_value_total' => round($marketValueTotal, 2),
                'unrealized_pnl_total' => round($unrealizedPnlTotal, 2),
                'open_pnl_percent' => $openPnlPercent,
                'closed_pnl_total' => round($closedPnlTotal, 2),
                'closed_pnl_today' => round($closedPnlToday, 2),
                'closed_pnl_7d' => round($closedPnl7d, 2),
                'profit_wallet_realized' => round($realizedProfitWallet, 2),
                'margin_utilization_percent' => $marginUtilization,
                'top_symbol_concentration_percent' => $concentration,
            ],
            'pnl_history' => [
                'labels' => $historyLabels,
                'net' => $historyNetSeries,
                'profit' => $historyProfitSeries,
                'loss' => $historyLossSeries,
                'cumulative' => $historyCumulativeSeries,
            ],
            'positions' => $positions->map(function ($position) use ($positionMetrics) {
                $metrics = $positionMetrics[$position->symbol] ?? null;
                return [
                    'symbol' => $position->symbol,
                    'quantity' => (float) $position->quantity,
                    'average_price' => (float) $position->average_price,
                    'invested_amount' => (float) $position->invested_amount,
                    'mark_price' => (float) ($metrics['mark_price'] ?? 0),
                    'market_value' => (float) ($metrics['market_value'] ?? 0),
                    'unrealized_pnl' => (float) ($metrics['unrealized_pnl'] ?? 0),
                    'unrealized_roi_percent' => (float) ($metrics['unrealized_roi_percent'] ?? 0),
                ];
            })->values(),
            'recent_orders' => $recentOrders->map(function ($order) {
                return [
                    'trx_id' => $order->trx_id,
                    'symbol' => $order->symbol,
                    'side' => $order->side,
                    'net_amount' => (float) $order->net_amount,
                    'realized_pnl' => (float) ($order->realized_pnl ?? 0),
                    'realized_pnl_percent' => $order->realized_pnl_percent !== null ? (float) $order->realized_pnl_percent : null,
                    'is_live_order' => (bool) $order->is_live_order,
                    'broker_status' => $order->broker_status,
                    'lifecycle_status' => $order->lifecycle_status ?: $this->mapLifecycleStatus((string) ($order->broker_status ?? ''), (float) ($order->filled_quantity ?? 0), (float) ($order->quantity ?? 0)),
                    'filled_quantity' => (float) ($order->filled_quantity ?? 0),
                    'remaining_quantity' => (float) ($order->remaining_quantity ?? 0),
                    'quantity' => (float) ($order->quantity ?? 0),
                    'created_at' => optional($order->created_at)->toDateTimeString(),
                ];
            })->values(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }

    private function mapLifecycleStatus(string $brokerStatus, float $filledQuantity, float $orderQuantity): string
    {
        $status = strtolower(trim($brokerStatus));

        if (in_array($status, ['new', 'accepted', 'pending_new', 'pending_replace', 'pending_cancel', 'held'], true)) {
            return 'pending';
        }

        if ($status === 'partially_filled' || ($filledQuantity > 0 && $filledQuantity < $orderQuantity)) {
            return 'partial';
        }

        if (in_array($status, ['filled', 'done_for_day'], true) || $filledQuantity >= $orderQuantity) {
            return 'filled';
        }

        if (in_array($status, ['canceled', 'cancelled', 'expired', 'rejected', 'suspended'], true)) {
            return 'cancelled';
        }

        return 'pending';
    }

    private function broadcastTick(array $snapshot): void
    {
        if (empty($snapshot['symbol']) || empty($snapshot['price'])) {
            return;
        }

        event(new TradingTickUpdated([
            'symbol' => $snapshot['symbol'],
            'price' => (float) $snapshot['price'],
            'bid' => $snapshot['bid'] ?? null,
            'ask' => $snapshot['ask'] ?? null,
            'spread' => $snapshot['spread'] ?? null,
            'source' => $snapshot['source'] ?? 'fallback',
            'orderbook' => $snapshot['orderbook'] ?? [],
            'updated_at' => now()->toDateTimeString(),
        ]));
    }
}

