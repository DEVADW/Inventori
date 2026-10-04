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
        Schema::table('inspeksi_ofa', function (Blueprint $table) {
            $table->integer('inspection_month')->nullable();
            $table->integer('inspection_year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspeksi_ofa', function (Blueprint $table) {
            $table->dropColumn(['inspection_month', 'inspection_year']);
        });
    }
};
