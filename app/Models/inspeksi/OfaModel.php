<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfaModel extends Model
{
    use HasFactory;

    protected $table = 'inspeksi_ofa';

    protected $fillable = [
        'photo_path',
        'tanggal_inspeksi',
        'inspection_month',
        'inspection_year',
        'project_name',
        'version',
        'divisi_department',
        'type_unit',
        'jobsite',
        'code_number_unit',
        'serial_number_modul',
        'location',
        'no_asset',
        'no_lambung',
        'jenis_unit',
        'merek',
        'serial_number',
        'item_pemeriksaan',
        'catatan_tambahan',
        'keterangan',
        'tim_pelaksana',
        'diinspeksi_oleh',
        'diperiksa_oleh',
        'status_approval',
        'approved_by',
        'qr_code_persetujuan',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_inspeksi' => 'date',
            'item_pemeriksaan' => 'array',
            'tim_pelaksana' => 'array',
            'approved_at' => 'datetime',
        ];
    }
    
// ambil data nrp dari tabel karyawan
    public function karyawanId() {
        return $this->belongsTO(KaryawanModel::class,'diinspeksi_oleh', 'nrp');
    }
}
