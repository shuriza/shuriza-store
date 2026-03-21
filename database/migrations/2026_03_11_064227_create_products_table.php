<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("category_id")
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->string("slug")->unique();
            $table->text("description")->nullable();
            $table->text("short_description")->nullable();
            $table->unsignedBigInteger("price");
            $table->unsignedBigInteger("original_price")->nullable();
            $table->integer("stock")->default(0);
            $table->string("image")->nullable();
            $table->string("badge")->nullable(); // hot, sale, new
            $table->boolean("is_active")->default(true);
            $table->boolean("is_popular")->default(false);
            $table->integer("sort_order")->default(0);
            $table->integer("views")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("products");
    }
};
