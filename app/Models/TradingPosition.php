<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'quantity',
        'average_price',
        'invested_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(TradingOrder::class);
    }
}

