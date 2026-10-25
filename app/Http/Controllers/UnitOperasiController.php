<?php

namespace App\Http\Controllers;

use App\Models\SimCardModel;
use App\Models\UnitOperasiModel;
use App\Services\SimCardAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UnitOperasiController extends Controller
{
    public function __construct(protected SimCardAssignmentService $simCardAssignment) {}

    public function index(Request $request)
    {
        $query = UnitOperasiModel::with('simCards');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code_unit', 'like', '%'.$search.'%')
                    ->orWhere('type_unit', 'like', '%'.$search.'%')
                    ->orWhere('remark', 'like', '%'.$search.'%');
            });
        }

        $sortable = ['code_unit', 'type_unit', 'remark'];
        $sort = $request->input('sort');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if (in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id');
        }

        $dataUnitOperasi = $query->paginate(10)->withQueryString();

        $totalOfa = UnitOperasiModel::where('ofa', true)->count();
        $totalIcc = UnitOperasiModel::where('icc', true)->count();
        $totalSs6 = UnitOperasiModel::where('ss6', true)->count();

        return view('aset.unitoperasi.index', compact('dataUnitOperasi', 'totalOfa', 'totalIcc', 'totalSs6'));
    }

    public function create()
    {
        $availableSimCards = $this->simCardAssignment->availableFor(null);

        return view('aset.unitoperasi.create', compact('availableSimCards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $simCardIds = $validated['sim_card_ids'] ?? [];
        unset($validated['sim_card_ids']);

        $validated['ofa'] = $request->boolean('ofa');
        $validated['icc'] = $request->boolean('icc');
        $validated['ss6'] = $request->boolean('ss6');

        if (! $validated['ofa']) {
            $validated['serial_number_module'] = null;
        }

        if (! $validated['ss6']) {
            $validated['serial_number_tab'] = null;
            $validated['no_aset_tab'] = null;
        }

        $unitoperasi = UnitOperasiModel::create($validated);

        $this->simCardAssignment->sync($unitoperasi, $simCardIds);

        return redirect()->route('aset.unitoperasi.index')->with('success', 'Data Unit Operasi berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $file = $request->file('file');

        if (strcasecmp($file->getClientOriginalName(), 'data_aset.xlsx') !== 0) {
            throw ValidationException::withMessages([
                'file' => ['Nama file harus data_aset.xlsx.'],
            ]);
        }

        $rows = Excel::toCollection(null, $file)->first();

        if ($rows === null || $rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => ['File Excel tidak memiliki data untuk diimpor.'],
            ]);
        }

        $data = [];
        $errors = [];
        $seenCodes = [];
        $seenSimCards = [];
        $simCardsByMsisdn = SimCardModel::query()->get()->keyBy('nomor_msisdn');

        foreach ($rows as $index => $row) {
            // Baris 1 adalah header. Data selalu dimulai dari baris 2.
            if ($index === 0) {
                continue;
            }

            $rowNumber = $index + 1;
            $values = collect($row)->values();
            $simCardNumber = $this->excelValue($values->get(3));
            $ofa = $this->excelBoolean($this->excelValue($values->get(4)));
            $icc = $this->excelBoolean($this->excelValue($values->get(5)));
            $ss6 = $this->excelBoolean($this->excelValue($values->get(6)));

            $simCardId = $simCardNumber ? $simCardsByMsisdn->get($simCardNumber)?->id : null;

            $record = [
                'code_unit' => $this->excelValue($values->get(1)), // Kolom B
                'type_unit' => $this->excelValue($values->get(2)), // Kolom C
                'ofa' => $ofa ?? false, // Kolom E
                'icc' => $icc ?? false, // Kolom F
                'ss6' => $ss6 ?? false, // Kolom G
                'serial_number_module' => $ofa ? $this->excelValue($values->get(7)) : null, // Kolom H
                'serial_number_tab' => $ss6 ? $this->excelValue($values->get(8)) : null, // Kolom I
                'no_aset_tab' => $ss6 ? $this->excelValue($values->get(9)) : null, // Kolom J
                'remark' => $this->excelValue($values->get(10)), // Kolom K
            ];

            if (collect($record)->except(['ofa', 'icc', 'ss6'])->filter(fn ($value) => $value !== null)->isEmpty()) {
                continue;
            }

            $validator = Validator::make($record, [
                'code_unit' => ['required', 'string', 'max:255'],
                'type_unit' => ['required', 'string', 'max:255'],
                'ofa' => ['boolean'],
                'icc' => ['boolean'],
                'ss6' => ['boolean'],
                'serial_number_module' => ['nullable', 'string', 'max:255'],
                'serial_number_tab' => ['nullable', 'string', 'max:255'],
                'no_aset_tab' => ['nullable', 'string', 'max:255'],
                'remark' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors['file'][] = "Baris {$rowNumber}: {$message}";
                }
            }

            foreach (['ofa' => $ofa, 'icc' => $icc, 'ss6' => $ss6] as $field => $value) {
                if ($this->excelValue($values->get(['ofa' => 4, 'icc' => 5, 'ss6' => 6][$field])) !== null && $value === null) {
                    $errors['file'][] = "Baris {$rowNumber}: nilai {$field} harus Ya/Tidak, True/False, atau 1/0.";
                }
            }

            if ($record['code_unit'] !== null) {
                if (isset($seenCodes[$record['code_unit']])) {
                    $errors['file'][] = "Baris {$rowNumber}: code number unit duplikat dalam file.";
                }

                $seenCodes[$record['code_unit']] = true;
            }

            if ($simCardNumber !== null) {
                $simCard = $simCardsByMsisdn->get($simCardNumber);

                if (! $simCard) {
                    $errors['file'][] = "Baris {$rowNumber}: nomor SIM Card {$simCardNumber} tidak ditemukan.";
                } elseif ($simCard->unit_operasi_id !== null) {
                    $errors['file'][] = "Baris {$rowNumber}: nomor SIM Card {$simCardNumber} sudah digunakan oleh unit lain.";
                } elseif (isset($seenSimCards[$simCardNumber])) {
                    $errors['file'][] = "Baris {$rowNumber}: nomor SIM Card digunakan lebih dari satu kali dalam file.";
                }

                $seenSimCards[$simCardNumber] = true;
            }

            $data[] = ['record' => $record, 'sim_card_id' => $simCardId];
        }

        if (empty($data)) {
            $errors['file'][] = 'Tidak ada baris data yang dapat diimpor. Pastikan data dimulai dari baris 2.';
        }

        $existingCodes = UnitOperasiModel::query()
            ->whereIn('code_unit', array_keys($seenCodes))
            ->pluck('code_unit')
            ->all();

        foreach ($existingCodes as $code) {
            $errors['file'][] = "Code number unit {$code} sudah terdaftar.";
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $row) {
                $unitOperasi = UnitOperasiModel::create($row['record']);
                $this->simCardAssignment->sync($unitOperasi, $row['sim_card_id'] ? [$row['sim_card_id']] : []);
            }
        });

        return redirect()->route('aset.unitoperasi.index')
            ->with('success', count($data).' data Unit Operasi berhasil diimpor.');
    }

    public function edit(UnitOperasiModel $unitoperasi)
    {
        $availableSimCards = $this->simCardAssignment->availableFor($unitoperasi);

        return view('aset.unitoperasi.edit', compact('unitoperasi', 'availableSimCards'));
    }

    public function update(Request $request, UnitOperasiModel $unitoperasi)
    {
        $validated = $request->validate($this->validationRules($unitoperasi));

        $simCardIds = $validated['sim_card_ids'] ?? [];
        unset($validated['sim_card_ids']);

        $validated['ofa'] = $request->boolean('ofa');
        $validated['icc'] = $request->boolean('icc');
        $validated['ss6'] = $request->boolean('ss6');

        if (! $validated['ofa']) {
            $validated['serial_number_module'] = null;
        }

        if (! $validated['ss6']) {
            $validated['serial_number_tab'] = null;
            $validated['no_aset_tab'] = null;
        }

        $unitoperasi->update($validated);

        $this->simCardAssignment->sync($unitoperasi, $simCardIds);

        return redirect()->route('aset.unitoperasi.index')->with('success', 'Data Unit Operasi berhasil diubah.');
    }

    public function destroy(UnitOperasiModel $unitoperasi)
    {
        SimCardModel::where('unit_operasi_id', $unitoperasi->id)->update(['unit_operasi_id' => null]);

        $unitoperasi->delete();

        return redirect()->route('aset.unitoperasi.index')->with('success', 'Data Unit Operasi berhasil dihapus.');
    }

    private function validationRules(?UnitOperasiModel $unitoperasi = null): array
    {
        return array_merge([
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
        ], $this->simCardAssignment->rules($unitoperasi));
    }

    private function excelValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function excelBoolean(?string $value): ?bool
    {
        if ($value === null) {
            return false;
        }

        return match (strtolower($value)) {
            '1', 'true', 'ya', 'yes' => true,
            '0', 'false', 'tidak', 'no' => false,
            default => null,
        };
    }
}
