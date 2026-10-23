<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\InventoriUpsModel;
use App\Models\inspeksi\UpsModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use App\Services\InspectionPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class UpsController extends Controller
{
    use HandlesInspectionPhoto;

    private const MODULE = 'ups';

    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi)
    {
    }

    public function index(Request $request)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $assetQuery = InventoriUpsModel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $assetQuery->where(function ($q) use ($search) {
                $q->where('no_aset', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $assets = $assetQuery->orderBy('no_aset')->paginate(15)->withQueryString();

        $inspectionsThisPeriod = UpsModel::whereIn('nomor_aset', $assets->pluck('no_aset'))
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->get()
            ->keyBy('nomor_aset');

        $assets->each(function ($asset) use ($inspectionsThisPeriod) {
            $asset->currentInspection = $inspectionsThisPeriod->get($asset->no_aset);
        });

        $totalAset = InventoriUpsModel::count();
        $totalSudah = UpsModel::whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->whereIn('nomor_aset', InventoriUpsModel::pluck('no_aset'))
            ->distinct()
            ->count('nomor_aset');

        return view('Inspeksi.ups.index', [
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

        $availableYears = UpsModel::query()
            ->selectRaw('DISTINCT YEAR(tanggal_inspeksi) as year')
            ->whereNotNull('tanggal_inspeksi')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty() || !$availableYears->contains($year)) {
            $availableYears = $availableYears->push($year)->unique()->sortDesc()->values();
        }

        return view('Inspeksi.ups.riwayat', [
            'inspections' => $inspections,
            'month' => $month,
            'year' => $year,
            'availableYears' => $availableYears,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create(InventoriUpsModel $asset)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $existing = UpsModel::where('nomor_aset', $asset->no_aset)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->first();

        if ($existing) {
            return redirect()->route('inspeksi.ups.edit', $existing)
                ->with('success', 'Aset ini sudah diinspeksi pada periode berjalan.');
        }

        return view('Inspeksi.ups.create', [
            'asset' => $asset,
            'periodLabel' => InspectionPeriod::label(self::MODULE),
            'deviceInfo' => $this->deviceInfoFromAsset($asset),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new InspectionExport($this->filteredQuery($request)->latest()->get(), [
            'Nomor Aset' => 'nomor_aset', 'Merek' => 'merek', 'Tipe' => 'type', 'SN' => 'sn',
            'Departemen' => 'departemen', 'Lokasi' => 'lokasi', 'Tanggal Inspeksi' => 'tanggal_inspeksi',
            'Status' => 'approval_status', 'Disetujui Oleh' => 'approved_by',
        ]), 'Inspeksi-UPS.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            ['asset_id' => ['required', 'exists:inventori_ups,id']],
            $this->checklistValidationRules()
        ));

        $asset = InventoriUpsModel::findOrFail($validated['asset_id']);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);
        $alreadyInspected = UpsModel::where('nomor_aset', $asset->no_aset)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('inspeksi.ups.index')
                ->with('error', 'Aset ini sudah diinspeksi pada periode berjalan.');
        }

        $data = $validated;
        unset($data['asset_id']);
        $data['nomor_aset'] = $asset->no_aset;
        $data['merek'] = $asset->merek;
        $data['type'] = $asset->tipe;
        $data['sn'] = $asset->serial_number;
        $data['departemen'] = $asset->department;
        $data['lokasi'] = $asset->lokasi;
        $data['inspektor'] = $request->user()->nrp;

        $ups = UpsModel::create($data);
        $this->storeInspectionPhoto($request, $ups);

        return redirect()->route('inspeksi.ups.index')
            ->with('success', 'Data inspeksi UPS berhasil disimpan.');
    }

    public function edit(UpsModel $ups)
    {
        return view('Inspeksi.ups.edit', [
            'ups' => $ups,
            'deviceInfo' => $this->deviceInfoFromInspection($ups),
        ]);
    }

    public function update(Request $request, UpsModel $ups)
    {
        if (!$this->approvalService->canUpdate($ups)) {
            return redirect()->route('inspeksi.ups.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $request->validate($this->checklistValidationRules());
        $data['inspektor'] = $ups->inspektor ?: $request->user()->nrp;

        $ups->update($data);
        $this->storeInspectionPhoto($request, $ups);

        return redirect()->route('inspeksi.ups.index')
            ->with('success', 'Data inspeksi UPS berhasil diperbarui.');
    }

    public function destroy(UpsModel $ups)
    {
        $ups->delete();

        return redirect()->route('inspeksi.ups.index')
            ->with('success', 'Data inspeksi UPS berhasil dihapus.');
    }

    public function pdf(UpsModel $ups)
    {
        return Pdf::loadView('pdf.inspeksi_ups', compact('ups'))
            ->setPaper('A4', 'portrait')
            ->stream("Checklist-Inspeksi-UPS-{$ups->nomor_aset}.pdf");
    }

    public function approve(Request $request, UpsModel $ups)
    {
        $result = $this->approvalService->approve($request, $ups);

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

        return redirect()->route('inspeksi.ups.index', $request->only('search'))
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

        $temporaryFile = tempnam(sys_get_temp_dir(), 'ups-approved-');
        $zipPath = $temporaryFile . '.zip';
        @unlink($temporaryFile);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat arsip PDF.');
        }

        foreach ($inspections as $inspection) {
            $pdf = Pdf::loadView('pdf.inspeksi_ups', ['ups' => $inspection])
                ->setPaper('A4', 'portrait')
                ->output();
            $zip->addFromString("Checklist-UPS-{$inspection->nomor_aset}-{$inspection->id}.pdf", $pdf);
        }
        $zip->close();

        return response()->download($zipPath, 'Inspeksi-UPS-Approved.zip')->deleteFileAfterSend(true);
    }

    private function filteredQuery(Request $request)
    {
        return UpsModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['nomor_aset', 'merek', 'type', 'sn', 'departemen'];
            $query->search($cols, $request->search);
        });
    }

    private function checklistValidationRules(): array
    {
        return array_merge([
            'tanggal_inspeksi' => 'required|date',
            'keterangan' => 'nullable|string',
            'casing' => 'nullable|string|max:255',
            'tindakan_casing' => 'nullable|string',
            'kebersihan' => 'nullable|string|max:255',
            'tindakan_kebersihan' => 'nullable|string',
            'kabel_adaptor' => 'nullable|string|max:255',
            'tindakan_kabel_adaptor' => 'nullable|string',
            'tombol_switch' => 'nullable|string|max:255',
            'tindakan_tombol_switch' => 'nullable|string',
            'indikator_status' => 'nullable|string|max:255',
            'tindakan_indikator_status' => 'nullable|string',
            'fungsi_alarm' => 'nullable|string|max:255',
            'tindakan_fungsi_alarm' => 'nullable|string',
            'respon_kehilangan_daya' => 'nullable|string|max:255',
            'tindakan_respon_kehilangan_daya' => 'nullable|string',
            'fuse' => 'nullable|string|max:255',
            'tindakan_fuse' => 'nullable|string',
        ], $this->photoValidationRules());
    }

    private function deviceInfoFromAsset(InventoriUpsModel $asset): array
    {
        return [
            'nomor_aset' => $asset->no_aset,
            'merek' => $asset->merek,
            'tipe' => $asset->tipe,
            'serial_number' => $asset->serial_number,
            'departemen' => $asset->department,
            'lokasi' => $asset->lokasi,
        ];
    }

    private function deviceInfoFromInspection(UpsModel $ups): array
    {
        return [
            'nomor_aset' => $ups->nomor_aset,
            'merek' => $ups->merek,
            'tipe' => $ups->type,
            'serial_number' => $ups->sn,
            'departemen' => $ups->departemen,
            'lokasi' => $ups->lokasi,
        ];
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new UpsModel(), 'inspeksi.ups.index');
    }
}
