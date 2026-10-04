<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\inspeksi\Ss6Model;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class Ss6Controller extends Controller
{
    use HandlesInspectionPhoto;
    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi) {}

    public function index(Request $req)
    {
        $dataSs6 = $this->filteredQuery($req)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Inspeksi.ss6.index', [
            'dataSs6' => $dataSs6,
            'isGroupLeader' => $req->user()->jabatan === 'GL',
        ]);
    }

    public function create()
    {
        return view('Inspeksi.ss6.create');
    }

    public function export(Request $request)
    {
        return Excel::download(new InspectionExport($this->filteredQuery($request)->latest()->get(), [
            'No Asset' => 'no_asset', 'No Lambung' => 'no_lambung', 'Serial Number' => 'serial_number',
            'Tanggal Inspeksi' => 'tanggal_inspeksi', 'Status' => 'approval_status', 'Disetujui Oleh' => 'approved_by',
        ]), 'Inspeksi-SS6.xlsx');
    }

    public function store(Request $req)
    {
        // 1. Buat array rules dasar terlebih dahulu
        $rules = [
            'no_asset' => 'required|string|max:255',
            'no_lambung' => 'required|string|max:255',
            'tanggal_inspeksi' => 'required|date',
            'serial_number' => 'required|string|max:255',
            'output_powercharge' => 'required|string|max:255',

            'keterangan' => 'nullable|string',
        ];
        $rules = array_merge($rules, $this->photoValidationRules());

        // 2. Definisikan field yang ingin di-loop
        $feilds = [
            'kondisi_monitor',
            'kondisi_bracket',
            'kondisi_car_charger',
            'kondisi_kabel_power',
            'kondisi_app_lock',
            'software_ppa_teams',
            'kondisi_baterai',
        ];

        // 3. Masukkan rules tambahan secara dinamis lewat loop
        foreach ($feilds as $field) {
            // Ganti 'required|string' di bawah sesuai dengan validasi yang kamu butuhkan (misal: 'required|in:baik,rusak')
            $rules[$field . '_ketersediaan'] = 'required|string';
            $rules[$field . '_kondisi'] = 'required|string';
        }

        // 4. Jalankan validasi menggunakan array rules yang sudah lengkap
        $validated = $req->validate($rules);

        foreach ($feilds as $field) {
            // Gabung menjadi array key-value, lalu di-encode ke JSON string
            $validated[$field] = json_encode([
                'ketersediaan' => $validated[$field . '_ketersediaan'],
                'kondisi' => $validated[$field . '_kondisi'],
            ]);

            unset($validated[$field . '_ketersediaan']);
            unset($validated[$field . '_kondisi']);
        }

        // 5. Tambahkan data user yang login
        $validated['diinspeksi_oleh'] = auth()->user()->nrp;

        // 6. Simpan ke database
        $inspeksi = Ss6Model::create($validated);
        $this->storeInspectionPhoto($req, $inspeksi);

        return redirect()
            ->route('inspeksi.ss6.index')
            ->with('success', 'Data inspeksi berhasil disimpan');
    }

    public function edit(Ss6Model $inspeksi)
    {
        return view('Inspeksi.ss6.edit', compact('inspeksi'));
    }

    public function update(Request $req, Ss6Model $inspeksi)
    {
        if (!$this->approvalService->canUpdate($inspeksi)) {
            return redirect()->route('inspeksi.ss6.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        // 1. Buat array rules dasar terlebih dahulu
        $rules = [
            'no_asset' => 'required|string|max:255',
            'no_lambung' => 'required|string|max:255',
            'tanggal_inspeksi' => 'required|date',
            'serial_number' => 'required|string|max:255',
            'output_powercharge' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ];
        $rules = array_merge($rules, $this->photoValidationRules());

        // 2. Definisikan field yang ingin di-loop
        $feilds = [
            'kondisi_monitor',
            'kondisi_bracket',
            'kondisi_car_charger',
            'kondisi_kabel_power',
            'kondisi_app_lock',
            'software_ppa_teams',
            'kondisi_baterai',
        ];

        // 3. Masukkan rules tambahan secara dinamis lewat loop
        foreach ($feilds as $field) {
            // Ganti 'required|string' di bawah sesuai dengan validasi yang kamu butuhkan (misal: 'required|in:baik,rusak')
            $rules[$field . '_ketersediaan'] = 'required|string';
            $rules[$field . '_kondisi'] = 'required|string';
        }

        // 4. Jalankan validasi menggunakan array rules yang sudah lengkap
        $validated = $req->validate($rules);

        foreach ($feilds as $field) {
            // Gabung menjadi array key-value, lalu di-encode ke JSON string
            $validated[$field] = json_encode([
                'ketersediaan' => $validated[$field . '_ketersediaan'],
                'kondisi' => $validated[$field . '_kondisi'],
            ]);

            unset($validated[$field . '_ketersediaan']);
            unset($validated[$field . '_kondisi']);
        }

        $validated['diinspeksi_oleh'] = auth()->user()->nama;

        if (auth()->user()->jabatan == 'GL') {
            $validated['diperiksa_oleh'] = auth()->user()->nama;
        }

        $inspeksi->update($validated);
        $this->storeInspectionPhoto($req, $inspeksi);

        return redirect()
            ->route('inspeksi.ss6.index')
            ->with('success', 'Data inspeksi berhasil diperbarui.');
    }

    public function destroy(Ss6Model $inspeksi)
    {
        $inspeksi->delete();

        return redirect()
            ->route('inspeksi.ss6.index')
            ->with('success', 'Data inspeksi berhasil dihapus.');
    }

 

    public function pdf(Ss6Model $inspeksi)
    {
        return Pdf::loadView('pdf.inspeksi_ss6', compact('inspeksi'))
            ->setPaper('A4', 'portrait')
            ->stream("Checklist-Inspeksi-SS6-{$inspeksi->no_asset}.pdf");
    }

    public function approve(Request $request, Ss6Model $inspeksi)
    {
        $result = $this->approvalService->approve($request, $inspeksi);

        if (!$result['success']) {
            return back()->with($result['type'], $result['message']);
        }

        return back()->with($result['type'], $result['message']);
    }

    public function approveAll(Request $request)
    {
        $result = $this->approvalService->approveMultiple(
            $request,
            $this->filteredQuery($request)
        );

        if (!$result['success']) {
            return back()->with($result['type'], $result['message']);
        }

        return redirect()->route('inspeksi.ss6.index', $request->only('search'))
            ->with($result['type'], $result['message']);
    }

    public function downloadApproved(Request $request)
    {
        $period = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);
        $month = $period['month'] ?? now()->month;
        $year = $period['year'] ?? now()->year;
        $inspections = $this->filteredQuery($request)->whereNotNull('approved_at')
            ->whereYear('tanggal_inspeksi', $year)->whereMonth('tanggal_inspeksi', $month)
            ->latest()->get();
        if ($inspections->isEmpty()) {
            return back()->with('error', "Belum ada inspeksi approved untuk {$month}/{$year}.");
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'ss6-approved-');
        $zipPath = $temporaryFile . '.zip';
        @unlink($temporaryFile);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat arsip PDF.');
        }

        foreach ($inspections as $inspection) {
            $pdf = Pdf::loadView('pdf.inspeksi_ss6', ['inspeksi' => $inspection])
                ->setPaper('A4', 'portrait')
                ->output();
            $zip->addFromString("Checklist-SS6-{$inspection->no_asset}-{$inspection->id}.pdf", $pdf);
        }
        $zip->close();

        return response()->download($zipPath, 'Inspeksi-SS6-Approved.zip')->deleteFileAfterSend(true);
    }

    private function filteredQuery(Request $request)
    {
        return Ss6Model::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['no_asset', 'no_lambung', 'serial_number', 'diperiksa_oleh', 'diinspeksi_oleh'];
            $query->search($cols, $request->search);
        });
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new Ss6Model(), 'inspeksi.ss6.index');
    }
}
