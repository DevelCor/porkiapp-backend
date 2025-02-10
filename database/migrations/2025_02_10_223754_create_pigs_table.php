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
        Schema::create('pigs', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->integer('age');
            $table->float('weight');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('birth_code');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('farm_id');
            $table->timestamps();

            // Clave foránea para la relación recursiva
            $table->foreign('parent_id')->references('id')->on('pigs')->onDelete('set null');
            // Clave foránea para usuario
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Clave foránea para la granja
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pigs');
    }
};
