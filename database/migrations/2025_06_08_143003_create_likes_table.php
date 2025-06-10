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
        Schema::create('likes', function (Blueprint $table) {
            $table->id(); // This creates a BIGINT UNSIGNED auto-incrementing primary key

            // Foreign key to the 'users' table
            $table->foreignId('user_id')
                  ->constrained('users') // References the 'users' table
                  ->onDelete('cascade'); // If a user is deleted, their likes are also deleted

            // Foreign key to the 'houserents' table
            $table->foreignId('house_rent_id')
                  ->constrained('house_rents') // References the 'houserents' table
                  ->onDelete('cascade'); // If a house rent is deleted, its likes are also deleted

            // Ensures a user can only like a house rent once
            $table->unique(['user_id', 'house_rent_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};