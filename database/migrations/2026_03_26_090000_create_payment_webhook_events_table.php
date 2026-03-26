<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30);
            $table->string('event_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('endpoint')->default('payment.notification');
            $table->longText('payload');
            $table->longText('headers')->nullable();
            $table->string('payload_hash', 64);
            $table->string('status', 20)->default('received');
            $table->unsignedSmallInteger('attempts')->default(1);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'event_id']);
            $table->index(['provider', 'status']);
            $table->index('order_number');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};
