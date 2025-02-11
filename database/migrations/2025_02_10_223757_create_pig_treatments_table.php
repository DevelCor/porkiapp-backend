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
        Schema::create('pig_treatments', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto-increment ID
            $table->unsignedBigInteger('pig_id'); // Relación con pigs
            $table->string('name');
            $table->text('description');
            $table->string('dosage');
            $table->timestamps();

            // Clave foránea para la relación con pigs
            $table->foreign('pig_id')->references('id')->on('pigs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pig_treatments');
    }
};
