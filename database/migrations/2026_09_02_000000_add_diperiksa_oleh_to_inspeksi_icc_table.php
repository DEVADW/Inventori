<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspeksi_icc', function (Blueprint $table) {
            $table->string('diperiksa_oleh')->nullable()->after('diketahui_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('inspeksi_icc', function (Blueprint $table) {
            $table->dropColumn('diperiksa_oleh');
        });
    }
};
