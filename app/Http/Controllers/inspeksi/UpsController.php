<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\inspeksi\UpsModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class UpsController extends Controller
{
    use HandlesInspectionPhoto;
    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi)
    {
    }

    public function index(Request $request)
    {
        $ups = $this->filteredQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view('Inspeksi.ups.index', [
            'ups' => $ups,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create()
    {
        return view('Inspeksi.ups.create');
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
        $data = $this->validateRequest($request);
        $data['inspektor'] = $request->user()->nrp;

        $ups = UpsModel::create($data);
        $this->storeInspectionPhoto($request, $ups);

        return redirect()->route('inspeksi.ups.index')
            ->with('success', 'Data inspeksi UPS berhasil disimpan.');
    }

    public function edit(UpsModel $ups)
    {
        return view('Inspeksi.ups.edit', compact('ups'));
    }

    public function update(Request $request, UpsModel $ups)
    {
        if (!$this->approvalService->canUpdate($ups)) {
            return redirect()->route('inspeksi.ups.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $this->validateRequest($request);
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

    protected function validateRequest(Request $request): array
    {
        return $request->validate(array_merge([
            'nomor_aset' => 'nullable|string|max:255',
            'merek' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'sn' => 'nullable|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_inspeksi' => 'nullable|date',
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
            'inspektor' => 'nullable|string|max:255',
            'diketahui_oleh' => 'nullable|string|max:255',
        ], $this->photoValidationRules()));
    }

    public function clone(Request $request) {
      

        return $this->cloneInspeksi->cloneInpeksi($request, new UpsModel(), 'inspeksi.ups.index');
    }
}
