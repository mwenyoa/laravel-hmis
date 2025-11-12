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
        Schema::create('lab_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lab_order_id')->constrained('lab_orders')->onDelete('cascade');
            $table->foreignUuid('lab_test_id')->constrained('lab_tests')->onDelete('cascade');
            $table->string('result_value')->nullable(); // Made nullable (not all results are strings)
            $table->decimal('result_numeric', 10, 4)->nullable(); // Added precision, made nullable
            $table->string('result_unit')->nullable(); // Made nullable
            $table->text('notes')->nullable(); // Made nullable
            $table->timestamp('completed_at');
            $table->enum('flag', ['normal', 'low', 'high', 'critical', 'abnormal'])->default('normal'); // Changed to enum
            $table->timestamps(); // Added timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};