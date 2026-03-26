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
        Schema::table('coupons', function (Blueprint $table) {
            // Per-user usage limit (null = unlimited per user)
            $table->unsignedInteger('usage_per_user')->nullable()->after('usage_limit');
            // Restrict coupon to first order only
            $table->boolean('first_order_only')->default(false)->after('usage_per_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['usage_per_user', 'first_order_only']);
        });
    }
};
