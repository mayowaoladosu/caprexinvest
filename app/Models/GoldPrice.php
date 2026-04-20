<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = ['currency', 'rate', 'recorded_at'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

}
