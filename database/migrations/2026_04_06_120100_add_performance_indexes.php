<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah index pada kolom yang sering di-query untuk meningkatkan performa.
     */
    public function up(): void
    {
        // Products indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('slug');
            $table->index('is_active');
            $table->index('is_popular');
            $table->index('category_id');
            $table->index(['is_active', 'stock'], 'idx_products_visible');
            $table->index(['flash_sale_start', 'flash_sale_end'], 'idx_products_flash_sale');
        });

        // Orders indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('order_number');
            $table->index('user_id');
            $table->index(['status', 'created_at'], 'idx_orders_status_date');
        });

        // Reviews indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('is_approved');
            $table->index(['product_id', 'is_approved'], 'idx_reviews_product_approved');
        });

        // Categories indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');
        });

        // Wishlists indexes
        Schema::table('wishlists', function (Blueprint $table) {
            $table->index(['user_id', 'product_id']);
        });

        // Articles indexes
        Schema::table('articles', function (Blueprint $table) {
            $table->index('slug');
            $table->index('is_published');
        });

        // Coupons indexes
        Schema::table('coupons', function (Blueprint $table) {
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_popular']);
            $table->dropIndex(['category_id']);
            $table->dropIndex('idx_products_visible');
            $table->dropIndex('idx_products_flash_sale');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['order_number']);
            $table->dropIndex(['user_id']);
            $table->dropIndex('idx_orders_status_date');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
            $table->dropIndex('idx_reviews_product_approved');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'product_id']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_published']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['is_active']);
        });
    }
};
