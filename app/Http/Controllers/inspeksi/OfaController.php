<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesInspectionPhoto;
use App\Exports\InspectionExport;
use App\Models\inspeksi\OfaModel;
use App\Models\UnitsModel;
use App\Services\ApprovalService;
use App\Services\CloneInspeksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Illuminate\Http\JsonResponse;

class OfaController extends Controller
{
    use HandlesInspectionPhoto;
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
        $ofas = $this->filteredQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Inspeksi.ofa.index', [
            'ofas' => $ofas,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create()
    {
        return view('Inspeksi.ofa.create', $this->formData());
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

    public function select2(Request $request): JsonResponse
    {
        $search = $request->input('q', '');
        $perPage = 20; // jumlah item per "page" scroll select2

        $query = UnitsModel::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('code_unit', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('code_unit')->paginate($perPage);

        // format hasil sesuai kebutuhan select2 (id, text)
        $results = $units->getCollection()->map(function ($unit) {
            return [
                'id'   => $unit->id,
                // text ini yang akan tampil di dropdown select2
                'text' => "{$unit->code_unit} - {$unit->model} ({$unit->serial_number})",
                // data tambahan kalau mau dipakai di JS setelah dipilih
                'code_unit'     => $unit->code_unit,
                'model'         => $unit->model,
                'serial_number' => $unit->serial_number,
                'type_unit'     => $unit->type_unit ?? $unit->model,
            ];
        });

        return response()->json([
            'results'    => $results,
            'pagination' => [
                // true = select2 akan load "page" berikutnya saat di-scroll
                'more' => $units->currentPage() < $units->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $ofa = OfaModel::create($data);
        $this->storeInspectionPhoto($request, $ofa);

        return redirect()
            ->route('inspeksi.ofa.index')
            ->with('success', 'Data inspeksi OFA berhasil disimpan.');
    }

    public function edit(OfaModel $ofa)
    {
        return view('Inspeksi.ofa.edit', array_merge($this->formData(), compact('ofa')));
    }

    public function update(Request $request, OfaModel $ofa)
    {
        if (!$this->approvalService->canUpdate($ofa)) {
            return redirect()
                ->route('inspeksi.ofa.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $data = $this->validatedData($request);

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
            ->where(function ($query) use ($month, $year) {
                $query->where(function ($query) use ($month, $year) {
                    $query->where('inspection_month', $month)
                        ->where('inspection_year', $year);
                })->orWhere(function ($query) use ($month, $year) {
                    $query->whereYear('tanggal_inspeksi', $year)
                        ->whereMonth('tanggal_inspeksi', $month);
                });
            })
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

    private function validatedData(Request $request): array
    {
        $data = $request->validate(array_merge([
            'project_name' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:100'],
            'divisi_department' => ['nullable', 'string', 'max:255'],
            'type_unit' => ['required', 'string', 'max:255'],
            'jobsite' => ['required', 'string', 'max:255'],
            'tanggal_inspeksi' => ['required', 'date'],
            'code_number_unit' => ['required', 'string', 'max:255'],
            'serial_number_modul' => ['nullable', 'string', 'max:255'],
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
        ], $this->photoValidationRules()));

        $data['item_pemeriksaan'] = collect($data['item_pemeriksaan'])
            ->values()
            ->all();

        $inspectionDate = \Carbon\Carbon::parse($data['tanggal_inspeksi']);
        $data['inspection_month'] = $inspectionDate->month;
        $data['inspection_year'] = $inspectionDate->year;

        $inspector = $request->user();

        $data['diinspeksi_oleh'] = $inspector->nrp;

        $data['tim_pelaksana'] = collect($data['tim_pelaksana'])
            ->values()
            ->all();

        return $data;
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
