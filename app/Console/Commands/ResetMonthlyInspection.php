<?php

namespace App\Console\Commands;

use App\Models\inspeksi\OfaModel;
use App\Models\inspeksi\IccModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetMonthlyInspection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-monthly-inspection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset inspeksi bulanan - Reset status untuk semua unit di bulan baru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Memulai reset inspeksi bulanan...');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        try {
            DB::beginTransaction();

            // Ambil semua unique units dari inspeksi bulan sebelumnya
            $lastMonth = $currentMonth - 1 ?: 12;
            $lastYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

            $previousUnits = OfaModel::where('inspection_month', $lastMonth)
                ->where('inspection_year', $lastYear)
                ->get(['code_number_unit', 'project_name', 'version', 'divisi_department', 'type_unit', 'jobsite', 'serial_number_modul', 'location'])
                ->unique('code_number_unit');

            $createdCount = 0;
            $resetCount = 0;

            foreach ($previousUnits as $unit) {
                // Cek apakah unit ini sudah ada di bulan ini
                $existingRecord = OfaModel::where('code_number_unit', $unit->code_number_unit)
                    ->where('inspection_month', $currentMonth)
                    ->where('inspection_year', $currentYear)
                    ->first();

                if ($existingRecord) {
                    // Reset status jika sudah ada
                    $existingRecord->update([
                        'status_pemeriksaan' => 'belum',
                        'status_approval' => 'menunggu',
                        'approved_by' => null,
                        'approved_at' => null,
                        'qr_code_persetujuan' => null,
                    ]);
                    $resetCount++;
                } else {
                    // Buat record baru untuk bulan ini
                    OfaModel::create([
                        'project_name' => $unit->project_name,
                        'version' => $unit->version,
                        'divisi_department' => $unit->divisi_department,
                        'type_unit' => $unit->type_unit,
                        'jobsite' => $unit->jobsite,
                        'code_number_unit' => $unit->code_number_unit,
                        'serial_number_modul' => $unit->serial_number_modul,
                        'location' => $unit->location,
                        'inspection_month' => $currentMonth,
                        'inspection_year' => $currentYear,
                        'status_pemeriksaan' => 'belum',
                        'status_approval' => 'menunggu',
                    ]);
                    $createdCount++;
                }
            }

            // ICC menggunakan tanggal_inspeksi sebagai periode inspeksi. Data unit
            // dari bulan sebelumnya disalin sekali untuk setiap nomor lambung.
            $startOfLastMonth = now()->subMonthNoOverflow()->startOfMonth();
            $endOfLastMonth = now()->subMonthNoOverflow()->endOfMonth();
            $startOfCurrentMonth = now()->startOfMonth();
            $endOfCurrentMonth = now()->endOfMonth();

            $previousIccs = IccModel::query()
                ->whereBetween('tanggal_inspeksi', [$startOfLastMonth, $endOfLastMonth])
                ->orderByDesc('tanggal_inspeksi')
                ->orderByDesc('id')
                ->get()
                ->unique('no_lambung_unit');

            $iccCreatedCount = 0;
            $iccResetCount = 0;

            foreach ($previousIccs as $icc) {
                $existingRecord = IccModel::query()
                    ->where('no_lambung_unit', $icc->no_lambung_unit)
                    ->whereBetween('tanggal_inspeksi', [$startOfCurrentMonth, $endOfCurrentMonth])
                    ->first();

                if ($existingRecord) {
                    $existingRecord->update([
                        'status_approval' => 'menunggu',
                        'approved_by' => null,
                        'approved_at' => null,
                        'qr_code_persetujuan' => null,
                    ]);
                    $iccResetCount++;

                    continue;
                }

                IccModel::create([
                    'no_lambung_unit' => $icc->no_lambung_unit,
                    'tanggal_inspeksi' => $startOfCurrentMonth->toDateString(),
                    'lokasi_inspeksi' => $icc->lokasi_inspeksi,
                    'item_pemeriksaan' => $icc->item_pemeriksaan,
                    'note' => $icc->note,
                    'inspektor' => $icc->inspektor,
                    'diketahui_oleh' => $icc->diketahui_oleh,
                    'diperiksa_oleh' => $icc->diperiksa_oleh,
                    'status_approval' => 'menunggu',
                    'approved_by' => null,
                    'approved_at' => null,
                    'qr_code_persetujuan' => null,
                ]);
                $iccCreatedCount++;
            }

            DB::commit();

            $this->info("ICC - Record baru dibuat: {$iccCreatedCount}");
            $this->info("ICC - Record di-reset: {$iccResetCount}");

            $this->info("✅ Reset inspeksi bulanan ({$currentMonth}/{$currentYear}) berhasil!");
            $this->info("   📝 Record baru dibuat: {$createdCount}");
            $this->info("   🔄 Record di-reset: {$resetCount}");
            $this->info("   📊 Total: " . ($createdCount + $resetCount));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
