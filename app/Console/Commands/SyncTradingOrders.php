<?php

namespace App\Console\Commands;

use App\Models\TradingBrokerConfig;
use App\Models\TradingOrder;
use App\Services\Trading\AlpacaBrokerService;
use Illuminate\Console\Command;

class SyncTradingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading:sync-orders {--limit=200 : Maximum live orders to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync live broker order statuses for lifecycle and partial fill updates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $brokerConfig = TradingBrokerConfig::first();
        if (!$brokerConfig) {
            $this->warn('Broker config not found.');
            return self::SUCCESS;
        }

        $brokerService = new AlpacaBrokerService($brokerConfig);
        if (!$brokerService->isReady()) {
            $this->warn('Broker integration is not ready.');
            return self::SUCCESS;
        }

        $limit = max(1, (int) $this->option('limit'));
        $orders = TradingOrder::query()
            ->where('is_live_order', true)
            ->whereNotNull('broker_order_id')
            ->latest()
            ->limit($limit)
            ->get();

        $synced = 0;
        foreach ($orders as $order) {
            $remote = $brokerService->fetchOrderById((string) $order->broker_order_id);
            if (!$remote) {
                continue;
            }

            $filledQty = (float) data_get($remote, 'filled_qty', 0);
            $remainingQty = max(0, (float) $order->quantity - $filledQty);
            $status = (string) data_get($remote, 'status', $order->broker_status);

            $order->broker_status = $status;
            $order->filled_quantity = $filledQty;
            $order->remaining_quantity = $remainingQty;
            $order->lifecycle_status = $this->mapLifecycleStatus($status, $filledQty, (float) $order->quantity);
            $order->last_synced_at = now();
            $order->save();
            $synced++;
        }

        $this->info("Synced {$synced} trading orders.");
        return self::SUCCESS;
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
}

