<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\UnitOperasiModel;
use App\Models\inspeksi\IccModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use App\Services\InspectionPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class IccController extends Controller
{
    use HandlesInspectionPhoto;

    private const MODULE = 'icc';

    /** Item dan nomor mengikuti PPA-ADRO-F-ICTMD-035. Nomor 12 memang tidak ada pada form sumber. */
    public const CHECKLIST_ITEMS = [
        1 => 'Kamera: Lensa bersih, tidak tergores',
        2 => 'Kamera: Posisi dan sudut pengambilan gambar sesuai',
        3 => 'Kamera: Kabel dan konektor terpasang dengan baik',
        4 => 'Power / Catu Daya: ICC menyala saat kendaraan hidup',
        5 => 'Power / Catu Daya: Tidak ada indikasi korsleting atau kabel terbakar',
        6 => 'Perekaman: Fungsi rekam berjalan normal (audio & video)',
        7 => 'Memori / SD Card: Tersedia, kapasitas cukup, tidak error',
        8 => 'Playback / Monitoring: Video bisa diputar melalui monitor / aplikasi',
        9 => 'Lampu Indikator / Status: Semua indikator berfungsi',
        10 => 'Kabel dan Aksesoris: Tidak ada kabel yang longgar atau rusak',
        11 => 'Vibrator: Berfungsi sesuai peringatan / alarm',
        13 => 'Fatigue Lamp / Alarm: Menyala dan berfungsi saat sistem mendeteksi kelelahan',
        14 => 'Fungsi Deteksi Kamera: Sistem mendeteksi kamera aktif & merekam dengan benar',
    ];

    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi) {}

    public function index(Request $request)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $assetQuery = UnitOperasiModel::where('icc', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $assetQuery->where(function ($q) use ($search) {
                $q->where('code_unit', 'like', "%{$search}%")
                    ->orWhere('type_unit', 'like', "%{$search}%");
            });
        }

        $assets = $assetQuery->orderBy('code_unit')->paginate(15)->withQueryString();

        $inspectionsThisPeriod = IccModel::whereIn('no_lambung_unit', $assets->pluck('code_unit'))
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->get()
            ->keyBy('no_lambung_unit');

        $assets->each(function ($asset) use ($inspectionsThisPeriod) {
            $asset->currentInspection = $inspectionsThisPeriod->get($asset->code_unit);
        });

        $totalAset = UnitOperasiModel::where('icc', true)->count();
        $totalSudah = IccModel::whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->whereIn('no_lambung_unit', UnitOperasiModel::where('icc', true)->pluck('code_unit'))
            ->distinct()
            ->count('no_lambung_unit');

        return view('Inspeksi.icc.index', [
            'assets' => $assets,
            'periodLabel' => InspectionPeriod::label(self::MODULE),
            'totalAset' => $totalAset,
            'totalSudah' => $totalSudah,
            'totalBelum' => max(0, $totalAset - $totalSudah),
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function riwayat(Request $request)
    {
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $inspections = $this->filteredQuery($request)
            ->whereYear('tanggal_inspeksi', $year)
            ->whereMonth('tanggal_inspeksi', $month)
            ->latest('tanggal_inspeksi')
            ->paginate(15)
            ->withQueryString();

        $availableYears = IccModel::query()
            ->selectRaw('DISTINCT YEAR(tanggal_inspeksi) as year')
            ->whereNotNull('tanggal_inspeksi')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty() || !$availableYears->contains($year)) {
            $availableYears = $availableYears->push($year)->unique()->sortDesc()->values();
        }

        return view('Inspeksi.icc.riwayat', [
            'inspections' => $inspections,
            'month' => $month,
            'year' => $year,
            'availableYears' => $availableYears,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create(UnitOperasiModel $asset)
    {
        abort_unless($asset->icc, 404);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $existing = IccModel::where('no_lambung_unit', $asset->code_unit)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->first();

        if ($existing) {
            return redirect()->route('inspeksi.icc.edit', $existing)
                ->with('success', 'Unit ini sudah diinspeksi pada periode berjalan.');
        }

        return view('Inspeksi.icc.create', [
            'asset' => $asset,
            'checklistItems' => self::CHECKLIST_ITEMS,
            'periodLabel' => InspectionPeriod::label(self::MODULE),
            'deviceInfo' => [
                'no_lambung_unit' => $asset->code_unit,
            ],
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new InspectionExport($this->filteredQuery($request)->latest()->get(), [
            'No. Lambung Unit' => 'no_lambung_unit', 'Tanggal Inspeksi' => 'tanggal_inspeksi',
            'Lokasi' => 'lokasi_inspeksi', 'Inspektor' => 'inspektor',
            'Status' => 'approval_status', 'Disetujui Oleh' => 'approved_by',
        ]), 'Inspeksi-ICC.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            ['asset_id' => ['required', 'exists:unit_operasi,id', $this->iccAssetRule()]],
            $this->checklistValidationRules()
        ));

        $asset = UnitOperasiModel::findOrFail($validated['asset_id']);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);
        $alreadyInspected = IccModel::where('no_lambung_unit', $asset->code_unit)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('inspeksi.icc.index')
                ->with('error', 'Unit ini sudah diinspeksi pada periode berjalan.');
        }

        $data = $validated;
        unset($data['asset_id']);
        $data['no_lambung_unit'] = $asset->code_unit;
        $data['item_pemeriksaan'] = collect($data['item_pemeriksaan'])->values()->all();
        $data['inspektor'] = $request->user()->nrp;

        $icc = IccModel::create($data);
        $this->storeInspectionPhoto($request, $icc);

        return redirect()->route('inspeksi.icc.index')->with('success', 'Data pemeliharaan ICC berhasil disimpan.');
    }

    public function edit(IccModel $icc)
    {
        return view('Inspeksi.icc.edit', [
            'icc' => $icc,
            'checklistItems' => self::CHECKLIST_ITEMS,
            'deviceInfo' => [
                'no_lambung_unit' => $icc->no_lambung_unit,
            ],
        ]);
    }

    public function update(Request $request, IccModel $icc)
    {
        if (!$this->approvalService->canUpdate($icc)) {
            return redirect()->route('inspeksi.icc.index')->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $request->validate($this->checklistValidationRules());
        $data['item_pemeriksaan'] = collect($data['item_pemeriksaan'])->values()->all();

        $icc->update($data);
        $this->storeInspectionPhoto($request, $icc);

        return redirect()->route('inspeksi.icc.index')->with('success', 'Data pemeliharaan ICC berhasil diperbarui.');
    }

    public function destroy(IccModel $icc)
    {
        $icc->delete();

        return redirect()->route('inspeksi.icc.index')->with('success', 'Data pemeliharaan ICC berhasil dihapus.');
    }

    public function pdf(IccModel $icc)
    {
        return Pdf::loadView('pdf.inspeksi_icc', compact('icc'))->setPaper('A4', 'portrait')
            ->stream("Formulir-Pemeliharaan-ICC-{$icc->no_lambung_unit}.pdf");
    }

    public function approve(Request $request, IccModel $icc)
    {
        $result = $this->approvalService->approve($request, $icc);

        if (!$result['success']) {
            return back()->with($result['type'], $result['message']);
        }

        return back()->with($result['type'], $result['message']);
    }

    public function approveAll(Request $request)
    {
        $result = $this->approvalService->approveMultiple($request, $this->filteredQuery($request));

        if (!$result['success']) {
            return back()->with($result['type'], $result['message']);
        }

        return redirect()->route('inspeksi.icc.index', $request->only('search'))->with($result['type'], $result['message']);
    }

    public function downloadApproved(Request $request)
    {
        $period = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);
        $month = $period['month'] ?? now()->month;
        $year = $period['year'] ?? now()->year;
        $iccs = $this->filteredQuery($request)->whereNotNull('approved_at')
            ->whereYear('tanggal_inspeksi', $year)->whereMonth('tanggal_inspeksi', $month)
            ->latest()->get();
        if ($iccs->isEmpty()) {
            return back()->with('error', "Belum ada pemeliharaan ICC approved untuk {$month}/{$year}.");
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'icc-approved-');
        $zipPath = $temporaryFile . '.zip';
        @unlink($temporaryFile);
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat arsip PDF.');
        }

        foreach ($iccs as $icc) {
            $pdf = Pdf::loadView('pdf.inspeksi_icc', compact('icc'))->setPaper('A4', 'portrait')->output();
            $zip->addFromString("Formulir-Pemeliharaan-ICC-{$icc->no_lambung_unit}-{$icc->id}.pdf", $pdf);
        }
        $zip->close();

        return response()->download($zipPath, 'Pemeliharaan-ICC-Approved.zip')->deleteFileAfterSend(true);
    }

    private function iccAssetRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if ($value && !UnitOperasiModel::where('id', $value)->where('icc', true)->exists()) {
                $fail('Unit ini tidak terdaftar sebagai ICC.');
            }
        };
    }

    private function checklistValidationRules(): array
    {
        return array_merge([
            'tanggal_inspeksi' => ['required', 'date'],
            'lokasi_inspeksi' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'item_pemeriksaan' => ['required', 'array', 'min:1'],
            'item_pemeriksaan.*.nama' => ['required', 'string', 'max:255'],
            'item_pemeriksaan.*.no' => ['required', 'integer'],
            'item_pemeriksaan.*.status' => ['required', 'in:iya,tidak'],
            'item_pemeriksaan.*.keterangan' => ['nullable', 'string', 'max:1000'],
            'item_pemeriksaan.*.tindakan' => ['nullable', 'string', 'max:1000'],
        ], $this->photoValidationRules());
    }

    private function filteredQuery(Request $request)
    {
        return IccModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['no_lambung_unit', 'lokasi_inspeksi', 'inspektor'];
            $query->search($cols, $request->search);
        });
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new IccModel(), 'inspeksi.icc.index');
    }
}
