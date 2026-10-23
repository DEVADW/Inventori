<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\UnitOperasiModel;
use App\Models\inspeksi\Ss6Model;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use App\Services\InspectionPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class Ss6Controller extends Controller
{
    use HandlesInspectionPhoto;

    private const MODULE = 'ss6';

    private const KONDISI_FIELDS = [
        'kondisi_monitor',
        'kondisi_bracket',
        'kondisi_car_charger',
        'kondisi_kabel_power',
        'kondisi_app_lock',
        'software_ppa_teams',
        'kondisi_baterai',
    ];

    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi) {}

    public function index(Request $req)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $assetQuery = UnitOperasiModel::where('ss6', true);

        if ($req->filled('search')) {
            $search = $req->search;
            $assetQuery->where(function ($q) use ($search) {
                $q->where('code_unit', 'like', "%{$search}%")
                    ->orWhere('no_aset_tab', 'like', "%{$search}%")
                    ->orWhere('serial_number_tab', 'like', "%{$search}%");
            });
        }

        $assets = $assetQuery->orderBy('code_unit')->paginate(15)->withQueryString();

        $inspectionsThisPeriod = Ss6Model::whereIn('no_lambung', $assets->pluck('code_unit'))
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->get()
            ->keyBy('no_lambung');

        $assets->each(function ($asset) use ($inspectionsThisPeriod) {
            $asset->currentInspection = $inspectionsThisPeriod->get($asset->code_unit);
        });

        $totalAset = UnitOperasiModel::where('ss6', true)->count();
        $totalSudah = Ss6Model::whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->whereIn('no_lambung', UnitOperasiModel::where('ss6', true)->pluck('code_unit'))
            ->distinct()
            ->count('no_lambung');

        return view('Inspeksi.ss6.index', [
            'assets' => $assets,
            'periodLabel' => InspectionPeriod::label(self::MODULE),
            'totalAset' => $totalAset,
            'totalSudah' => $totalSudah,
            'totalBelum' => max(0, $totalAset - $totalSudah),
            'isGroupLeader' => $req->user()->jabatan === 'GL',
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

        $availableYears = Ss6Model::query()
            ->selectRaw('DISTINCT YEAR(tanggal_inspeksi) as year')
            ->whereNotNull('tanggal_inspeksi')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty() || !$availableYears->contains($year)) {
            $availableYears = $availableYears->push($year)->unique()->sortDesc()->values();
        }

        return view('Inspeksi.ss6.riwayat', [
            'inspections' => $inspections,
            'month' => $month,
            'year' => $year,
            'availableYears' => $availableYears,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create(UnitOperasiModel $asset)
    {
        abort_unless($asset->ss6, 404);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $existing = Ss6Model::where('no_lambung', $asset->code_unit)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->first();

        if ($existing) {
            return redirect()->route('inspeksi.ss6.edit', $existing)
                ->with('success', 'Unit ini sudah diinspeksi pada periode berjalan.');
        }

        return view('Inspeksi.ss6.create', [
            'asset' => $asset,
            'periodLabel' => InspectionPeriod::label(self::MODULE),
            'deviceInfo' => [
                'no_lambung' => $asset->code_unit,
                'no_asset' => $asset->no_aset_tab,
                'serial_number' => $asset->serial_number_tab,
            ],
        ]);
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
        $validated = $req->validate(array_merge(
            ['asset_id' => ['required', 'exists:unit_operasi,id', $this->ss6AssetRule()]],
            $this->checklistValidationRules()
        ));

        $asset = UnitOperasiModel::findOrFail($validated['asset_id']);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);
        $alreadyInspected = Ss6Model::where('no_lambung', $asset->code_unit)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('inspeksi.ss6.index')
                ->with('error', 'Unit ini sudah diinspeksi pada periode berjalan.');
        }

        $data = $this->packKondisiFields($validated);
        unset($data['asset_id']);
        $data['no_lambung'] = $asset->code_unit;
        $data['no_asset'] = $asset->no_aset_tab;
        $data['serial_number'] = $asset->serial_number_tab;
        $data['diinspeksi_oleh'] = $req->user()->nrp;

        $inspeksi = Ss6Model::create($data);
        $this->storeInspectionPhoto($req, $inspeksi);

        return redirect()
            ->route('inspeksi.ss6.index')
            ->with('success', 'Data inspeksi berhasil disimpan');
    }

    public function edit(Ss6Model $inspeksi)
    {
        return view('Inspeksi.ss6.edit', [
            'inspeksi' => $inspeksi,
            'deviceInfo' => [
                'no_lambung' => $inspeksi->no_lambung,
                'no_asset' => $inspeksi->no_asset,
                'serial_number' => $inspeksi->serial_number,
            ],
        ]);
    }

    public function update(Request $req, Ss6Model $inspeksi)
    {
        if (!$this->approvalService->canUpdate($inspeksi)) {
            return redirect()->route('inspeksi.ss6.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $validated = $req->validate($this->checklistValidationRules());
        $data = $this->packKondisiFields($validated);
        $data['diinspeksi_oleh'] = $inspeksi->diinspeksi_oleh ?: $req->user()->nrp;

        if ($req->user()->jabatan === 'GL') {
            $data['diperiksa_oleh'] = $req->user()->nama;
        }

        $inspeksi->update($data);
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

    private function ss6AssetRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if ($value && !UnitOperasiModel::where('id', $value)->where('ss6', true)->exists()) {
                $fail('Unit ini tidak terdaftar sebagai TAB SS6.');
            }
        };
    }

    private function checklistValidationRules(): array
    {
        $rules = [
            'tanggal_inspeksi' => 'required|date',
            'output_powercharge' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ];

        foreach (self::KONDISI_FIELDS as $field) {
            $rules[$field . '_ketersediaan'] = 'required|string';
            $rules[$field . '_kondisi'] = 'required|string';
        }

        return array_merge($rules, $this->photoValidationRules());
    }

    private function packKondisiFields(array $validated): array
    {
        foreach (self::KONDISI_FIELDS as $field) {
            $validated[$field] = json_encode([
                'ketersediaan' => $validated[$field . '_ketersediaan'],
                'kondisi' => $validated[$field . '_kondisi'],
            ]);

            unset($validated[$field . '_ketersediaan']);
            unset($validated[$field . '_kondisi']);
        }

        return $validated;
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new Ss6Model(), 'inspeksi.ss6.index');
    }
}
