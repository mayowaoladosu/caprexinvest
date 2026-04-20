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
        Schema::table('products', function (Blueprint $table) {
            $table->string('breadcrumb_image')->nullable()->after('meta_image_driver');
           $table->string('breadcrumb_image_driver')->nullable()->after('breadcrumb_image');
           $table->boolean('breadcrumb_image_status')->default(true)->after('breadcrumb_image_driver');
           $table->text('og_description')->nullable()->after('meta_description');
           $table->text('meta_robots')->nullable()->after('og_description');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('breadcrumb_image');
            $table->dropColumn('breadcrumb_image_driver');
            $table->dropColumn('breadcrumb_image_status');
            $table->dropColumn('og_description');
            $table->dropColumn('meta_robots');
        });
    }
};
