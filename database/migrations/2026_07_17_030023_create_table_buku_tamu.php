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
        Schema::create('buku_tamu', function (Blueprint $table) {
            $table->integer('no')->nullable();
            $table->string('nama', 100)->nullable();
            $table->string('no_telp')->nullable();
            $table->string('nrp')->nullable();
            $table->string('instansi')->nullable();
            $table->string('keperluan', 200)->nullable();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_buku_tamu');
    }
};
