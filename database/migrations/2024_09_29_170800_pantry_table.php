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
        Schema::create('pantry', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('pantry_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
        });

        Schema::create('pantry_users_access', function (Blueprint $table) {
            $table->foreignId('pantry_id')->constrained('pantry');
            $table->foreignId('users_id')->constrained('users');
            $table->foreignId('role_id')->constrained('pantry_roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pantry_users_access');
        Schema::dropIfExists('pantry_roles');
        Schema::dropIfExists('pantry');
    }
};
