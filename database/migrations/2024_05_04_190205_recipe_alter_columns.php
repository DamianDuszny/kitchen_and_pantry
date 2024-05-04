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
        Schema::table('recipes', function(Blueprint $table) {
            $table->renameColumn('how_long_it_takes', 'preparation_time');
            $table->integer('complexity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function(Blueprint $table) {
            $table->renameColumn('preparation_time', 'how_long_it_takes');
            $table->integer('complexity')->change();
        });
    }
};
