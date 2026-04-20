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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('profit_balance',10,2)->after('balance');
            $table->decimal('total_invest',10,2)->after('profit_balance');
            $table->decimal('total_profit',10,2)->after('total_invest');
            $table->decimal('total_deposit',10,2)->after('total_profit');
            $table->decimal('total_commission',10,2)->after('total_deposit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
