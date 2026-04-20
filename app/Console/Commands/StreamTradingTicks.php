<?php

namespace App\Console\Commands;

use App\Events\TradingTickUpdated;
use App\Models\TradingBrokerConfig;
use App\Services\Trading\AlpacaBrokerService;
use App\Services\Trading\MarketDataService;
use Illuminate\Console\Command;

class StreamTradingTicks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading:stream-ticks {--interval=2 : Seconds between tick updates} {--symbols= : Optional comma separated symbols override}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast live trading ticks over websocket channels';

    public function __construct(private readonly MarketDataService $marketDataService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $interval = max(1, (int) $this->option('interval'));
        $symbolsOverride = trim((string) $this->option('symbols'));

        $this->info("Trading tick stream started. Interval: {$interval}s");

        while (true) {
            $symbols = $this->resolveSymbols($symbolsOverride);
            $brokerConfig = TradingBrokerConfig::first();
            $brokerService = $brokerConfig ? new AlpacaBrokerService($brokerConfig) : null;

            foreach ($symbols as $symbol) {
                $snapshot = null;
                if ($brokerService && $brokerService->isReady()) {
                    $snapshot = $brokerService->latestQuoteSnapshot($symbol);
                }

                if (!$snapshot) {
                    $snapshot = $this->marketDataService->fallbackQuoteSnapshot($symbol);
                }

                if (!$snapshot || empty($snapshot['price'])) {
                    continue;
                }

                $orderbook = $this->marketDataService->syntheticOrderbook(
                    (float) $snapshot['price'],
                    isset($snapshot['bid']) ? (float) $snapshot['bid'] : null,
                    isset($snapshot['ask']) ? (float) $snapshot['ask'] : null,
                    8
                );

                event(new TradingTickUpdated([
                    'symbol' => strtoupper($symbol),
                    'price' => (float) $snapshot['price'],
                    'bid' => isset($snapshot['bid']) ? (float) $snapshot['bid'] : null,
                    'ask' => isset($snapshot['ask']) ? (float) $snapshot['ask'] : null,
                    'spread' => (isset($snapshot['ask'], $snapshot['bid']) && $snapshot['ask'] !== null && $snapshot['bid'] !== null)
                        ? (float) $snapshot['ask'] - (float) $snapshot['bid']
                        : null,
                    'source' => $snapshot['source'] ?? 'fallback',
                    'orderbook' => $orderbook,
                    'updated_at' => now()->toDateTimeString(),
                ]));
            }

            sleep($interval);
        }
    }

    private function resolveSymbols(string $symbolsOverride): array
    {
        if ($symbolsOverride !== '') {
            return collect(preg_split('/[\s,;]+/', $symbolsOverride))
                ->map(fn ($item) => strtoupper(trim((string) $item)))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $symbols = basicControl()->trading_symbols;
        if (is_string($symbols)) {
            $decoded = json_decode($symbols, true);
            $symbols = is_array($decoded) ? $decoded : [];
        }

        return collect($symbols ?: ['NASDAQ:AAPL', 'BTC/USD', 'ETH/USD'])
            ->map(fn ($item) => strtoupper(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

