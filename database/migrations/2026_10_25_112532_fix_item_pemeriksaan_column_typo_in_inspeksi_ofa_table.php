<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `inspeksi_ofa` CHANGE `item_pemeriksaaan` `item_pemeriksaan` TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `inspeksi_ofa` CHANGE `item_pemeriksaan` `item_pemeriksaaan` TEXT NULL');
    }
};
