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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->string('fullname');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->string('password');

            $table->string('specialization');
            $table->string('medical_license_number')->unique();
            $table->integer('years_of_experience');
            $table->text('clinic_or_hospital_name');
            $table->string('work_address');
            $table->text('available_working_hours');

            $table->enum('gender', ['male', 'female', 'other']);

            $table->string('image')->default('image.png');
            
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
