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
        Schema::create('inventori_tv', function (Blueprint $table) {
            $table->id();
            $table->string('no_aset')->unique();
            $table->string('merek');
            $table->string('tipe');
            $table->string('serial_number');
            $table->string('department');
            $table->string('lokasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventori_tv');
    }
};
