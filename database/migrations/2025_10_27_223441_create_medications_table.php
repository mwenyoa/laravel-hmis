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
        Schema::create('medications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Changed from 'med_name' to 'name' for consistency
            $table->string('generic_name');
            $table->string('brand_name')->nullable(); // Added brand name
            $table->string('strength')->nullable(); // Added strength (e.g., "500mg")
            $table->string('form')->nullable(); // Added form (tablet, capsule, liquid, etc.)
            $table->string('route')->nullable(); // Added route (oral, topical, IV, etc.)
            $table->text('contraindications')->nullable(); // Made nullable
            $table->text('side_effects')->nullable(); // Added side effects
            $table->text('indications')->nullable(); // Added medical uses
            $table->text('dosage_guidelines')->nullable(); // Added dosage information
            $table->boolean('is_active')->default(true); // Added active status
            $table->boolean('is_controlled')->default(false); // Added controlled substance flag
            $table->timestamps(); // Added timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};