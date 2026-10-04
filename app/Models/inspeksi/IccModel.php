<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IccModel extends Model
{
    use HasFactory;

    protected $table = 'inspeksi_icc';

    protected $fillable = [
        'photo_path',
        'no_lambung_unit',
        'tanggal_inspeksi',
        'lokasi_inspeksi',
        'item_pemeriksaan',
        'note',
        'inspektor',
        'diketahui_oleh',
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
            'approved_at' => 'datetime',
        ];
    }

    public function IdKaryawan() {
        return $this->belongsTo(KaryawanModel::class, 'inspektor', 'nrp');
    }
}
