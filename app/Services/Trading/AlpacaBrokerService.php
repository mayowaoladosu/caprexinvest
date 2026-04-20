<?php

namespace App\Services\Trading;

use App\Models\TradingBrokerConfig;
use Illuminate\Support\Facades\Http;

class AlpacaBrokerService
{
    public function __construct(private readonly TradingBrokerConfig $config)
    {
    }

    public function isReady(): bool
    {
        return $this->config->is_enabled && !empty($this->config->api_key) && !empty($this->config->api_secret);
    }

    public function placeMarketOrder(
        string $symbol,
        string $side,
        float $quantity,
        ?float $takeProfitPrice = null,
        ?float $stopLossPrice = null
    ): array {
        $payload = [
            'symbol' => $symbol,
            'qty' => (string) $quantity,
            'side' => $side,
            'type' => 'market',
            'time_in_force' => 'gtc',
        ];

        if ($side === 'buy' && $takeProfitPrice && $stopLossPrice && !$this->isCryptoSymbol($symbol)) {
            $payload['order_class'] = 'bracket';
            $payload['take_profit'] = ['limit_price' => (string) $takeProfitPrice];
            $payload['stop_loss'] = ['stop_price' => (string) $stopLossPrice];
        }

        $response = $this->request()->post('/v2/orders', $payload);
        if (!$response->ok()) {
            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Broker order placement failed',
                'data' => $response->json(),
            ];
        }

        return [
            'success' => true,
            'data' => $response->json(),
        ];
    }

    public function latestPrice(string $symbol): ?float
    {
        $snapshot = $this->latestQuoteSnapshot($symbol);
        return $snapshot['price'] ?? null;
    }

    public function latestQuoteSnapshot(string $symbol): ?array
    {
        $normalized = $this->normalizeSymbolForBroker($symbol);

        if ($this->isCryptoSymbol($normalized)) {
            $encodedSymbol = rawurlencode($normalized);
            $response = $this->request('https://data.alpaca.markets')
                ->get("/v1beta3/crypto/us/latest/quotes?symbols={$encodedSymbol}");
            if (!$response->ok()) {
                return null;
            }
            $ask = data_get($response->json(), "quotes.{$normalized}.ap");
            $bid = data_get($response->json(), "quotes.{$normalized}.bp");
            $price = $ask ?: $bid;
            if ($price === null) {
                return null;
            }

            return [
                'symbol' => $normalized,
                'price' => (float) $price,
                'bid' => $bid !== null ? (float) $bid : null,
                'ask' => $ask !== null ? (float) $ask : null,
                'source' => 'alpaca',
            ];
        }

        $response = $this->request('https://data.alpaca.markets')
            ->get('/v2/stocks/' . rawurlencode($normalized) . '/quotes/latest', [
                'feed' => $this->config->data_feed ?: 'iex',
            ]);
        if (!$response->ok()) {
            return null;
        }

        $ask = data_get($response->json(), 'quote.ap');
        $bid = data_get($response->json(), 'quote.bp');
        $price = $ask ?: $bid;
        if ($price === null) {
            return null;
        }

        return [
            'symbol' => $normalized,
            'price' => (float) $price,
            'bid' => $bid !== null ? (float) $bid : null,
            'ask' => $ask !== null ? (float) $ask : null,
            'source' => 'alpaca',
        ];
    }

    public function fetchOrderById(string $orderId): ?array
    {
        $response = $this->request()->get('/v2/orders/' . rawurlencode($orderId));
        if (!$response->ok()) {
            return null;
        }

        return $response->json();
    }

    public function normalizeSymbolForBroker(string $symbol): string
    {
        $symbol = strtoupper(trim($symbol));
        if (str_contains($symbol, ':')) {
            [, $symbol] = explode(':', $symbol, 2);
        }

        $compact = str_replace(['-', '_', '.'], '', $symbol);
        if (in_array($compact, ['BTCUSD', 'ETHUSD', 'LTCUSD', 'SOLUSD'], true)) {
            return substr($compact, 0, 3) . '/USD';
        }

        return str_replace(' ', '', $symbol);
    }

    private function isCryptoSymbol(string $symbol): bool
    {
        return str_contains($symbol, '/');
    }

    private function request(?string $overrideBaseUrl = null)
    {
        $baseUrl = $overrideBaseUrl ?: $this->resolveBaseUrl();
        return Http::baseUrl($baseUrl)
            ->timeout(15)
            ->withHeaders([
                'APCA-API-KEY-ID' => $this->config->api_key ?? '',
                'APCA-API-SECRET-KEY' => $this->config->api_secret ?? '',
            ]);
    }

    private function resolveBaseUrl(): string
    {
        if (!empty($this->config->custom_base_url)) {
            return rtrim($this->config->custom_base_url, '/');
        }

        return $this->config->mode === 'live'
            ? 'https://api.alpaca.markets'
            : 'https://paper-api.alpaca.markets';
    }
}

