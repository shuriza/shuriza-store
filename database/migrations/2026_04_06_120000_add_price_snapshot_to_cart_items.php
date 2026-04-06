<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom price_snapshot untuk menyimpan harga saat produk ditambahkan ke cart.
     * Ini mencegah harga cart berubah jika admin mengupdate harga produk.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedInteger('price_snapshot')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('price_snapshot');
        });
    }
};
