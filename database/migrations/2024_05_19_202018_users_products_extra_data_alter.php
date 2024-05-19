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
            $table->dropColumn('name');
            $table->addColumn('date', 'expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_products_extra_data', function(Blueprint $table) {
            $table->addColumn('string', 'name');
            $table->dropColumn('expiration_date');
        });
    }
};
