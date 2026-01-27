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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('yoga_class_id')->constrained()->onDelete('cascade');
        
        $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
        
        $table->unique(['user_id', 'yoga_class_id']); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
