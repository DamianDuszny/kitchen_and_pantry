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
        Schema::table('users_products_descriptions', function (Blueprint $table) {
            $table->dropColumn('products_id');
            $table->foreignId('users_products_stock_id')->constrained('users_products_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_products_descriptions', function (Blueprint $table) {
            $table->foreignId('products_id')->constrained('products');
            $table->dropColumn('users_products_stock_id');
        });
    }
};
