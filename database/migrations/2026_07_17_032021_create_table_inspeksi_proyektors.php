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
        Schema::create('inspeksi_proyektors', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_aset')->nullable();
            $table->string('departemen')->nullable();
            $table->string('merek')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('type')->nullable();
            $table->date('tanggal_inspeksi')->nullable();
            $table->string('sn')->nullable();
            $table->string('kondisi_casing')->nullable();
            $table->text('tindakan_kondisi_casing')->nullable();
            $table->string('kebersihan')->nullable();
            $table->text('tindakan_kebersihan')->nullable();
            $table->string('kabel_adaptor')->nullable();
            $table->text('tindakan_kabel_adaptor')->nullable();
            $table->string('lensa_proyektor')->nullable();
            $table->text('tindakan_lensa_proyektor')->nullable();
            $table->string('indikator_lampu')->nullable();
            $table->text('tindakan_indikator_lampu')->nullable();
            $table->string('fokus_zoom')->nullable();
            $table->text('tindakan_fokus_zoom')->nullable();
            $table->string('kecerahan_kontras')->nullable();
            $table->text('tindakan_kecerahan_kontras')->nullable();
            $table->string('koneksi_input_hdmi')->nullable();
            $table->string('koneksi_input_vga')->nullable();
            $table->string('koneksi_input_usb')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('inspektor')->nullable();
            $table->string('jabatan_inspektor')->nullable();
            $table->string('diketahui_oleh')->nullable()->default('Group Leader ICT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_proyektors');
    }
};
