<?php

namespace App\Services\Trading;

use Illuminate\Support\Facades\Http;

class MarketDataService
{
    private const SPECIAL_YAHOO_TICKERS = [
        'XAU/USD' => ['GC=F', 'XAUUSD=X'],
        'XAG/USD' => ['SI=F', 'XAGUSD=X'],
        'WTI/USD' => ['CL=F'],
        'BRENT/USD' => ['BZ=F'],
    ];

    public function fallbackPrice(string $symbol): ?float
    {
        $snapshot = $this->fallbackQuoteSnapshot($symbol);
        return $snapshot['price'] ?? null;
    }

    public function fallbackQuoteSnapshot(string $symbol): ?array
    {
        $normalizedSymbol = strtoupper(trim($symbol));
        $tickers = $this->extractTickerCandidates($normalizedSymbol);

        try {
            foreach ($tickers as $ticker) {
                $chartResponse = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://query1.finance.yahoo.com/v8/finance/chart/' . rawurlencode($ticker));

                if ($chartResponse->ok()) {
                    $price = data_get($chartResponse->json(), 'chart.result.0.meta.regularMarketPrice');
                    if ($price !== null) {
                        $price = (float) $price;
                        if ($this->isReasonablePrice($normalizedSymbol, $price)) {
                            return [
                                'symbol' => $normalizedSymbol,
                                'price' => $price,
                                'bid' => null,
                                'ask' => null,
                                'source' => 'yahoo-chart',
                            ];
                        }
                    }
                }

                $quoteResponse = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://query1.finance.yahoo.com/v7/finance/quote', [
                        'symbols' => $ticker,
                    ]);

                if ($quoteResponse->ok()) {
                    $price = data_get($quoteResponse->json(), 'quoteResponse.result.0.regularMarketPrice');
                    if ($price !== null) {
                        $price = (float) $price;
                        if ($this->isReasonablePrice($normalizedSymbol, $price)) {
                            $bid = data_get($quoteResponse->json(), 'quoteResponse.result.0.bid');
                            $ask = data_get($quoteResponse->json(), 'quoteResponse.result.0.ask');

                            return [
                                'symbol' => $normalizedSymbol,
                                'price' => $price,
                                'bid' => $bid !== null ? (float) $bid : null,
                                'ask' => $ask !== null ? (float) $ask : null,
                                'source' => 'yahoo-quote',
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $exception) {
            return null;
        }

        return null;
    }

    public function syntheticOrderbook(float $price, ?float $bid = null, ?float $ask = null, int $levels = 8): array
    {
        $baseBid = $bid ?: $price * 0.9995;
        $baseAsk = $ask ?: $price * 1.0005;
        $tick = max($price * 0.0005, 0.0001);

        $bids = [];
        $asks = [];

        for ($i = 0; $i < $levels; $i++) {
            $depthMultiplier = $i + 1;
            $bids[] = [
                'price' => round($baseBid - ($tick * $i), 8),
                'size' => round(max(0.01, (18 / $depthMultiplier) + mt_rand(1, 30) / 10), 4),
            ];

            $asks[] = [
                'price' => round($baseAsk + ($tick * $i), 8),
                'size' => round(max(0.01, (18 / $depthMultiplier) + mt_rand(1, 30) / 10), 4),
            ];
        }

        return [
            'bids' => $bids,
            'asks' => $asks,
        ];
    }

    private function extractTickerCandidates(string $symbol): array
    {
        $symbol = strtoupper(trim($symbol));

        if (isset(self::SPECIAL_YAHOO_TICKERS[$symbol])) {
            return self::SPECIAL_YAHOO_TICKERS[$symbol];
        }

        if (str_contains($symbol, ':')) {
            [, $symbol] = explode(':', $symbol, 2);
        }

        $symbol = str_replace(' ', '', $symbol);
        $candidates = [];

        if (str_contains($symbol, '/')) {
            [$base, $quote] = array_pad(explode('/', $symbol, 2), 2, '');
            $base = trim($base);
            $quote = trim($quote);

            if ($base !== '' && $quote !== '') {
                $candidates[] = "{$base}/{$quote}";
                $candidates[] = "{$base}-{$quote}";
                $candidates[] = "{$base}{$quote}";
                $candidates[] = "{$base}{$quote}=X";
                $candidates[] = "{$base}-{$quote}=X";
            }
        } else {
            $candidates[] = $symbol;
            $candidates[] = str_replace('/', '-', $symbol);
        }

        return collect($candidates)
            ->map(fn ($item) => strtoupper(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function isReasonablePrice(string $symbol, float $price): bool
    {
        if ($price <= 0) {
            return false;
        }

        if ($symbol === 'XAU/USD' && $price < 100) {
            return false;
        }

        if ($symbol === 'XAG/USD' && $price < 1) {
            return false;
        }

        return true;
    }
}

