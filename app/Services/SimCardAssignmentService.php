<?php

namespace App\Services;

use App\Models\SimCardModel;
use App\Models\UnitOperasiModel;
use Illuminate\Database\Eloquent\Collection;

class SimCardAssignmentService
{
    /**
     * SIM card yang boleh dipilih: yang belum terpakai, ditambah yang sedang dipakai unit ini.
     */
    public function availableFor(?UnitOperasiModel $unit): Collection
    {
        return SimCardModel::where(function ($q) use ($unit) {
            $q->whereNull('unit_operasi_id');

            if ($unit) {
                $q->orWhere('unit_operasi_id', $unit->id);
            }
        })->orderBy('nomor_msisdn')->get();
    }

    public function rules(?UnitOperasiModel $unit): array
    {
        $simCardRule = function ($attribute, $value, $fail) use ($unit) {
            $usedByOther = SimCardModel::where('id', $value)
                ->whereNotNull('unit_operasi_id')
                ->when($unit, fn ($q) => $q->where('unit_operasi_id', '!=', $unit->id))
                ->exists();

            if ($usedByOther) {
                $fail('SIM Card ini sudah digunakan oleh unit lain.');
            }
        };

        return [
            'sim_card_ids' => ['nullable', 'array'],
            'sim_card_ids.*' => ['distinct', 'integer', 'exists:sim_cards,id', $simCardRule],
        ];
    }

    public function sync(UnitOperasiModel $unit, array $simCardIds): void
    {
        SimCardModel::where('unit_operasi_id', $unit->id)
            ->whereNotIn('id', $simCardIds)
            ->update(['unit_operasi_id' => null]);

        if (! empty($simCardIds)) {
            SimCardModel::whereIn('id', $simCardIds)->update(['unit_operasi_id' => $unit->id]);
        }
    }
}
