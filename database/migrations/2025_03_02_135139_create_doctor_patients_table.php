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
        Schema::create('doctor_patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('fullname');
            $table->string('email');
            $table->string('phoneNumber');
            $table->integer('age');
            $table->string('typeOfAddiction');
            $table->enum('status',['Under Treatment','Partial Recovery','Full Recovery'])->default('Under Treatment');
            $table->string('durationOfAddication');


            $table->timestamps();
            // علاقات الـ foreign keys
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            // عشان تمنع تكرار نفس العلاقة
            $table->unique(['doctor_id', 'patient_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_patients');
    }
};
