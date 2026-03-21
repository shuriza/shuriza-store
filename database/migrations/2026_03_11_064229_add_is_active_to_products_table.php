<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // is_active already included in create_products_table migration
    }

    public function down(): void
    {
        // nothing to reverse
    }
};
