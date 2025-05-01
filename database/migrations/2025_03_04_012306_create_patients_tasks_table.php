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
        Schema::create('patients_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->enum('status', ['Pending', 'In Progress', 'Completed','Overdue'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients_tasks');
    }
};
