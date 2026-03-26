<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('usage_limit_per_user')->nullable()->after('usage_limit');
            $table->unsignedInteger('min_total_items')->default(1)->after('min_order');
            $table->text('allowed_category_ids')->nullable()->after('min_total_items');
            $table->string('campaign_name', 120)->nullable()->after('name');
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('discount_amount')->default(0);
            $table->timestamp('used_at');
            $table->timestamps();

            $table->index(['coupon_id', 'user_id']);
            $table->index(['coupon_id', 'used_at']);
            $table->unique(['coupon_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'usage_limit_per_user',
                'min_total_items',
                'allowed_category_ids',
                'campaign_name',
            ]);
        });
    }
};
