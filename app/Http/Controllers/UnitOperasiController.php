<?php

namespace App\Http\Controllers;

use App\Models\SimCardModel;
use App\Models\UnitOperasiModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitOperasiController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitOperasiModel::with('simCard');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code_unit', 'like', '%'.$search.'%')
                    ->orWhere('type_unit', 'like', '%'.$search.'%')
                    ->orWhere('remark', 'like', '%'.$search.'%');
            });
        }

        $dataUnitOperasi = $query->latest()->paginate(10)->withQueryString();

        $totalOfa = UnitOperasiModel::where('ofa', true)->count();
        $totalIcc = UnitOperasiModel::where('icc', true)->count();
        $totalSs6 = UnitOperasiModel::where('ss6', true)->count();

        return view('aset.unitoperasi.index', compact('dataUnitOperasi', 'totalOfa', 'totalIcc', 'totalSs6'));
    }

    public function create()
    {
        $availableSimCards = SimCardModel::whereNull('associate')->orderBy('nomor_msisdn')->get();

        return view('aset.unitoperasi.create', compact('availableSimCards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $validated['ofa'] = $request->boolean('ofa');
        $validated['icc'] = $request->boolean('icc');
        $validated['ss6'] = $request->boolean('ss6');

        if (!$validated['ofa']) {
            $validated['serial_number_module'] = null;
        }

        if (!$validated['ss6']) {
            $validated['serial_number_tab'] = null;
            $validated['no_aset_tab'] = null;
        }

        $unitoperasi = UnitOperasiModel::create($validated);

        $this->syncSimCardAssociation($unitoperasi, $validated['sim_card_id'] ?? null);

        return redirect()->route('aset.unitoperasi.index')->with('success', 'Data Unit Operasi berhasil ditambahkan.');
    }

    public function edit(UnitOperasiModel $unitoperasi)
    {
        $availableSimCards = SimCardModel::where(function ($q) use ($unitoperasi) {
            $q->whereNull('associate')->orWhere('id', $unitoperasi->sim_card_id);
        })->orderBy('nomor_msisdn')->get();

        return view('aset.unitoperasi.edit', compact('unitoperasi', 'availableSimCards'));
    }

    public function update(Request $request, UnitOperasiModel $unitoperasi)
    {
        $validated = $request->validate($this->validationRules($unitoperasi));

        $validated['ofa'] = $request->boolean('ofa');
        $validated['icc'] = $request->boolean('icc');
        $validated['ss6'] = $request->boolean('ss6');

        if (!$validated['ofa']) {
            $validated['serial_number_module'] = null;
        }

        if (!$validated['ss6']) {
            $validated['serial_number_tab'] = null;
            $validated['no_aset_tab'] = null;
        }

        $oldSimCardId = $unitoperasi->sim_card_id;

        $unitoperasi->update($validated);

        $this->syncSimCardAssociation($unitoperasi, $validated['sim_card_id'] ?? null, $oldSimCardId);

        return redirect()->route('aset.unitoperasi.index')->with('success', 'Data Unit Operasi berhasil diubah.');
    }

    public function destroy(UnitOperasiModel $unitoperasi)
    {
        if ($unitoperasi->sim_card_id) {
            SimCardModel::where('id', $unitoperasi->sim_card_id)->update(['associate' => null]);
        }

        $unitoperasi->delete();

        return redirect()->route('aset.unitoperasi.index')->with('success', 'Data Unit Operasi berhasil dihapus.');
    }

    private function validationRules(?UnitOperasiModel $unitoperasi = null): array
    {
        $simCardRule = function ($attribute, $value, $fail) use ($unitoperasi) {
            if (!$value) {
                return;
            }

            $usedByOther = SimCardModel::where('id', $value)
                ->whereNotNull('associate')
                ->when($unitoperasi, fn ($q) => $q->where('id', '!=', $unitoperasi->sim_card_id ?? 0))
                ->exists();

            if ($usedByOther) {
                $fail('SIM Card ini sudah digunakan oleh unit lain.');
            }
        };

        return [
            'code_unit' => [
                'required', 'string', 'max:255',
                $unitoperasi
                    ? Rule::unique('unit_operasi', 'code_unit')->ignore($unitoperasi->id)
                    : Rule::unique('unit_operasi', 'code_unit'),
            ],
            'type_unit' => ['required', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
            'ofa' => ['nullable', 'boolean'],
            'icc' => ['nullable', 'boolean'],
            'ss6' => ['nullable', 'boolean'],
            'serial_number_module' => ['nullable', 'string', 'max:255'],
            'serial_number_tab' => ['nullable', 'string', 'max:255'],
            'no_aset_tab' => ['nullable', 'string', 'max:255'],
            'sim_card_id' => ['nullable', 'exists:sim_cards,id', $simCardRule],
        ];
    }

    private function syncSimCardAssociation(UnitOperasiModel $unitoperasi, ?int $newSimCardId, ?int $oldSimCardId = null): void
    {
        if ($oldSimCardId && $oldSimCardId !== $newSimCardId) {
            SimCardModel::where('id', $oldSimCardId)->update(['associate' => null]);
        }

        if ($newSimCardId) {
            SimCardModel::where('id', $newSimCardId)->update(['associate' => $unitoperasi->code_unit]);
        }
    }
}
