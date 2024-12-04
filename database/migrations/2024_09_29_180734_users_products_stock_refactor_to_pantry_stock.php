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
        Schema::rename('users_pantry_stock', 'pantry_stock');
        Schema::table('pantry_stock', function (Blueprint $table) {
            $table->dropColumn('users_id');
            $table->foreignId('pantry_id')->constrained('pantry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('pantry_stock', 'users_pantry_stock');
        Schema::table('users_pantry_stock', function (Blueprint $table) {
            $table->dropColumn('pantry_id');
            $table->foreignId('users_id')->constrained('users');
        });
    }
};
