<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pig_treatments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'administered'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('pig_treatments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
