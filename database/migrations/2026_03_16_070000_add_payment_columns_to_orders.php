<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('manual')->after('status');
            $table->string('payment_token')->nullable()->after('payment_method');
            $table->string('payment_url')->nullable()->after('payment_token');
            $table->string('payment_gateway_id')->nullable()->after('payment_url');
            $table->timestamp('paid_at')->nullable()->after('payment_gateway_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_token', 'payment_url', 'payment_gateway_id', 'paid_at']);
        });
    }
};
