<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pigs', function (Blueprint $table) {
            $table->date('birth_date');
            $table->dropColumn('age');
        });
    }

    public function down(): void
    {
        Schema::table('pigs', function (Blueprint $table) {
            $table->integer('age');
            $table->dropColumn('birth_date');
        });
    }
};
