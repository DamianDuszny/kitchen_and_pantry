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
        Schema::table('recipes_products', function (Blueprint $table) {
//            $table->unique(['recipes_id', 'products_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes_products', function (Blueprint $table) {
            $table->dropUnique('recipes_id');
            $table->dropUnique('products_id');
        });
    }
};
