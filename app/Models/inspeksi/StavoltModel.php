<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StavoltModel extends Model
{
    use HasFactory;

    protected $table = 'inspeksi_stavolts';

    protected $fillable = [
        'photo_path', 'nomor_aset', 'merek', 'type', 'sn', 'departemen', 'lokasi',
        'tanggal_inspeksi', 'keterangan', 'casing', 'tindakan_casing',
        'kebersihan', 'tindakan_kebersihan', 'kabel_adaptor',
        'tindakan_kabel_adaptor', 'tombol_switch', 'tindakan_tombol_switch',
        'indikator_voltase', 'tindakan_indikator_voltase',
        'respon_perubahan_beban', 'tindakan_respon_perubahan_beban',
        'inspektor', 'jabatan_inspektor', 'diketahui_oleh',
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
