<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\InventoriProyektorModel;
use App\Models\inspeksi\ProyektorModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use App\Services\InspectionPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ProyektorController extends Controller
{
    use HandlesInspectionPhoto;

    private const MODULE = 'proyektor';

    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi)
    {
    }

    public function index(Request $request)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $assetQuery = InventoriProyektorModel::query();

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

        $inspectionsThisPeriod = ProyektorModel::whereIn('nomor_aset', $assets->pluck('no_aset'))
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->get()
            ->keyBy('nomor_aset');

        $assets->each(function ($asset) use ($inspectionsThisPeriod) {
            $asset->currentInspection = $inspectionsThisPeriod->get($asset->no_aset);
        });

        $totalAset = InventoriProyektorModel::count();
        $totalSudah = ProyektorModel::whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->whereIn('nomor_aset', InventoriProyektorModel::pluck('no_aset'))
            ->distinct()
            ->count('nomor_aset');

        return view('Inspeksi.proyektor.index', [
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

        $availableYears = ProyektorModel::query()
            ->selectRaw('DISTINCT YEAR(tanggal_inspeksi) as year')
            ->whereNotNull('tanggal_inspeksi')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty() || !$availableYears->contains($year)) {
            $availableYears = $availableYears->push($year)->unique()->sortDesc()->values();
        }

        return view('Inspeksi.proyektor.riwayat', [
            'inspections' => $inspections,
            'month' => $month,
            'year' => $year,
            'availableYears' => $availableYears,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create(InventoriProyektorModel $asset)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $existing = ProyektorModel::where('nomor_aset', $asset->no_aset)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->first();

        if ($existing) {
            return redirect()->route('inspeksi.proyektor.edit', $existing)
                ->with('success', 'Aset ini sudah diinspeksi pada periode berjalan.');
        }

        return view('Inspeksi.proyektor.create', [
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
        ]), 'Inspeksi-Proyektor.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            ['asset_id' => ['required', 'exists:inventori_proyektor,id']],
            $this->checklistValidationRules()
        ));

        $asset = InventoriProyektorModel::findOrFail($validated['asset_id']);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);
        $alreadyInspected = ProyektorModel::where('nomor_aset', $asset->no_aset)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('inspeksi.proyektor.index')
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

        $proyektor = ProyektorModel::create($data);
        $this->storeInspectionPhoto($request, $proyektor);

        return redirect()->route('inspeksi.proyektor.index')->with('success', 'Data inspeksi proyektor berhasil disimpan.');
    }

    public function edit(ProyektorModel $proyektor)
    {
        return view('Inspeksi.proyektor.edit', [
            'proyektor' => $proyektor,
            'deviceInfo' => $this->deviceInfoFromInspection($proyektor),
        ]);
    }

    public function update(Request $request, ProyektorModel $proyektor)
    {
        if (!$this->approvalService->canUpdate($proyektor)) {
            return redirect()->route('inspeksi.proyektor.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $request->validate($this->checklistValidationRules());
        $data['inspektor'] = $proyektor->inspektor ?: $request->user()->nrp;
        $proyektor->update($data);
        $this->storeInspectionPhoto($request, $proyektor);

        return redirect()->route('inspeksi.proyektor.index')->with('success', 'Data inspeksi proyektor berhasil diperbarui.');
    }

    public function destroy(ProyektorModel $proyektor)
    {
        $proyektor->delete();

        return redirect()->route('inspeksi.proyektor.index')->with('success', 'Data inspeksi proyektor berhasil dihapus.');
    }

    public function pdf(ProyektorModel $proyektor)
    {
        return Pdf::loadView('pdf.inspeksi_proyektor', compact('proyektor'))
            ->setPaper('A4', 'portrait')
            ->stream("Checklist-Inspeksi-Proyektor-{$proyektor->nomor_aset}.pdf");
    }

    public function approve(Request $request, ProyektorModel $proyektor)
    {
        $result = $this->approvalService->approve($request, $proyektor);

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

        return redirect()->route('inspeksi.proyektor.index', $request->only('search'))
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

        $temporaryFile = tempnam(sys_get_temp_dir(), 'proyektor-approved-');
        $zipPath = $temporaryFile . '.zip';
        @unlink($temporaryFile);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat arsip PDF.');
        }

        foreach ($inspections as $inspection) {
            $pdf = Pdf::loadView('pdf.inspeksi_proyektor', ['proyektor' => $inspection])
                ->setPaper('A4', 'portrait')
                ->output();
            $zip->addFromString("Checklist-Proyektor-{$inspection->nomor_aset}-{$inspection->id}.pdf", $pdf);
        }
        $zip->close();

        return response()->download($zipPath, 'Inspeksi-Proyektor-Approved.zip')->deleteFileAfterSend(true);
    }

    private function filteredQuery(Request $request)
    {
        return ProyektorModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['nomor_aset', 'merek', 'type', 'sn', 'departemen'];
            $query->search($cols, $request->search);
        });
    }

    private function checklistValidationRules(): array
    {
        return array_merge([
            'tanggal_inspeksi' => 'required|date',
            'keterangan' => 'nullable|string',
            'kondisi_casing' => 'nullable|string|max:255',
            'tindakan_kondisi_casing' => 'nullable|string',
            'kebersihan' => 'nullable|string|max:255',
            'tindakan_kebersihan' => 'nullable|string',
            'kabel_adaptor' => 'nullable|string|max:255',
            'tindakan_kabel_adaptor' => 'nullable|string',
            'lensa_proyektor' => 'nullable|string|max:255',
            'tindakan_lensa_proyektor' => 'nullable|string',
            'indikator_lampu' => 'nullable|string|max:255',
            'tindakan_indikator_lampu' => 'nullable|string',
            'fokus_zoom' => 'nullable|string|max:255',
            'tindakan_fokus_zoom' => 'nullable|string',
            'kecerahan_kontras' => 'nullable|string|max:255',
            'tindakan_kecerahan_kontras' => 'nullable|string',
            'koneksi_input_hdmi' => 'nullable|string|max:255',
            'koneksi_input_vga' => 'nullable|string|max:255',
            'koneksi_input_usb' => 'nullable|string|max:255',
        ], $this->photoValidationRules());
    }

    private function deviceInfoFromAsset(InventoriProyektorModel $asset): array
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

    private function deviceInfoFromInspection(ProyektorModel $proyektor): array
    {
        return [
            'nomor_aset' => $proyektor->nomor_aset,
            'merek' => $proyektor->merek,
            'tipe' => $proyektor->type,
            'serial_number' => $proyektor->sn,
            'departemen' => $proyektor->departemen,
            'lokasi' => $proyektor->lokasi,
        ];
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new ProyektorModel(), 'inspeksi.proyektor.index');
    }
}
