<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TradingBrokerConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'is_enabled',
        'mode',
        'api_key_encrypted',
        'api_secret_encrypted',
        'custom_base_url',
        'data_feed',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiKeyAttribute(): ?string
    {
        if (empty($this->api_key_encrypted)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->api_key_encrypted);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function setApiSecretAttribute(?string $value): void
    {
        $this->attributes['api_secret_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiSecretAttribute(): ?string
    {
        if (empty($this->api_secret_encrypted)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->api_secret_encrypted);
        } catch (\Throwable $exception) {
            return null;
        }
    }
}

