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
        Schema::create('recipes_products', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\products::class);
            $table->foreignIdFor(\App\Models\recipes::class);
            $table->smallInteger('how_well_matches');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes_products');
    }
};
