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
        Schema::create('inspeksi_ofa', function (Blueprint $table) {
            $table->id();
             $table->string('project_name')->nullable();
            $table->string('version')->nullable();
            $table->string('divisi_department')->nullable();
            $table->string('type_unit')->nullable();
            $table->string('jobsite')->nullable();
            $table->string('code_number_unit')->nullable();
            $table->string('serial_number_modul')->nullable();
            $table->string('location')->nullable();
            $table->text('item_pemeriksaaan')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->json('tim_pelaksana')->nullable();
            $table->string('diinspeksi_oleh')->nullable();
            $table->string('diperiksa_oleh')->nullable();
            $table->date('tanggal_inspeksi')->nullable();
            $table->enum('status_pemeriksaan', [
    'belum',
    'sudah',
])->default('belum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the mixgrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_ofa');
    }
};
