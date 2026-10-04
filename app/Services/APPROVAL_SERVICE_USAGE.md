<?php

/**
 * CONTOH PENGGUNAAN APPROVAL SERVICE
 * 
 * ApprovalService dapat digunakan di semua inspection controller.
 * Berikut adalah contoh implementasi untuk berbagai scenario.
 */

// ============================================
// 1. BASIC SETUP DI CONTROLLER
// ============================================

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Models\inspeksi\MonitorModel;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    // Inject ApprovalService via constructor
    public function __construct(protected ApprovalService $approvalService)
    {
    }

    // ... existing methods ...
}

// ============================================
// 2. APPROVAL SINGLE RECORD
// ============================================

public function approve(Request $request, MonitorModel $monitor)
{
    $result = $this->approvalService->approve($request, $monitor);

    if (!$result['success']) {
        return back()->with($result['type'], $result['message']);
    }

    return back()->with($result['type'], $result['message']);
}

// ============================================
// 3. APPROVAL MULTIPLE RECORDS
// ============================================

public function approveAll(Request $request)
{
    $result = $this->approvalService->approveMultiple(
        $request,
        $this->filteredQuery($request)  // query builder
    );

    if (!$result['success']) {
        return back()->with($result['type'], $result['message']);
    }

    return redirect()->route('inspeksi.monitor.index', $request->only('search'))
        ->with($result['type'], $result['message']);
}

// ============================================
// 4. CHECK APPROVAL STATUS BEFORE UPDATE
// ============================================

public function update(Request $request, MonitorModel $monitor)
{
    if (!$this->approvalService->canUpdate($monitor)) {
        return redirect()->back()
            ->with('error', 'Inspeksi yang sudah di-approve tidak dapat diubah.');
    }

    $monitor->update($this->validateRequest($request));

    return redirect()->route('inspeksi.monitor.index')
        ->with('success', 'Data inspeksi monitor/TV berhasil diperbarui.');
}

// ============================================
// 5. GET APPROVAL STATUS INFO
// ============================================

public function show(MonitorModel $monitor)
{
    $approvalStatus = $this->approvalService->getApprovalStatus($monitor);

    return view('inspeksi.monitor.show', [
        'monitor' => $monitor,
        'approvalStatus' => $approvalStatus,
    ]);
}

// ============================================
// 6. CUSTOM ROLE REQUIREMENT (if needed)
// ============================================

public function approve(Request $request, MonitorModel $monitor)
{
    // Default menggunakan 'GL' (Group Leader)
    // Bisa di-customize jika ada role berbeda
    $result = $this->approvalService->approve($request, $monitor, 'GL');

    if (!$result['success']) {
        return back()->with($result['type'], $result['message']);
    }

    return back()->with($result['type'], $result['message']);
}

// ============================================
// 7. ROUTES YANG DIPERLUKAN
// ============================================

// routes/web.php
Route::middleware('auth')->group(function () {
    Route::prefix('inspeksi/ofa')->name('inspeksi.ofa.')->group(function () {
        Route::post('/{ofa}/approve', [OfaController::class, 'approve'])->name('approve');
        Route::post('/approve-all', [OfaController::class, 'approveAll'])->name('approve-all');
    });

    Route::prefix('inspeksi/monitor')->name('inspeksi.monitor.')->group(function () {
        Route::post('/{monitor}/approve', [MonitorController::class, 'approve'])->name('approve');
        Route::post('/approve-all', [MonitorController::class, 'approveAll'])->name('approve-all');
    });

    // ... repeat untuk controller lainnya
});

// ============================================
// 8. BLADE VIEW - APPROVAL BUTTONS
// ============================================

{{-- resources/views/inspeksi/ofa/index.blade.php --}}

@forelse($ofas as $ofa)
    <tr>
        <td>{{ $ofa->code_number_unit }}</td>
        <td>
            @if($ofa->approved_at)
                <span class="badge bg-success">Approved</span>
            @else
                <span class="badge bg-warning">Pending</span>
            @endif
        </td>
        <td>
            @if($isGroupLeader)
                @if(!$ofa->approved_at)
                    <form method="POST" action="{{ route('inspeksi.ofa.approve', $ofa) }}" style="display:inline;">
                        @csrf
                        <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                @endif
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">Tidak ada data</td>
    </tr>
@endforelse

// ============================================
// 9. HASIL APPROVAL SERVICE
// ============================================

/**
 * Response Format:
 * 
 * Success:
 * [
 *     'success' => true,
 *     'message' => 'Inspeksi berhasil di-approve.',
 *     'type' => 'success',
 *     'count' => 5  // hanya untuk approveMultiple
 * ]
 * 
 * Error/Info:
 * [
 *     'success' => false,
 *     'message' => 'Inspeksi ini sudah di-approve.',
 *     'type' => 'info'  // bisa 'info' atau berdasarkan kondisi
 * ]
 */

// ============================================
// 10. BENEFITS DARI APPROVAL SERVICE
// ============================================

/**
 * ✅ DRY (Don't Repeat Yourself)
 *    - Approval logic hanya di satu tempat
 *    - Mudah untuk update/maintenance
 * 
 * ✅ Konsistensi
 *    - Semua approval method sama format response
 *    - Format approval data yang sama di semua inspection
 * 
 * ✅ Reusability
 *    - Bisa digunakan di semua inspection controller
 *    - Bisa digunakan di service lain jika perlu
 * 
 * ✅ Testability
 *    - Mudah untuk test logic approval secara terpisah
 *    - Tidak perlu test di setiap controller
 * 
 * ✅ Extensibility
 *    - Mudah menambah feature (email notif, audit log, etc)
 *    - Tidak perlu update di semua controller
 */
