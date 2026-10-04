<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $inspectionTables = [
        'inspeksi_monitor',
        'inspeksi_proyektors',
        'inspeksi_stavolts',
        'inspeksi_ups',
        'inspeksi_ss6',
        'inspeksi_ofa',
        'inspeksi_icc',
    ];

    public function up(): void
    {
        foreach ($this->inspectionTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('photo_path')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->inspectionTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('photo_path');
            });
        }
    }
};