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
            $table->decimal('realized_pnl', 20, 2)->default(0)->after('net_amount');
            $table->decimal('realized_pnl_percent', 10, 2)->nullable()->after('realized_pnl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_orders', function (Blueprint $table) {
            $table->dropColumn([
                'realized_pnl',
                'realized_pnl_percent',
            ]);
        });
    }
};

