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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('encounter_id')->constrained('clinical_encounters')->onDelete('cascade');
            $table->foreignUuid('medication_id')->constrained('medications')->onDelete('cascade');
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->integer('quantity');
            $table->text('instructions')->nullable(); // Made nullable
            $table->integer('refills_allowed')->default(0);
            $table->boolean('is_active')->default(true); // Changed to boolean
            $table->timestamp('prescribed_date');
            $table->timestamp('expiry_date')->nullable(); // Added expiry date
            $table->timestamps(); // Added timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};