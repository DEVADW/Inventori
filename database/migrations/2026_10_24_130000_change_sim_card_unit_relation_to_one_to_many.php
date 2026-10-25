<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sim_cards', function (Blueprint $table) {
            $table->foreignId('unit_operasi_id')->nullable()->after('status_kartu')
                ->constrained('unit_operasi')->nullOnDelete();
        });

        DB::table('unit_operasi')
            ->whereNotNull('sim_card_id')
            ->select('id', 'sim_card_id')
            ->orderBy('id')
            ->each(function ($unit) {
                DB::table('sim_cards')
                    ->where('id', $unit->sim_card_id)
                    ->update(['unit_operasi_id' => $unit->id]);
            });

        Schema::table('unit_operasi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sim_card_id');
        });

        Schema::table('sim_cards', function (Blueprint $table) {
            $table->dropColumn('associate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sim_cards', function (Blueprint $table) {
            $table->string('associate')->nullable()->after('status_kartu');
        });

        Schema::table('unit_operasi', function (Blueprint $table) {
            $table->foreignId('sim_card_id')->nullable()->after('no_aset_tab')
                ->constrained('sim_cards')->nullOnDelete();
        });

        DB::table('sim_cards')
            ->whereNotNull('unit_operasi_id')
            ->select('id', 'unit_operasi_id')
            ->orderBy('id')
            ->each(function ($simCard) {
                DB::table('unit_operasi')
                    ->where('id', $simCard->unit_operasi_id)
                    ->update(['sim_card_id' => $simCard->id]);
            });

        Schema::table('sim_cards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_operasi_id');
        });
    }
};
