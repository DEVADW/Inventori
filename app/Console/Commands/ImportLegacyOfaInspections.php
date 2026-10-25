<?php

namespace App\Console\Commands;

use App\Models\UnitOperasiModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyOfaInspections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:import-ofa {path=database/legacy-imports/inspeksi_ofa.sql} {--dry-run : Preview without writing to inspeksi_ofa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import historical OFA inspection records from a legacy SQL dump';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $sql = file_get_contents($path);
        $sql = str_replace('`inspeksi_ofa`', '`legacy_ofa_staging`', $sql);

        DB::unprepared('DROP TABLE IF EXISTS `legacy_ofa_staging`');
        DB::unprepared($sql);

        $rows = DB::table('legacy_ofa_staging')->orderBy('id')->get();
        $this->info('Baris ditemukan di dump: '.$rows->count());

        $existingUnitCodes = UnitOperasiModel::where('ofa', true)->pluck('code_unit')->all();
        $unknownUnitCodes = [];

        $imported = 0;
        $skippedDuplicate = 0;

        foreach ($rows as $row) {
            $alreadyExists = DB::table('inspeksi_ofa')
                ->where('code_number_unit', $row->code_number_unit)
                ->where('tanggal_inspeksi', $row->tanggal_inspeksi)
                ->exists();

            if ($alreadyExists) {
                $skippedDuplicate++;

                continue;
            }

            if ($row->code_number_unit && ! in_array($row->code_number_unit, $existingUnitCodes, true)) {
                $unknownUnitCodes[$row->code_number_unit] = true;
            }

            if (! $this->option('dry-run')) {
                DB::table('inspeksi_ofa')->insert([
                    'photo_path' => $row->photo_path,
                    'project_name' => $row->project_name,
                    'version' => $row->version,
                    'divisi_department' => $row->divisi_department,
                    'type_unit' => $row->type_unit,
                    'jobsite' => $row->jobsite,
                    'code_number_unit' => $row->code_number_unit,
                    'serial_number_modul' => $row->serial_number_modul,
                    'location' => $row->location,
                    'item_pemeriksaan' => $row->item_pemeriksaan,
                    'catatan_tambahan' => $row->catatan_tambahan,
                    'tim_pelaksana' => $row->tim_pelaksana,
                    'diinspeksi_oleh' => $row->diinspeksi_oleh,
                    'diperiksa_oleh' => $row->diperiksa_oleh,
                    'status_approval' => $row->status_approval,
                    'approved_by' => $row->approved_by,
                    'qr_code_persetujuan' => $row->qr_code_persetujuan,
                    'approved_at' => $row->approved_at,
                    'tanggal_inspeksi' => $row->tanggal_inspeksi,
                    'status_pemeriksaan' => $row->status_pemeriksaan,
                    'inspection_month' => $row->inspection_month,
                    'inspection_year' => $row->inspection_year,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            $imported++;
        }

        DB::unprepared('DROP TABLE IF EXISTS `legacy_ofa_staging`');

        $this->info(($this->option('dry-run') ? '[DRY RUN] ' : '')."Akan diimpor: {$imported}");
        $this->info("Dilewati (sudah ada unit+tanggal yang sama): {$skippedDuplicate}");

        if (! empty($unknownUnitCodes)) {
            $this->warn('Code unit berikut tidak ditemukan di Data Unit Operasi (ofa=true), inspeksi tetap diimpor tapi tidak akan muncul di halaman index OFA sampai unit ditambahkan/diaktifkan:');
            foreach (array_keys($unknownUnitCodes) as $code) {
                $this->line(" - {$code}");
            }
        }

        $this->warn('Catatan: file foto (.jpg) tidak ikut dalam dump SQL. Path foto lama tetap disimpan sebagai referensi, tapi gambar tidak akan tampil kecuali file aslinya disalin manual ke storage/app/public/ dengan path yang sama.');

        return self::SUCCESS;
    }
}
