<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHouseRentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('house_rents', function (Blueprint $table) {
            $table->id();
            $table->string('district');
            $table->string('police_station');
            $table->string('road');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('square_feet');
            $table->integer('bedrooms');
            $table->json('gallery');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ছবির গ্যালারি সংরক্ষণের জন্য
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('house_rents');
    }
}
