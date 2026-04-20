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
            $table->decimal('filled_quantity', 20, 8)->default(0)->after('quantity');
            $table->decimal('remaining_quantity', 20, 8)->default(0)->after('filled_quantity');
            $table->string('lifecycle_status', 30)->default('pending')->after('broker_status');
            $table->timestamp('last_synced_at')->nullable()->after('lifecycle_status');
            $table->index('lifecycle_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_orders', function (Blueprint $table) {
            $table->dropIndex(['lifecycle_status']);
            $table->dropColumn([
                'filled_quantity',
                'remaining_quantity',
                'lifecycle_status',
                'last_synced_at',
            ]);
        });
    }
};

