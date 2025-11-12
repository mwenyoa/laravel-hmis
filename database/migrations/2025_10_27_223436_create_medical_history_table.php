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
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('condition_name');
            $table->enum('condition_type', ['acute', 'chronic', 'genetic', 'infectious', 'autoimmune', 'other']);
            $table->date('diagnosis_date');
            $table->date('resolved_date')->nullable(); 
            $table->enum('severity', ['mild', 'moderate', 'severe', 'critical']); // Use enum
            $table->longText('notes')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};