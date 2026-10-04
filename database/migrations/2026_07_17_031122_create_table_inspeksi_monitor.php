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
        Schema::create('inspeksi_monitor', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_aset')->nullable();
            $table->string('merek')->nullable();
            $table->string('type')->nullable();
            $table->string('sn')->nullable();
            $table->string('departemen')->nullable();
            $table->string('lokasi')->nullable();
            $table->date('tanggal_inspeksi')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('tampilan_layer')->nullable();
            $table->string('kabel_power')->nullable();
            $table->string('bracket_dudukan')->nullable();
            $table->string('kebersihan')->nullable();
            $table->string('stop_kontak')->nullable();
            $table->text('tindakan_tampilan_layer')->nullable();
            $table->text('tindakan_kabel_power')->nullable();
            $table->text('tindakan_bracket_dudukan')->nullable();
            $table->text('tindakan_kebersihan')->nullable();
            $table->text('tindakan_stop_kontak')->nullable();
            $table->string('inspektor')->nullable();
            $table->string('jabatan_inspektor')->nullable();
            $table->string('diketahui_oleh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_monitor');
    }
};
