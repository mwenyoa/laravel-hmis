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
        Schema::create('encounter_diagnoses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('encounter_id')->constrained('clinical_encounters')->onDelete('cascade');
            $table->foreignUuid('diagnosis_id')->constrained('diagnoses')->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->enum('type', ['primary', 'secondary', 'differential', 'rule_out', 'comorbidity'])->default('primary');
            $table->text('notes')->nullable();
            $table->enum('certainty', ['confirmed', 'probable', 'suspected', 'ruled_out'])->default('confirmed');
            $table->timestamps();

            // Prevent duplicate diagnoses for the same encounter
            $table->unique(['encounter_id', 'diagnosis_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_diagnoses');
    }
};