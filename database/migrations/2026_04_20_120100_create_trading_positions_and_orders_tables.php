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
        Schema::create('trading_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 30);
            $table->decimal('quantity', 20, 8)->default(0);
            $table->decimal('average_price', 20, 8)->default(0);
            $table->decimal('invested_amount', 20, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'symbol']);
        });

        Schema::create('trading_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_position_id')->nullable()->constrained('trading_positions')->nullOnDelete();
            $table->string('symbol', 30);
            $table->enum('side', ['buy', 'sell']);
            $table->decimal('quantity', 20, 8);
            $table->decimal('unit_price', 20, 8);
            $table->decimal('gross_amount', 20, 2);
            $table->decimal('fee_amount', 20, 2)->default(0);
            $table->decimal('net_amount', 20, 2);
            $table->string('trx_id', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_orders');
        Schema::dropIfExists('trading_positions');
    }
};

