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
        Schema::create('shopping_list_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_lists_id')->constrained('shopping_lists');
            $table->foreignId('products_id')->constrained('products');
            $table->foreignId('recipes_id')->nullable()->constrained('recipes');
            $table->integer('amount')->nullable();
            $table->integer('weight')->nullable();
            $table->foreignId('substitute_for')->nullable()->constrained('shopping_list_products');
            $table->boolean('accepted')->default(true)->comment('Used for substitutes');
            $table->integer('satisfied_amount')->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_products');
    }
};
