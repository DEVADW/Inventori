<?php

namespace App\Http\Controllers;

use App\Models\InventoriStavoltModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class InventoriStavoltController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoriStavoltModel::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_aset', 'like', '%'.$search.'%')
                    ->orWhere('merek', 'like', '%'.$search.'%')
                    ->orWhere('tipe', 'like', '%'.$search.'%')
                    ->orWhere('serial_number', 'like', '%'.$search.'%')
                    ->orWhere('department', 'like', '%'.$search.'%')
                    ->orWhere('lokasi', 'like', '%'.$search.'%');
            });
        }

        $dataInventoriStavolt = $query->latest()->paginate(10)->withQueryString();

        return view('aset.inventoristavolt.index', compact('dataInventoriStavolt'));
    }

    public function create()
    {
        return view('aset.inventoristavolt.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', 'unique:inventori_stavolt,no_aset'],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        InventoriStavoltModel::create($validated);

        return redirect()->route('aset.inventoristavolt.index')->with('success', 'Data Inventori Stavolt berhasil ditambahkan.');
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
        $seenNoAset = [];

        foreach ($rows as $index => $row) {
            // Baris 1 adalah header. Data selalu dimulai dari baris 2.
            if ($index === 0) {
                continue;
            }

            $rowNumber = $index + 1;
            $values = collect($row)->values();

            $record = [
                'no_aset' => $this->excelValue($values->get(1)), // Kolom B
                'merek' => $this->excelValue($values->get(2)), // Kolom C
                'tipe' => $this->excelValue($values->get(3)), // Kolom D
                'serial_number' => $this->excelValue($values->get(4)), // Kolom E
                'department' => $this->excelValue($values->get(5)), // Kolom F
                'lokasi' => $this->excelValue($values->get(6)), // Kolom G
            ];

            if (collect($record)->filter(fn ($value) => $value !== null)->isEmpty()) {
                continue;
            }

            $validator = Validator::make($record, [
                'no_aset' => ['required', 'string', 'max:255'],
                'merek' => ['required', 'string', 'max:255'],
                'tipe' => ['required', 'string', 'max:255'],
                'serial_number' => ['required', 'string', 'max:255'],
                'department' => ['required', 'string', 'max:255'],
                'lokasi' => ['required', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors['file'][] = "Baris {$rowNumber}: {$message}";
                }
            }

            if ($record['no_aset'] !== null) {
                if (isset($seenNoAset[$record['no_aset']])) {
                    $errors['file'][] = "Baris {$rowNumber}: nomor aset duplikat dalam file.";
                }

                $seenNoAset[$record['no_aset']] = true;
            }

            $data[] = $record;
        }

        if (empty($data)) {
            $errors['file'][] = 'Tidak ada baris data yang dapat diimpor. Pastikan data dimulai dari baris 2.';
        }

        $existingNoAset = InventoriStavoltModel::query()
            ->whereIn('no_aset', array_keys($seenNoAset))
            ->pluck('no_aset')
            ->all();

        foreach ($existingNoAset as $noAset) {
            $errors['file'][] = "Nomor aset {$noAset} sudah terdaftar.";
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $record) {
                InventoriStavoltModel::create($record);
            }
        });

        return redirect()->route('aset.inventoristavolt.index')
            ->with('success', count($data).' data Inventori Stavolt berhasil diimpor.');
    }

    private function excelValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public function edit(InventoriStavoltModel $inventoristavolt)
    {
        return view('aset.inventoristavolt.edit', compact('inventoristavolt'));
    }

    public function update(Request $request, InventoriStavoltModel $inventoristavolt)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', Rule::unique('inventori_stavolt', 'no_aset')->ignore($inventoristavolt->id)],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        $inventoristavolt->update($validated);

        return redirect()->route('aset.inventoristavolt.index')->with('success', 'Data Inventori Stavolt berhasil diubah.');
    }

    public function destroy(InventoriStavoltModel $inventoristavolt)
    {
        $inventoristavolt->delete();

        return redirect()->route('aset.inventoristavolt.index')->with('success', 'Data Inventori Stavolt berhasil dihapus.');
    }
}
