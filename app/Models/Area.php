<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['area_name', 'post_code',];
    public function shippingCharge() :HasMany
    {
        return $this->hasMany(ShippingCharge::class);
    }
}
