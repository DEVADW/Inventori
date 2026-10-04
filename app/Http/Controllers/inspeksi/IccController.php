<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\IccUnitModels;
use App\Models\inspeksi\IccModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class IccController extends Controller
{
    use HandlesInspectionPhoto;
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
        $iccs = $this->filteredQuery($request)->latest()->paginate(15)->withQueryString();

        return view('Inspeksi.icc.index', [
            'iccs' => $iccs,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create()
    {
        return view('Inspeksi.icc.create', ['checklistItems' => self::CHECKLIST_ITEMS]);
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
        $icc = IccModel::create($this->validatedData($request));
        $this->storeInspectionPhoto($request, $icc);

        return redirect()->route('inspeksi.icc.index')->with('success', 'Data pemeliharaan ICC berhasil disimpan.');
    }

    public function edit(IccModel $icc)
    {
        return view('Inspeksi.icc.edit', [
            'icc' => $icc,
            'checklistItems' => self::CHECKLIST_ITEMS,
        ]);
    }

    public function update(Request $request, IccModel $icc)
    {
        if (!$this->approvalService->canUpdate($icc)) {
            return redirect()->route('inspeksi.icc.index')->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $icc->update($this->validatedData($request));
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

        return back()->with($result['type'], $result['message']);
    }

    public function approveAll(Request $request)
    {
        $result = $this->approvalService->approveMultiple($request, $this->filteredQuery($request));

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

    private function validatedData(Request $request): array
    {
        $data = $request->validate(array_merge([
            'no_lambung_unit' => ['required', 'string', 'max:255'],
            'tanggal_inspeksi' => ['required', 'date'],
            'lokasi_inspeksi' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'item_pemeriksaan' => ['required', 'array', 'min:1'],
            'item_pemeriksaan.*.nama' => ['required', 'string', 'max:255'],
            'item_pemeriksaan.*.no' => ['required', 'integer'],
            'item_pemeriksaan.*.status' => ['required', 'in:iya,tidak'],
            'item_pemeriksaan.*.keterangan' => ['nullable', 'string', 'max:1000'],
            'item_pemeriksaan.*.tindakan' => ['nullable', 'string', 'max:1000'],
        ], $this->photoValidationRules()));
        $data['item_pemeriksaan'] = collect($data['item_pemeriksaan'])->values()->all();
        $data['inspektor'] = $request->user()->nrp;

        return $data;
    }

    private function filteredQuery(Request $request)
    {
        return IccModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['no_lambung_unit', 'lokasi_inspeksi', 'inspektor'];
            $query->search($cols, $request->search);
        });
    }

    public function select2(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('q', $request->input('search', '')));
        $perPage = 20;

        $query = IccUnitModels::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                    ->orWhere('fleet', 'like', "%{$search}%")
                    ->orWhere('sim', 'like', "%{$search}%")
                    ->orWhere('imei', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('device_name')->paginate($perPage);

        $results = $units->getCollection()->map(function ($unit) {
            return [
                'id' => $unit->id,
                'text' => "{$unit->device_name} - {$unit->imei} - {$unit->fleet} - {$unit->sim}",
                'code_unit' => $unit->device_name,
                'fleet' => $unit->fleet,
                'sim' => $unit->sim,
                'imei' => $unit->imei,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $units->currentPage() < $units->lastPage(),
            ],
        ]);
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new IccModel(), 'inspeksi.icc.index');
    }
}
