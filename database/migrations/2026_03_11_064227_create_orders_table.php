<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("orders", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string("order_number")->unique();
            $table->string("name");
            $table->string("phone");
            $table->string("email")->nullable();
            $table->decimal("total", 12, 2)->default(0);
            $table
                ->enum("status", [
                    "pending",
                    "processing",
                    "completed",
                    "cancelled",
                ])
                ->default("pending");
            $table->text("notes")->nullable();
            $table->timestamp("whatsapp_sent_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("orders");
    }
};
