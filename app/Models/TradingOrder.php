<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trading_position_id',
        'symbol',
        'side',
        'quantity',
        'filled_quantity',
        'remaining_quantity',
        'unit_price',
        'gross_amount',
        'fee_amount',
        'net_amount',
        'realized_pnl',
        'realized_pnl_percent',
        'trx_id',
        'broker_name',
        'broker_order_id',
        'broker_status',
        'lifecycle_status',
        'last_synced_at',
        'take_profit_price',
        'stop_loss_price',
        'is_live_order',
        'notes',
    ];

    protected $casts = [
        'is_live_order' => 'boolean',
        'last_synced_at' => 'datetime',
        'realized_pnl' => 'float',
        'realized_pnl_percent' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(TradingPosition::class, 'trading_position_id');
    }
}

