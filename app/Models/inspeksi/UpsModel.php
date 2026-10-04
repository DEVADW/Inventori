<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsModel extends Model
{
    //
    use HasFactory;
    protected $table = 'inspeksi_ups';

    protected $fillable = [
        'photo_path',
        'nomor_aset',
        'merek',
        'type',
        'sn',
        'departemen',
        'lokasi',
        'tanggal_inspeksi',
        'keterangan',
        'casing',
        'tindakan_casing',
        'kebersihan',
        'tindakan_kebersihan',
        'kabel_adaptor',
        'tindakan_kabel_adaptor',
        'tombol_switch',
        'tindakan_tombol_switch',
        'indikator_status',
        'tindakan_indikator_status',
        'fungsi_alarm',
        'tindakan_fungsi_alarm',
        'respon_kehilangan_daya',
        'tindakan_respon_kehilangan_daya',
        'fuse',
        'tindakan_fuse',
        'inspektor',
        'diketahui_oleh',
        'status_approval', 'approved_by', 'qr_code_persetujuan', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_inspeksi' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function inspektorKaryawan()
    {
        return $this->belongsTo(KaryawanModel::class, 'inspektor', 'nrp');
    }
}
