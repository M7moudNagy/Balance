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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('address');
            $table->string('governorate');
            $table->string('image')->default('image.png');

            $table->string('medical_specialty');
            $table->integer('years_of_experience');
            $table->string('type_of_practice');
            $table->string('facility_name')->nullable();
            $table->string('facility_address')->nullable();
            $table->string('facility_governorate')->nullable();

            $table->string('medical_license_number')->unique();
            $table->text('medical_license');
            $table->text('graduation_certificate');
            $table->text('other_certifications')->nullable();

            $table->text('national_id_or_passport');

            $table->text('motivation');
            $table->text('balance_help');

            $table->boolean('licensed_provider');
            $table->boolean('agree_terms');
            $table->decimal('rating', 2, 1)->default(0);

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
