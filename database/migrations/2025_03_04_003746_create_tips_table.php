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
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tip Title
            $table->text('description')->nullable(); // Tip Description
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // علاقة Many-to-One مع الفئات
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('published_date')->nullable(); // Published Date
            $table->text('notes')->nullable(); // Notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips');
    }
};
