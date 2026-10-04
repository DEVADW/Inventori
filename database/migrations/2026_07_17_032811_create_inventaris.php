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
        Schema::create('inventaris', function (Blueprint $table) {
           
            $table->id();
            $table->string('nama');
            $table->string('nrp');
            $table->string('nama_perangkat');
            $table->string('no_asset')->nullable();
            $table->enum('status_peminjaman', ['Belum Dikembalikan', 'Dikembalikan'])
                  ->default('Belum Dikembalikan');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
