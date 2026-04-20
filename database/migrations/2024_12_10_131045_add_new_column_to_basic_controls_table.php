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
            $table->string('dark_logo',255)->after('logo')->nullable();
            $table->string('dark_logo_driver',20)->after('dark_logo')->nullable();
            $table->string('home_style',10)->nullable()->after('theme');
            $table->string('website_wallet_logo',150)->nullable()->after('admin_dark_mode_logo_driver');
            $table->string('website_wallet_logo_driver',20)->nullable()->after('website_wallet_logo');
            $table->string('cash_on_delivery_logo',150)->nullable()->after('website_wallet_logo_driver');
            $table->string('cash_on_delivery_logo_driver',20)->nullable()->after('cash_on_delivery_logo');
            $table->string('cookie_title',60)->nullable()->after('date_time_format');
            $table->string('cookie_button_name',25)->nullable()->after('cookie_title');
            $table->string('cookie_button_url')->nullable()->after('cookie_button_name');
            $table->boolean('cookie_status')->default(true)->after('cookie_button_url');
            $table->string('cookie_short_text')->nullable()->after('cookie_status');
            $table->string('cookie_image',100)->nullable()->after('cookie_short_text');
            $table->string('cookie_driver',50)->nullable()->after('cookie_image');
            $table->boolean('deposit_commission')->default(false)->after('cookie_driver');
            $table->boolean('investment_commission')->default(false)->after('deposit_commission');
            $table->boolean('profit_commission')->default(false)->after('investment_commission');
            $table->boolean('registration_bonus')->default(true)->after('profit_commission');
            $table->decimal('registration_bonus_amount',10,2)->default(0)->after('registration_bonus');
            $table->decimal('referral_user_bonus_amount',10,2)->default(0)->after('registration_bonus_amount');
            $table->decimal('vat',10,2)->default(0)->after('referral_user_bonus_amount');
            $table->decimal('working_cost',10,2)->default(0)->after('vat');
            $table->string('instructions')->nullable()->after('working_cost');
            $table->boolean('ecommerce')->default(true)->after('instructions');
            $table->decimal('terminate_charge',10,2)->default(0)->after('ecommerce');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_controls', function (Blueprint $table) {
            $table->dropColumn('dark_logo');
            $table->dropColumn('dark_logo_driver');
            $table->dropColumn('home_style');
            $table->dropColumn('website_wallet_logo');
            $table->dropColumn('website_wallet_logo_driver');
            $table->dropColumn('cash_on_delivery_logo');
            $table->dropColumn('cash_on_delivery_logo_driver');
            $table->dropColumn('cookie_title');
            $table->dropColumn('cookie_button_name');
            $table->dropColumn('cookie_button_url');
            $table->dropColumn('cookie_status');
            $table->dropColumn('cookie_short_text');
            $table->dropColumn('cookie_image');
            $table->dropColumn('cookie_driver');
            $table->dropColumn('deposit_commission');
            $table->dropColumn('investment_commission');
            $table->dropColumn('profit_commission');
            $table->dropColumn('registration_bonus');
            $table->dropColumn('registration_bonus_amount');
            $table->dropColumn('referral_user_bonus_amount');
            $table->dropColumn('vat');
            $table->dropColumn('working_cost');
            $table->dropColumn('instructions');
            $table->dropColumn('ecommerce');
            $table->dropColumn('terminate_charge');
        });
    }
};
