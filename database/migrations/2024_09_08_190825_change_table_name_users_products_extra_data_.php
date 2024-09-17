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
        Schema::rename('users_products_extra_data', 'users_products_stock');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('users_products_stock', 'users_products_extra_data');
    }
};
