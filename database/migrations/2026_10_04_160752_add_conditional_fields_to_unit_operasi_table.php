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
        Schema::table('unit_operasi', function (Blueprint $table) {
            $table->string('serial_number_module')->nullable()->after('ofa');
            $table->string('serial_number_tab')->nullable()->after('ss6');
            $table->string('no_aset_tab')->nullable()->after('serial_number_tab');
            $table->foreignId('sim_card_id')->nullable()->after('no_aset_tab')
                ->constrained('sim_cards')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_operasi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sim_card_id');
            $table->dropColumn(['serial_number_module', 'serial_number_tab', 'no_aset_tab']);
        });
    }
};
