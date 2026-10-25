<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('karyawans');
    }

    public function down(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nrp')->unique();
            $table->string('jabatan');
            $table->string('departemen');
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }
};
