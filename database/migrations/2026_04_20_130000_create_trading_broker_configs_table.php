<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trading_broker_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30)->default('alpaca');
            $table->boolean('is_enabled')->default(false);
            $table->enum('mode', ['paper', 'live'])->default('paper');
            $table->text('api_key_encrypted')->nullable();
            $table->text('api_secret_encrypted')->nullable();
            $table->string('custom_base_url')->nullable();
            $table->string('data_feed', 10)->default('iex');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_broker_configs');
    }
};

