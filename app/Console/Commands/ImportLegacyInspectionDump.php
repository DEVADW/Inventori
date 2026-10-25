<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportLegacyInspectionDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:import-inspection
        {table : Nama tabel tujuan, misal inspeksi_icc}
        {path : Path file dump SQL, relatif terhadap root project}
        {--dedup=* : Kolom untuk deteksi duplikat, misal --dedup=no_lambung_unit --dedup=tanggal_inspeksi}
        {--dry-run : Preview tanpa menulis ke tabel tujuan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dump SQL inspeksi lama ke tabel inspeksi yang sedang berjalan';

    public function handle(): int
    {
        $table = $this->argument('table');
        $path = base_path($this->argument('path'));
        $dedupColumns = $this->option('dedup');

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        if (! Schema::hasTable($table)) {
            $this->error("Tabel tujuan tidak ditemukan: {$table}");

            return self::FAILURE;
        }

        $targetColumns = Schema::getColumnListing($table);
        foreach ($dedupColumns as $col) {
            if (! in_array($col, $targetColumns, true)) {
                $this->error("Kolom dedup '{$col}' tidak ada di tabel {$table}.");

                return self::FAILURE;
            }
        }

        $stagingTable = 'legacy_'.$table.'_staging';

        $sql = file_get_contents($path);
        $sql = str_replace("`{$table}`", "`{$stagingTable}`", $sql);

        DB::unprepared("DROP TABLE IF EXISTS `{$stagingTable}`");
        DB::unprepared($sql);

        $rows = DB::table($stagingTable)->orderBy('id')->get();
        $this->info('Baris ditemukan di dump: '.$rows->count());

        $imported = 0;
        $skippedDuplicate = 0;

        foreach ($rows as $row) {
            $data = (array) $row;
            unset($data['id']);

            // Hanya sertakan kolom yang memang ada di tabel tujuan.
            $data = array_intersect_key($data, array_flip($targetColumns));

            if (! empty($dedupColumns)) {
                $query = DB::table($table);
                foreach ($dedupColumns as $col) {
                    $query->where($col, $data[$col] ?? null);
                }

                if ($query->exists()) {
                    $skippedDuplicate++;

                    continue;
                }
            }

            if (! $this->option('dry-run')) {
                DB::table($table)->insert($data);
            }

            $imported++;
        }

        DB::unprepared("DROP TABLE IF EXISTS `{$stagingTable}`");

        $this->info(($this->option('dry-run') ? '[DRY RUN] ' : '')."Akan diimpor: {$imported}");
        $this->info("Dilewati (duplikat berdasarkan ".implode(', ', $dedupColumns).'): '.$skippedDuplicate);

        $this->warn('Catatan: file foto (.jpg) tidak ikut dalam dump SQL. Path foto lama tetap disimpan sebagai referensi, tapi gambar tidak akan tampil kecuali file aslinya disalin manual ke storage/app/public/ dengan path yang sama.');

        return self::SUCCESS;
    }
}
