<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\InventoriTvModel;
use App\Models\inspeksi\MonitorModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use App\Services\InspectionPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class MonitorController extends Controller
{
    use HandlesInspectionPhoto;

    private const MODULE = 'monitor';

    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi)
    {
    }

    public function index(Request $request)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $assetQuery = InventoriTvModel::query();

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

        $inspectionsThisPeriod = MonitorModel::whereIn('nomor_aset', $assets->pluck('no_aset'))
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->get()
            ->keyBy('nomor_aset');

        $assets->each(function ($asset) use ($inspectionsThisPeriod) {
            $asset->currentInspection = $inspectionsThisPeriod->get($asset->no_aset);
        });

        $totalAset = InventoriTvModel::count();
        $totalSudah = MonitorModel::whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->whereIn('nomor_aset', InventoriTvModel::pluck('no_aset'))
            ->distinct()
            ->count('nomor_aset');

        return view('Inspeksi.monitor.index', [
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

        $availableYears = MonitorModel::query()
            ->selectRaw('DISTINCT YEAR(tanggal_inspeksi) as year')
            ->whereNotNull('tanggal_inspeksi')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty() || !$availableYears->contains($year)) {
            $availableYears = $availableYears->push($year)->unique()->sortDesc()->values();
        }

        return view('Inspeksi.monitor.riwayat', [
            'inspections' => $inspections,
            'month' => $month,
            'year' => $year,
            'availableYears' => $availableYears,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create(InventoriTvModel $asset)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $existing = MonitorModel::where('nomor_aset', $asset->no_aset)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->first();

        if ($existing) {
            return redirect()->route('inspeksi.monitor.edit', $existing)
                ->with('success', 'Aset ini sudah diinspeksi pada periode berjalan.');
        }

        return view('Inspeksi.monitor.create', [
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
        ]), 'Inspeksi-Monitor.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            ['asset_id' => ['required', 'exists:inventori_tv,id']],
            $this->checklistValidationRules()
        ));

        $asset = InventoriTvModel::findOrFail($validated['asset_id']);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);
        $alreadyInspected = MonitorModel::where('nomor_aset', $asset->no_aset)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('inspeksi.monitor.index')
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

        $monitor = MonitorModel::create($data);
        $this->storeInspectionPhoto($request, $monitor);

        return redirect()->route('inspeksi.monitor.index')->with('success', 'Data inspeksi monitor/TV berhasil disimpan.');
    }

    public function edit(MonitorModel $monitor)
    {
        return view('Inspeksi.monitor.edit', [
            'monitor' => $monitor,
            'deviceInfo' => $this->deviceInfoFromInspection($monitor),
        ]);
    }

    public function update(Request $request, MonitorModel $monitor)
    {
        if (!$this->approvalService->canUpdate($monitor)) {
            return redirect()->route('inspeksi.monitor.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $request->validate($this->checklistValidationRules());
        $data['inspektor'] = $monitor->inspektor ?: $request->user()->nrp;
        $monitor->update($data);
        $this->storeInspectionPhoto($request, $monitor);

        return redirect()->route('inspeksi.monitor.index')->with('success', 'Data inspeksi monitor/TV berhasil diperbarui.');
    }

    public function destroy(MonitorModel $monitor)
    {
        $monitor->delete();

        return redirect()->route('inspeksi.monitor.index')->with('success', 'Data inspeksi monitor/TV berhasil dihapus.');
    }

    public function pdf(MonitorModel $monitor)
    {
        return Pdf::loadView('pdf.inspeksi_monitor', compact('monitor'))
            ->setPaper('A4', 'portrait')
            ->stream("Checklist-Inspeksi-Monitor-{$monitor->nomor_aset}.pdf");
    }

    public function approve(Request $request, MonitorModel $monitor)
    {
        $result = $this->approvalService->approve($request, $monitor);

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

        return redirect()->route('inspeksi.monitor.index', $request->only('search'))
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

        $temporaryFile = tempnam(sys_get_temp_dir(), 'monitor-approved-');
        $zipPath = $temporaryFile . '.zip';
        @unlink($temporaryFile);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat arsip PDF.');
        }

        foreach ($inspections as $inspection) {
            $pdf = Pdf::loadView('pdf.inspeksi_monitor', ['monitor' => $inspection])
                ->setPaper('A4', 'portrait')
                ->output();
            $zip->addFromString("Checklist-Monitor-{$inspection->nomor_aset}-{$inspection->id}.pdf", $pdf);
        }
        $zip->close();

        return response()->download($zipPath, 'Inspeksi-Monitor-Approved.zip')->deleteFileAfterSend(true);
    }

    private function filteredQuery(Request $request)
    {
        return MonitorModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['nomor_aset', 'merek', 'type', 'sn', 'departemen'];
            $query->search($cols, $request->search);
        });
    }

    private function checklistValidationRules(): array
    {
        return array_merge([
            'tanggal_inspeksi' => 'required|date',
            'keterangan' => 'nullable|string',
            'tampilan_layer' => 'nullable|string|max:255',
            'tindakan_tampilan_layer' => 'nullable|string',
            'kabel_power' => 'nullable|string|max:255',
            'tindakan_kabel_power' => 'nullable|string',
            'bracket_dudukan' => 'nullable|string|max:255',
            'tindakan_bracket_dudukan' => 'nullable|string',
            'kebersihan' => 'nullable|string|max:255',
            'tindakan_kebersihan' => 'nullable|string',
            'stop_kontak' => 'nullable|string|max:255',
            'tindakan_stop_kontak' => 'nullable|string',
        ], $this->photoValidationRules());
    }

    private function deviceInfoFromAsset(InventoriTvModel $asset): array
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

    private function deviceInfoFromInspection(MonitorModel $monitor): array
    {
        return [
            'nomor_aset' => $monitor->nomor_aset,
            'merek' => $monitor->merek,
            'tipe' => $monitor->type,
            'serial_number' => $monitor->sn,
            'departemen' => $monitor->departemen,
            'lokasi' => $monitor->lokasi,
        ];
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new MonitorModel(), 'inspeksi.monitor.index');
    }
}
