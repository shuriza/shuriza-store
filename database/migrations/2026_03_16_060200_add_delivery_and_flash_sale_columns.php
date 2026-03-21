<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add digital delivery fields to order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->text('delivery_data')->nullable()->after('subtotal');
            $table->string('delivery_type')->nullable()->after('delivery_data');
            $table->timestamp('delivered_at')->nullable()->after('delivery_type');
        });

        // Add flash sale fields to products
        Schema::table('products', function (Blueprint $table) {
            $table->integer('flash_sale_price')->nullable()->after('original_price');
            $table->timestamp('flash_sale_start')->nullable()->after('flash_sale_price');
            $table->timestamp('flash_sale_end')->nullable()->after('flash_sale_start');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['delivery_data', 'delivery_type', 'delivered_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['flash_sale_price', 'flash_sale_start', 'flash_sale_end']);
        });
    }
};
