<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('delivery_status', 20)->default('pending')->after('delivered_at');
            $table->unsignedInteger('delivery_attempts')->default(0)->after('delivery_status');
            $table->text('last_delivery_error')->nullable()->after('delivery_attempts');
            $table->json('delivery_meta')->nullable()->after('last_delivery_error');

            $table->index(['delivery_status', 'delivered_at']);
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['delivery_status', 'delivered_at']);
            $table->dropColumn([
                'delivery_status',
                'delivery_attempts',
                'last_delivery_error',
                'delivery_meta',
            ]);
        });
    }
};
