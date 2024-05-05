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
        Schema::create('recipes_substitute_products', function (Blueprint $table) {
            //@todo SQLSTATE[42830]: Invalid foreign key: 7 BŁĄD:  brak ograniczenia unikalnego pasującego do danych kluczy dla tabeli referencyjnej "recipes_products" (Connection: pgsql, SQL: alter table "recipes_substitute_products" add constraint
            //"recipes_substitute_products_recipes_id_foreign" foreign key ("recipes_id") references "recipes_products" ("recipes_id"))

//            $table->foreignId('recipes_id')->references('recipes_id')->on('recipes_products');
//            $table->foreignId('substitute_for')->references('products_id')->on('recipes_products');
            $table->integer('recipes_id');
            $table->integer('substitute_for')->comment('recipes_products.products_id');
            $table->foreignId('products_id')->references('id')->on('products');
            $table->integer('amount')->nullable();
            $table->integer('weight')->nullable();
            $table->string('comment')->nullable();
            $table->integer('how_well_fits')->comment('should contain value between 1 and 100. 100 means fits the best');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes_substitute_products');
    }
};
