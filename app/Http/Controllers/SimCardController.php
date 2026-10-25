<?php

namespace App\Http\Controllers;

use App\Models\SimCardModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class SimCardController extends Controller
{
    public function index(Request $request)
    {
        $query = SimCardModel::with('unitOperasi');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nomor_msisdn', 'like', '%'.$search.'%')
                    ->orWhere('remark', 'like', '%'.$search.'%')
                    ->orWhereHas('unitOperasi', function ($unitQuery) use ($search) {
                        $unitQuery->where('code_unit', 'like', '%'.$search.'%');
                    });
            });
        }

        $sortable = ['nomor_msisdn', 'jumlah_paket_kuota', 'status_kartu', 'remark'];
        $sort = $request->input('sort');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if (in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id');
        }

        $dataSimCard = $query->paginate(10)->withQueryString();

        $totalTerpasang = SimCardModel::where('status_kartu', 'Terpasang')->count();
        $totalStandby = SimCardModel::where('status_kartu', 'Standby')->count();
        $totalNonaktif = SimCardModel::where('status_kartu', 'Nonaktif')->count();

        return view('aset.simcard.index', compact('dataSimCard', 'totalTerpasang', 'totalStandby', 'totalNonaktif'));
    }

    public function create()
    {
        return view('aset.simcard.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_msisdn' => ['required', 'string', 'max:20', 'unique:sim_cards,nomor_msisdn'],
            'jumlah_paket_kuota' => ['nullable', 'string', 'max:255'],
            'status_kartu' => ['required', Rule::in(['Terpasang', 'Standby', 'Nonaktif'])],
            'remark' => ['nullable', 'string'],
        ]);

        SimCardModel::create($validated);

        return redirect()->route('aset.simcard.index')->with('success', 'Data SIM Card berhasil ditambahkan.');
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

        $sheets = Excel::toCollection(null, $file);
        $rows = $sheets->first();

        if ($rows === null || $rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => ['File Excel tidak memiliki data untuk diimpor.'],
            ]);
        }

        $data = [];
        $errors = [];
        $seenMsisdn = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            $values = collect($row)->values();

            // Lewati heading bila sheet memiliki judul kolom pada baris pertama.
            if ($index === 0 && in_array(strtolower(trim((string) $values->get(1))), ['nomor msisdn', 'nomor_msisdn'], true)) {
                continue;
            }

            $record = [
                'nomor_msisdn' => $this->excelValue($values->get(1)), // Kolom 2 / B
                'jumlah_paket_kuota' => $this->excelValue($values->get(2)), // Kolom 3 / C
                'status_kartu' => $this->normaliseStatusKartu($this->excelValue($values->get(3))), // Kolom 4 / D
                'remark' => $this->excelValue($values->get(4)), // Kolom 5 / E
            ];

            if (collect($record)->filter(fn ($value) => $value !== null)->isEmpty()) {
                continue;
            }

            $validator = Validator::make($record, [
                'nomor_msisdn' => ['required', 'string', 'max:20'],
                'jumlah_paket_kuota' => ['nullable', 'string', 'max:255'],
                'status_kartu' => ['required', Rule::in(['Terpasang', 'Standby', 'Nonaktif'])],
                'remark' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors['file'][] = "Baris {$rowNumber}: {$message}";
                }
            }

            if ($record['nomor_msisdn'] !== null) {
                if (isset($seenMsisdn[$record['nomor_msisdn']])) {
                    $errors['file'][] = "Baris {$rowNumber}: nomor MSISDN duplikat dalam file.";
                }

                $seenMsisdn[$record['nomor_msisdn']] = true;
            }

            $data[] = $record;
        }

        if (empty($data)) {
            $errors['file'][] = 'Tidak ada baris data yang dapat diimpor.';
        }

        $existingMsisdn = SimCardModel::query()
            ->whereIn('nomor_msisdn', array_keys($seenMsisdn))
            ->pluck('nomor_msisdn')
            ->all();

        foreach ($existingMsisdn as $msisdn) {
            $errors['file'][] = "Nomor MSISDN {$msisdn} sudah terdaftar.";
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $record) {
                SimCardModel::create($record);
            }
        });

        return redirect()->route('aset.simcard.index')
            ->with('success', count($data).' data SIM Card berhasil diimpor.');
    }

    public function edit(SimCardModel $simcard)
    {
        return view('aset.simcard.edit', compact('simcard'));
    }

    public function update(Request $request, SimCardModel $simcard)
    {
        $validated = $request->validate([
            'nomor_msisdn' => ['required', 'string', 'max:20', Rule::unique('sim_cards', 'nomor_msisdn')->ignore($simcard->id)],
            'jumlah_paket_kuota' => ['nullable', 'string', 'max:255'],
            'status_kartu' => ['required', Rule::in(['Terpasang', 'Standby', 'Nonaktif'])],
            'remark' => ['nullable', 'string'],
        ]);

        $simcard->update($validated);

        return redirect()->route('aset.simcard.index')->with('success', 'Data SIM Card berhasil diubah.');
    }

    public function destroy(SimCardModel $simcard)
    {
        $simcard->delete();

        return redirect()->route('aset.simcard.index')->with('success', 'Data SIM Card berhasil dihapus.');
    }

    private function excelValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normaliseStatusKartu(?string $status): ?string
    {
        return match (strtolower((string) $status)) {
            'terpasang' => 'Terpasang',
            'standby' => 'Standby',
            'nonaktif' => 'Nonaktif',
            default => $status,
        };
    }
}
