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
        Schema::table('users_products_extra_data', function(Blueprint $table) {
            $table->integer('net_weight')->nullable();
            $table->renameColumn('weight', 'unit_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_products_extra_data', function(Blueprint $table) {
            $table->dropColumn('net_weight');
            $table->renameColumn('unit_weight', 'weight');
        });
    }
};
