<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\UnitOperasiModel;
use App\Models\inspeksi\OfaModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use App\Services\InspectionPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class OfaController extends Controller
{
    use HandlesInspectionPhoto;

    private const MODULE = 'ofa';

    public function __construct(protected ApprovalService $approvalService, protected CloneInspeksi $cloneInspeksi) {}

    public const CHECKLIST_SECTIONS = [
        'A. MAIN MODUL' => [
            'Letak modul sesuai lokasi yang disepakati',
            'Bracket / baut pengunci',
            'Led indicator (merah, kuning)',
            'Port WLAN',
            'Konektor input',
            'Cooling perangkat',
            'Box / cover main modul',
        ],
        'B. LAYAR DISPLAY' => [
            'Pemasangan sesuai lokasi yang disepakati',
            'Bracket / baut pengunci',
            'Kondisi layar',
            'Kabel charger / input power',
            'Input power DC in',
        ],
        'C. OTHER' => [
            'Sambungan kabel',
            'Kabel komunikasi data (PLM/TELEMETRY)',
            'Antena LTE & GPS',
            'Bracket & baut antenna',
            'Push to MQTT server',
            'Koneksi WiFi konfigurasi',
            'Kabel terconduit / terproteksi',
        ],
    ];

    public function index(Request $request)
    {
        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $assetQuery = UnitOperasiModel::where('ofa', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $assetQuery->where(function ($q) use ($search) {
                $q->where('code_unit', 'like', "%{$search}%")
                    ->orWhere('type_unit', 'like', "%{$search}%")
                    ->orWhere('serial_number_module', 'like', "%{$search}%");
            });
        }

        $assets = $assetQuery->orderBy('code_unit')->paginate(15)->withQueryString();

        $inspectionsThisPeriod = OfaModel::whereIn('code_number_unit', $assets->pluck('code_unit'))
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->get()
            ->keyBy('code_number_unit');

        $assets->each(function ($asset) use ($inspectionsThisPeriod) {
            $asset->currentInspection = $inspectionsThisPeriod->get($asset->code_unit);
        });

        $totalAset = UnitOperasiModel::where('ofa', true)->count();
        $totalSudah = OfaModel::whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->whereIn('code_number_unit', UnitOperasiModel::where('ofa', true)->pluck('code_unit'))
            ->distinct()
            ->count('code_number_unit');

        return view('Inspeksi.ofa.index', [
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

        $availableYears = OfaModel::query()
            ->selectRaw('DISTINCT YEAR(tanggal_inspeksi) as year')
            ->whereNotNull('tanggal_inspeksi')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty() || !$availableYears->contains($year)) {
            $availableYears = $availableYears->push($year)->unique()->sortDesc()->values();
        }

        return view('Inspeksi.ofa.riwayat', [
            'inspections' => $inspections,
            'month' => $month,
            'year' => $year,
            'availableYears' => $availableYears,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create(UnitOperasiModel $asset)
    {
        abort_unless($asset->ofa, 404);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);

        $existing = OfaModel::where('code_number_unit', $asset->code_unit)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->latest('tanggal_inspeksi')
            ->first();

        if ($existing) {
            return redirect()->route('inspeksi.ofa.edit', $existing)
                ->with('success', 'Unit ini sudah diinspeksi pada periode berjalan.');
        }

        return view('Inspeksi.ofa.create', array_merge($this->formData(), [
            'asset' => $asset,
            'periodLabel' => InspectionPeriod::label(self::MODULE),
            'deviceInfo' => [
                'code_number_unit' => $asset->code_unit,
                'type_unit' => $asset->type_unit,
                'serial_number_modul' => $asset->serial_number_module,
            ],
        ]));
    }

    public function export(Request $request)
    {
        return Excel::download(new InspectionExport($this->filteredQuery($request)->latest()->get(), [
            'Project' => 'project_name',
            'Code Number Unit' => 'code_number_unit',
            'Tipe Unit' => 'type_unit',
            'Serial Number Modul' => 'serial_number_modul',
            'Tanggal Inspeksi' => 'tanggal_inspeksi',
            'Tim Pelaksana' => 'team_members',
            'Status' => 'approval_status',
            'Disetujui Oleh' => 'approved_by',
        ]), 'Inspeksi-OFA.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            ['asset_id' => ['required', 'exists:unit_operasi,id', $this->ofaAssetRule()]],
            $this->checklistValidationRules()
        ));

        $asset = UnitOperasiModel::findOrFail($validated['asset_id']);

        [$start, $end] = InspectionPeriod::currentRange(self::MODULE);
        $alreadyInspected = OfaModel::where('code_number_unit', $asset->code_unit)
            ->whereBetween('tanggal_inspeksi', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('inspeksi.ofa.index')
                ->with('error', 'Unit ini sudah diinspeksi pada periode berjalan.');
        }

        $data = $validated;
        unset($data['asset_id']);
        $data['code_number_unit'] = $asset->code_unit;
        $data['type_unit'] = $asset->type_unit;
        $data['serial_number_modul'] = $asset->serial_number_module;
        $data['item_pemeriksaan'] = collect($data['item_pemeriksaan'])->values()->all();
        $data['tim_pelaksana'] = collect($data['tim_pelaksana'])->values()->all();
        $data['diinspeksi_oleh'] = $request->user()->nrp;

        $ofa = OfaModel::create($data);
        $this->storeInspectionPhoto($request, $ofa);

        return redirect()
            ->route('inspeksi.ofa.index')
            ->with('success', 'Data inspeksi OFA berhasil disimpan.');
    }

    public function edit(OfaModel $ofa)
    {
        return view('Inspeksi.ofa.edit', array_merge($this->formData(), [
            'ofa' => $ofa,
            'deviceInfo' => [
                'code_number_unit' => $ofa->code_number_unit,
                'type_unit' => $ofa->type_unit,
                'serial_number_modul' => $ofa->serial_number_modul,
            ],
        ]));
    }

    public function update(Request $request, OfaModel $ofa)
    {
        if (!$this->approvalService->canUpdate($ofa)) {
            return redirect()
                ->route('inspeksi.ofa.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $request->validate($this->checklistValidationRules());
        $data['item_pemeriksaan'] = collect($data['item_pemeriksaan'])->values()->all();
        $data['tim_pelaksana'] = collect($data['tim_pelaksana'])->values()->all();

        $ofa->update($data);
        $this->storeInspectionPhoto($request, $ofa);

        return redirect()
            ->route('inspeksi.ofa.index')
            ->with('success', 'Data inspeksi OFA berhasil diperbarui.');
    }

    public function destroy(OfaModel $ofa)
    {
        $ofa->delete();

        return redirect()->route('inspeksi.ofa.index')->with('success', 'Data inspeksi OFA berhasil dihapus.');
    }

    public function pdf(OfaModel $ofa)
    {
        return Pdf::loadView('pdf.inspeksi_ofa', compact('ofa'))
            ->setPaper('A4', 'portrait')
            ->stream("Checklist-Inspeksi-OFA-{$ofa->code_number_unit}.pdf");
    }

    public function approve(Request $request, OfaModel $ofa)
    {
        $result = $this->approvalService->approve($request, $ofa);

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

        return redirect()->route('inspeksi.ofa.index', $request->only('search'))
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

        $inspections = $this->filteredQuery($request)
            ->whereNotNull('approved_at')
            ->whereYear('tanggal_inspeksi', $year)
            ->whereMonth('tanggal_inspeksi', $month)
            ->latest()
            ->get();
        if ($inspections->isEmpty()) {
            return back()->with('error', "Belum ada inspeksi OFA approved untuk {$month}/{$year}.");
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'ofa-approved-');
        $zipPath = $temporaryFile . '.zip';
        @unlink($temporaryFile);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat arsip PDF.');
        }

        foreach ($inspections as $inspection) {
            $pdf = Pdf::loadView('pdf.inspeksi_ofa', ['ofa' => $inspection])
                ->setPaper('A4', 'portrait')
                ->output();
            $zip->addFromString("Checklist-OFA-{$inspection->code_number_unit}-{$inspection->id}.pdf", $pdf);
        }
        $zip->close();

        return response()->download($zipPath, "Inspeksi-OFA-Approved-{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . '.zip')
            ->deleteFileAfterSend(true);
    }

    private function ofaAssetRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if ($value && !UnitOperasiModel::where('id', $value)->where('ofa', true)->exists()) {
                $fail('Unit ini tidak terdaftar sebagai OFA.');
            }
        };
    }

    private function checklistValidationRules(): array
    {
        return array_merge([
            'project_name' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:100'],
            'divisi_department' => ['nullable', 'string', 'max:255'],
            'jobsite' => ['required', 'string', 'max:255'],
            'tanggal_inspeksi' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'catatan_tambahan' => ['nullable', 'string'],
            'item_pemeriksaan' => ['required', 'array', 'min:1'],
            'item_pemeriksaan.*.nama' => ['required', 'string', 'max:255'],
            'item_pemeriksaan.*.section' => ['required', 'string', 'max:100'],
            'item_pemeriksaan.*.status' => ['required', 'in:baik,rusak,na'],
            'item_pemeriksaan.*.keterangan' => ['nullable', 'string', 'max:1000'],
            'tim_pelaksana' => ['required', 'array', 'min:1'],
            'tim_pelaksana.*.nama' => ['required', 'string', 'max:255'],
            'tim_pelaksana.*.nrp' => ['nullable', 'string', 'max:100'],
            'tim_pelaksana.*.jabatan' => ['nullable', 'string', 'max:255'],
            'tim_pelaksana.*.departemen' => ['nullable', 'string', 'max:255'],
            'tim_pelaksana.*.perusahaan' => ['required', 'string', 'max:255'],
        ], $this->photoValidationRules());
    }

    private function formData(): array
    {
        return [
            'checklistSections' => self::CHECKLIST_SECTIONS,
            'inspector' => auth()->user(),
        ];
    }

    public function clone(Request $request)
    {
        return $this->cloneInspeksi->cloneInpeksi($request, new OfaModel(), 'inspeksi.ofa.index');
    }

    private function filteredQuery(Request $request)
    {
        return OfaModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $cols = ['code_number_unit', 'serial_number_modul', 'type_unit', 'jobsite'];
            $query->search($cols, $request->search);
        });
    }
}
