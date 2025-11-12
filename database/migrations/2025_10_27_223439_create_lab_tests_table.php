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
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('test_code')->unique(); // Added unique constraint
            $table->string('test_name');
            $table->text('description')->nullable(); // Made nullable
            $table->text('normal_range')->nullable(); // Made nullable
            $table->string('unit')->nullable(); // Made nullable
            $table->decimal('price', 10, 2)->nullable(); // Added price field
            $table->integer('turnaround_time')->nullable(); // Added turnaround in hours
            $table->boolean('is_active')->default(true); // Added active status
            $table->timestamps(); // Added timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};