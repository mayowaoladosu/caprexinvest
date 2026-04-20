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
        Schema::table('basic_controls', function (Blueprint $table) {
            $table->boolean('trading_enabled')->default(false)->after('invest_status');
            $table->boolean('trading_dashboard_chart_enabled')->default(true)->after('trading_enabled');
            $table->boolean('trading_homepage_chart_enabled')->default(true)->after('trading_dashboard_chart_enabled');
            $table->boolean('trading_allow_buy')->default(true)->after('trading_homepage_chart_enabled');
            $table->boolean('trading_allow_sell')->default(true)->after('trading_allow_buy');
            $table->string('trading_chart_symbol', 30)->default('NASDAQ:AAPL')->after('trading_allow_sell');
            $table->string('trading_chart_interval', 10)->default('D')->after('trading_chart_symbol');
            $table->string('trading_chart_theme', 10)->default('dark')->after('trading_chart_interval');
            $table->json('trading_symbols')->nullable()->after('trading_chart_theme');
            $table->decimal('trading_min_order_amount', 16, 2)->default(10)->after('trading_symbols');
            $table->decimal('trading_max_order_amount', 16, 2)->default(100000)->after('trading_min_order_amount');
            $table->decimal('trading_fee_percent', 5, 2)->default(0)->after('trading_max_order_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_controls', function (Blueprint $table) {
            $table->dropColumn([
                'trading_enabled',
                'trading_dashboard_chart_enabled',
                'trading_homepage_chart_enabled',
                'trading_allow_buy',
                'trading_allow_sell',
                'trading_chart_symbol',
                'trading_chart_interval',
                'trading_chart_theme',
                'trading_symbols',
                'trading_min_order_amount',
                'trading_max_order_amount',
                'trading_fee_percent',
            ]);
        });
    }
};

