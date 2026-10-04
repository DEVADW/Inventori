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
        Schema::create('inspeksi_icc', function (Blueprint $table) {
            $table->id();
             // Data unit
            $table->string('no_lambung_unit');
            $table->date('tanggal_inspeksi');
            $table->string('lokasi_inspeksi');

            // Item pemeriksaan
            $table->json('item_pemeriksaan');

            // Catatan umum
            $table->text('note')->nullable();

            // Data pemeriksa
            $table->string('inspektor')->nullable();
            $table->string('diketahui_oleh')->nullable();
            $table->string('status_approval')->default('menunggu');
            $table->string('approved_by')->nullable();
            $table->string('qr_code_persetujuan')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_icc');
    }
};
