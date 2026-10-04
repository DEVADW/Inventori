<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code_unit')->unique();   // CODE UNIT, ex: C77023
            $table->string('model');                 // MODEL, ex: CAT777
            $table->string('serial_number');         // SERIALNUMBER
            $table->timestamps();

            // index untuk mempercepat pencarian select2
            $table->index('code_unit');
            $table->index('model');
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};