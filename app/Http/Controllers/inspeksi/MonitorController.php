<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\inspeksi\MonitorModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class MonitorController extends Controller
{
    use HandlesInspectionPhoto;
    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi)
    {
    }

    public function index(Request $request)
    {
        $monitors = $this->filteredQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Inspeksi.monitor.index', [
            'monitors' => $monitors,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create()
    {
        return view('Inspeksi.monitor.create');
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
        $data = $this->validateRequest($request);
        $data['inspektor'] = $request->user()->nrp;
        $monitor = MonitorModel::create($data);
        $this->storeInspectionPhoto($request, $monitor);

        return redirect()->route('Inspeksi.monitor.index')->with('success', 'Data inspeksi monitor/TV berhasil disimpan.');
    }

    public function edit(MonitorModel $monitor)
    {
        return view('Inspeksi.monitor.edit', compact('monitor'));
    }

    public function update(Request $request, MonitorModel $monitor)
    {
        if (!$this->approvalService->canUpdate($monitor)) {
            return redirect()->route('inspeksi.monitor.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $this->validateRequest($request);
        $data['inspektor'] = $monitor->inspektor ?: $request->user()->nrp;
        $monitor->update($data);
        $this->storeInspectionPhoto($request, $monitor);

        return redirect()->route('Inspeksi.monitor.index')->with('success', 'Data inspeksi monitor/TV berhasil diperbarui.');
    }

    public function destroy(MonitorModel $monitor)
    {
        $monitor->delete();

        return redirect()->route('Inspeksi.monitor.index')->with('success', 'Data inspeksi monitor/TV berhasil dihapus.');
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

        return redirect()->route('Inspeksi.monitor.index', $request->only('search'))
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

    private function validateRequest(Request $request): array
    {
        return $request->validate(array_merge([
            'nomor_aset' => 'nullable|string|max:255', 'merek' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255', 'sn' => 'nullable|string|max:255',
            'departemen' => 'nullable|string|max:255', 'lokasi' => 'nullable|string|max:255',
            'tanggal_inspeksi' => 'nullable|date', 'keterangan' => 'nullable|string',
            'tampilan_layer' => 'nullable|string|max:255', 'kabel_power' => 'nullable|string|max:255',
            'bracket_dudukan' => 'nullable|string|max:255', 'kebersihan' => 'nullable|string|max:255',
            'stop_kontak' => 'nullable|string|max:255', 'tindakan_tampilan_layer' => 'nullable|string',
            'tindakan_kabel_power' => 'nullable|string', 'tindakan_bracket_dudukan' => 'nullable|string',
            'tindakan_kebersihan' => 'nullable|string', 'tindakan_stop_kontak' => 'nullable|string',
            'inspektor' => 'nullable|string|max:255', 'jabatan_inspektor' => 'nullable|string|max:255',
            'diketahui_oleh' => 'nullable|string|max:255',
        ], $this->photoValidationRules()));
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new MonitorModel(), 'Inspeksi.monitor.index');
    }
}
