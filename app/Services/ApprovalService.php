<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApprovalService
{
    /**
     * Approve single inspection record
     */
    public function approve(Request $request, Model $inspection, string $requiredRole = 'GL'): array
    {
        // Authorization check
        $this->ensureAuthorized($request, $requiredRole);

        // Check if already approved
        if ($inspection->approved_at) {
            return [
                'success' => false,
                'message' => 'Inspeksi ini sudah di-approve.',
                'type' => 'info',
            ];
        }

        // Update with approval data
        $inspection->update($this->generateApprovalData($request));

        return [
            'success' => true,
            'message' => 'Inspeksi berhasil di-approve.',
            'type' => 'success',
        ];
    }

    /**
     * Approve multiple inspection records
     */
    public function approveMultiple(Request $request, $query, string $requiredRole = 'GL'): array
    {
        // Authorization check
        $this->ensureAuthorized($request, $requiredRole);

        // Get pending inspections
        $pendingInspections = $query->whereNull('approved_at')->get();

        if ($pendingInspections->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Belum ada inspeksi pending untuk di-approve.',
                'type' => 'info',
                'count' => 0,
            ];
        }

        // Update all pending inspections
        foreach ($pendingInspections as $inspection) {
            $inspection->update($this->generateApprovalData($request));
        }

        return [
            'success' => true,
            'message' => "{$pendingInspections->count()} inspeksi berhasil di-approve.",
            'type' => 'success',
            'count' => $pendingInspections->count(),
        ];
    }

    /**
     * Generate approval data
     */
    public function generateApprovalData(Request $request): array
    {
        $groupLeader = $request->user();
        $approvedAt = now();

        return [
            'status_approval' => 'approved',
            'diperiksa_oleh' => $groupLeader->nama,
            'approved_by' => $groupLeader->nama,
            'qr_code_persetujuan' => "Nama: {$groupLeader->nama} | NRP: {$groupLeader->nrp} | Jabatan: {$groupLeader->jabatan} | Approved: {$approvedAt->format('d-m-Y H:i')}",
            'approved_at' => $approvedAt,
        ];
    }

    /**
     * Check if record can be updated (not approved)
     */
    public function canUpdate(Model $inspection): bool
    {
        return !$inspection->approved_at;
    }

    /**
     * Ensure user has required role for approval
     */
    private function ensureAuthorized(Request $request, string $requiredRole): void
    {
        abort_unless(
            $request->user()->jabatan === $requiredRole,
            403,
            "Hanya {$requiredRole} yang dapat melakukan approval."
        );
    }

    /**
     * Get approval status display
     */
    public function getApprovalStatus(Model $inspection): array
    {
        return [
            'is_approved' => (bool) $inspection->approved_at,
            'approved_at' => $inspection->approved_at,
            'approved_by' => $inspection->approved_by ?? null,
            'status' => $inspection->status_approval ?? null,
        ];
    }
}
