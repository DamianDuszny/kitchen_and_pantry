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
        Schema::table('shopping_lists', function(Blueprint $table) {
            $table->dropColumn('products_id');
            $table->dropColumn('amount');
            $table->dropColumn('net_weight');
            $table->dropColumn('bought');
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_lists', function(Blueprint $table) {
            $table->integer('amount')->nullable();
            $table->integer('net_weight')->nullable();
            $table->boolean('bought')->nullable();
            $table->foreignId('products_id')->nullable()->constrained('products');
            $table->dropColumn('note');
        });
    }
};
