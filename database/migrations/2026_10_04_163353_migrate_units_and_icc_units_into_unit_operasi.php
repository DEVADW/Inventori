<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        foreach (DB::table('units')->orderBy('id')->get() as $unit) {
            $existing = DB::table('unit_operasi')->where('code_unit', $unit->code_unit)->first();

            if ($existing) {
                DB::table('unit_operasi')->where('id', $existing->id)->update([
                    'ofa' => true,
                    'type_unit' => $existing->type_unit ?: ($unit->model ?: 'N/A'),
                    'serial_number_module' => $existing->serial_number_module ?: $unit->serial_number,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('unit_operasi')->insert([
                    'code_unit' => $unit->code_unit,
                    'type_unit' => $unit->model ?: 'N/A',
                    'serial_number_module' => $unit->serial_number,
                    'ofa' => true,
                    'icc' => false,
                    'ss6' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach (DB::table('icc_units')->orderBy('id')->get() as $iccUnit) {
            $existing = DB::table('unit_operasi')->where('code_unit', $iccUnit->device_name)->first();
            $remarkNote = trim('SIM: '.($iccUnit->sim ?: '-').' | IMEI: '.($iccUnit->imei ?: '-'));

            if ($existing) {
                DB::table('unit_operasi')->where('id', $existing->id)->update([
                    'icc' => true,
                    'type_unit' => $existing->type_unit ?: ($iccUnit->fleet ?: 'N/A'),
                    'remark' => $existing->remark ? $existing->remark."\n".$remarkNote : $remarkNote,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('unit_operasi')->insert([
                    'code_unit' => $iccUnit->device_name,
                    'type_unit' => $iccUnit->fleet ?: 'N/A',
                    'remark' => $remarkNote,
                    'ofa' => false,
                    'icc' => true,
                    'ss6' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $unitCodes = DB::table('units')->pluck('code_unit');
        DB::table('unit_operasi')->whereIn('code_unit', $unitCodes)->update(['ofa' => false]);

        $iccCodes = DB::table('icc_units')->pluck('device_name');
        DB::table('unit_operasi')->whereIn('code_unit', $iccCodes)->update(['icc' => false]);
    }
};
