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
        Schema::create('unit_operasi', function (Blueprint $table) {
            $table->id();
            $table->string('code_unit')->unique();
            $table->string('type_unit');
            $table->text('remark')->nullable();
            $table->boolean('ofa')->default(false);
            $table->boolean('icc')->default(false);
            $table->boolean('ss6')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_operasi');
    }
};
