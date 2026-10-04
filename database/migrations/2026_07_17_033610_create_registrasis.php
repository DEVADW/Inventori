<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrasis', function (Blueprint $table) {
            $table->id();
            $table->string('perusahaan');
            $table->string('nomor_lambung');
            $table->string('jenis_kendaraan');
            $table->string('nomor_polisi')->nullable();
            $table->string('id_ptt')->unique();
            $table->string('merek_radio');
            $table->string('jenis_radio', 200)->nullable();
            $table->string('serial_number');
            $table->date('tanggal_permintaan')->default(DB::raw('CURRENT_DATE'));
            $table->json('channels')->nullable();
            $table->timestamps();
            $table->string('range_power')->nullable();
            $table->string('range_frekuensi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrasis');
    }
};
