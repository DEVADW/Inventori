<?php

namespace App\Services;

use App\Models\InspectionSettingModel;
use Illuminate\Support\Carbon;

class InspectionPeriod
{
    private const EPOCH = '2024-01-01';

    /**
     * Returns [Carbon $start, Carbon $end] for the period containing $reference,
     * aligned to a fixed epoch using the module's configured interval (in months).
     */
    public static function currentRange(string $module, ?Carbon $reference = null): array
    {
        $interval = max(1, InspectionSettingModel::intervalMonths($module));
        $reference = ($reference ?? now())->copy()->startOfDay();
        $epoch = Carbon::parse(self::EPOCH)->startOfDay();

        $monthsSinceEpoch = (int) $epoch->diffInMonths($reference);
        $periodIndex = intdiv($monthsSinceEpoch, $interval);

        $start = $epoch->copy()->addMonths($periodIndex * $interval)->startOfDay();
        $end = $start->copy()->addMonths($interval)->subDay()->endOfDay();

        return [$start, $end];
    }

    public static function label(string $module, ?Carbon $reference = null): string
    {
        [$start, $end] = self::currentRange($module, $reference);

        return $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y');
    }
}
