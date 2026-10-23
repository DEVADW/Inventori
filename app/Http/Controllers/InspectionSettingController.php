<?php

namespace App\Http\Controllers;

use App\Models\InspectionSettingModel;
use App\Services\InspectionPeriod;
use Illuminate\Http\Request;

class InspectionSettingController extends Controller
{
    private const MODULES = [
        'stavolt' => 'Inspeksi Stavolt',
        'ups' => 'Inspeksi UPS',
        'monitor' => 'Inspeksi Monitor/TV',
        'proyektor' => 'Inspeksi Proyektor',
        'ss6' => 'Inspeksi SS6',
        'ofa' => 'Inspeksi OFA',
        'icc' => 'Inspeksi ICC',
    ];

    public function index()
    {
        $settings = collect(self::MODULES)->map(function ($label, $module) {
            return [
                'module' => $module,
                'label' => $label,
                'interval_months' => InspectionSettingModel::intervalMonths($module),
                'period_label' => InspectionPeriod::label($module),
            ];
        })->values();

        return view('settings.inspeksi.index', compact('settings'));
    }

    public function update(Request $request, string $module)
    {
        abort_unless(array_key_exists($module, self::MODULES), 404);

        $validated = $request->validate([
            'interval_months' => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        InspectionSettingModel::updateOrCreate(
            ['module' => $module],
            ['interval_months' => $validated['interval_months']]
        );

        return redirect()->route('settings.inspeksi.index')
            ->with('success', 'Periode inspeksi berhasil diperbarui.');
    }
}
