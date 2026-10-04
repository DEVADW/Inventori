<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Models\inspeksi\MonitorModel;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

/**
 * CONTOH: MonitorController dengan Approval Service Integration
 * 
 * Salinan kode ini ke MonitorController.php sesuai dengan struktur yang sudah ada
 */
class MonitorController extends Controller
{
    public function __construct(protected ApprovalService $approvalService)
    {
    }

    public function index(Request $request)
    {
        $monitors = $this->filteredQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('inspeksi.monitor.index', [
            'monitors' => $monitors,
            'isGroupLeader' => $request->user()->jabatan === 'GL',
        ]);
    }

    public function create()
    {
        return view('inspeksi.monitor.create');
    }

    public function store(Request $request)
    {
        MonitorModel::create($this->validateRequest($request));

        return redirect()->route('inspeksi.monitor.index')
            ->with('success', 'Data inspeksi monitor/TV berhasil disimpan.');
    }

    public function edit(MonitorModel $monitor)
    {
        return view('inspeksi.monitor.edit', compact('monitor'));
    }

    public function update(Request $request, MonitorModel $monitor)
    {
        if (!$this->approvalService->canUpdate($monitor)) {
            return redirect()->route('inspeksi.monitor.index')
                ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
        }

        $monitor->update($this->validateRequest($request));

        return redirect()->route('inspeksi.monitor.index')
            ->with('success', 'Data inspeksi monitor/TV berhasil diperbarui.');
    }

    public function destroy(MonitorModel $monitor)
    {
        $monitor->delete();

        return redirect()->route('inspeksi.monitor.index')
            ->with('success', 'Data inspeksi monitor/TV berhasil dihapus.');
    }

    /**
     * Approve single inspection
     */
    public function approve(Request $request, MonitorModel $monitor)
    {
        $result = $this->approvalService->approve($request, $monitor);

        if (!$result['success']) {
            return back()->with($result['type'], $result['message']);
        }

        return back()->with($result['type'], $result['message']);
    }

    /**
     * Approve all pending inspections
     */
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

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'project_name' => ['nullable', 'string', 'max:255'],
            'type_unit' => ['required', 'string', 'max:255'],
            'jobsite' => ['required', 'string', 'max:255'],
            'code_number_unit' => ['required', 'string', 'max:255'],
            // ... other validation rules
        ]);
    }

    private function filteredQuery(Request $request)
    {
        return MonitorModel::query()->when($request->filled('search'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('project_name', 'like', "%{$request->search}%")
                    ->orWhere('code_number_unit', 'like', "%{$request->search}%")
                    ->orWhere('type_unit', 'like', "%{$request->search}%")
                    ->orWhere('jobsite', 'like', "%{$request->search}%");
            });
        });
    }
}
