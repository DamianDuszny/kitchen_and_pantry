<?php

use App\Models\user;
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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('name');
            $table->integer('kcal')->nullable();
            $table->integer('portion_for_how_many')->nullable();
            $table->integer('how_long_it_takes');
            $table->integer('complexity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
