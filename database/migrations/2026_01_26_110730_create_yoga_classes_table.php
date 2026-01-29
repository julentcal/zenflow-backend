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
    Schema::create('yoga_classes', function (Blueprint $table) {
        $table->id();
        $table->string('name'); 
        $table->text('description')->nullable();
        $table->dateTime('start_time'); 
        $table->integer('capacity')->default(15); 
        $table->integer('credit_cost')->default(1);
        $table->string('instructor_name');
        $table->integer('duration_minutes')->default(60);
        $table->timestamps();
        
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yoga_classes');
    }
};
