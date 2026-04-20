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
        Schema::table('trading_orders', function (Blueprint $table) {
            $table->string('broker_name', 30)->nullable()->after('trx_id');
            $table->string('broker_order_id')->nullable()->after('broker_name');
            $table->string('broker_status', 30)->nullable()->after('broker_order_id');
            $table->decimal('take_profit_price', 20, 8)->nullable()->after('broker_status');
            $table->decimal('stop_loss_price', 20, 8)->nullable()->after('take_profit_price');
            $table->boolean('is_live_order')->default(false)->after('stop_loss_price');
            $table->index('broker_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_orders', function (Blueprint $table) {
            $table->dropIndex(['broker_order_id']);
            $table->dropColumn([
                'broker_name',
                'broker_order_id',
                'broker_status',
                'take_profit_price',
                'stop_loss_price',
                'is_live_order',
            ]);
        });
    }
};

