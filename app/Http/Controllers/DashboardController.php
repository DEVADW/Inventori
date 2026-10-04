<?php

namespace App\Http\Controllers;

use App\Models\BukuTamuModel;
use App\Models\InventarisModel;
use App\Models\inspeksi\IccModel;
use App\Models\inspeksi\MonitorModel;
use App\Models\inspeksi\OfaModel;
use App\Models\inspeksi\ProyektorModel;
use App\Models\inspeksi\StavoltModel;
use App\Models\inspeksi\UpsModel;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $isHardwareEngineer = in_array(strtolower(trim((string) $user->jabatan)), [
            'hardware engineer',
            'hardware_enggineer',
            'hardware engg',
            'hardware_engg',
        ], true);

        $isIctTechnician = in_array(strtolower(trim((string) $user->jabatan)), [
            'ict technician',
            'ict_technician',
            'ict',
        ], true);

        // Data untuk Admin
        $totalInventaris = null;
        $dipinjam = null;
        $inventarisTerbaru = null;

        // Data untuk Admin & Security
        $totalTamuHariIni = null;
        $tamuTerbaru = null;

        // Data untuk semua user (Admin, GL, Staff, Non-staff)
        $inspeksi = [
            'stavolt' => StavoltModel::count(),
            'ups' => UpsModel::count(),
            'monitor' => MonitorModel::count(),
            'proyektor' => ProyektorModel::count(),
        ];
        $totalInspeksi = array_sum($inspeksi);

        // OFA dashboard data for Hardware Engineer
        $totalOfaData = null;
        $ofaThisMonth = null;
        $latestOfaInspectors = null;

        if ($isHardwareEngineer) {
            $totalOfaData = OfaModel::count();
            $ofaThisMonth = OfaModel::whereMonth('tanggal_inspeksi', now()->month)
                ->whereYear('tanggal_inspeksi', now()->year)
                ->count();
            $latestOfaInspectors = OfaModel::query()
                ->latest('tanggal_inspeksi')
                ->take(5)
                ->get();
        }

        // ICC dashboard data for ICT Technician
        $totalIccData = null;
        $iccThisMonth = null;
        $latestIccInspectors = null;

        if ($isIctTechnician) {
            $totalIccData = IccModel::count();
            $iccThisMonth = IccModel::whereMonth('tanggal_inspeksi', now()->month)
                ->whereYear('tanggal_inspeksi', now()->year)
                ->count();
            $latestIccInspectors = IccModel::query()
                ->latest('tanggal_inspeksi')
                ->take(5)
                ->get();
        }

        // Admin dapat melihat semua data
        if ($user->role === 'admin') {
            $totalInventaris = InventarisModel::count();
            $dipinjam = InventarisModel::where('status_peminjaman', 'Belum Dikembalikan')->count();
            $totalTamuHariIni = BukuTamuModel::whereDate('created_at', today())->count();
            $inventarisTerbaru = InventarisModel::latest()->take(5)->get();
            $tamuTerbaru = BukuTamuModel::latest()->take(5)->get();
        }
        // Security hanya melihat data Buku Tamu
        elseif ($user->jabatan === 'security') {
            $totalTamuHariIni = BukuTamuModel::whereDate('created_at', today())->count();
            $tamuTerbaru = BukuTamuModel::latest()->take(5)->get();
        }
        // GL, Staff, Non-staff hanya melihat Inspeksi

        return view('dashboard', compact(
            'totalInventaris', 'dipinjam', 'totalTamuHariIni',
            'inspeksi', 'totalInspeksi', 'inventarisTerbaru', 'tamuTerbaru', 'user',
            'isHardwareEngineer', 'isIctTechnician', 'totalOfaData', 'ofaThisMonth', 'latestOfaInspectors',
            'totalIccData', 'iccThisMonth', 'latestIccInspectors'
        ));
    }
}
