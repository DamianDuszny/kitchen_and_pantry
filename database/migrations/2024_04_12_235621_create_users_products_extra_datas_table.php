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
        Schema::create('users_products_extra_datas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id');
            $table->foreignId('products_id');
            $table->integer('weight');
            $table->string('name');
            $table->integer('amount');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_products_extra_datas');
    }
};
