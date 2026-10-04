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
        Schema::create('inspeksi_ss6', function (Blueprint $table) {
            $table->id();

            // Data Unit
            $table->string('no_asset');
            $table->string('no_lambung')->nullable();
            $table->date('tanggal_inspeksi');
            $table->string('serial_number')->nullable();

            // Hasil Inspeksi
            $table->json('kondisi_monitor')->nullable();
            $table->json('kondisi_bracket')->nullable();
            $table->json('kondisi_car_charger')->nullable();
            $table->json('kondisi_kabel_power')->nullable();
            $table->json('kondisi_app_lock')->nullable();
            $table->json('software_ppa_teams')->nullable();
            $table->json('kondisi_baterai')->nullable();

            // Output Power Charger
            $table->string('output_powercharge')->nullable();

            // Keterangan
            $table->text('keterangan')->nullable();

            // Pemeriksaan
            $table->string('diinspeksi_oleh')->nullable();
            $table->string('diperiksa_oleh')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_ss6');
    }
};
