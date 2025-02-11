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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto-increment ID
            $table->text('message')->nullable(); // Mensaje opcional
            $table->unsignedBigInteger('pig_id'); // Relación con pigs
            $table->date('reminder_date'); // Fecha de recordatorio
            $table->unsignedBigInteger('treatment_id')->nullable(); // Relación con tratamientos
            $table->unsignedBigInteger('farm_id'); // Relación con usuarios
            $table->timestamps();

            // Claves foráneas
            $table->foreign('pig_id')->references('id')->on('pigs')->onDelete('cascade');
            $table->foreign('treatment_id')->references('id')->on('pig_treatments')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
