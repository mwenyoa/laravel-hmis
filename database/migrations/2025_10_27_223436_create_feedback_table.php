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
        Schema::disableForeignKeyConstraints();

        Schema::create('feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('message');
            $table->uuid('patient_id');
            $table->foreign('patient_id')->references('id')->on('Patient');
            $table->uuid('doctor_id');
            $table->foreign('doctor_id')->references('id')->on('Doctor');
            $table->bigInteger('appointment_id');
            $table->foreign('appointment_id')->references('id')->on('Appointment');
             $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
