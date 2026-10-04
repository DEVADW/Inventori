<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyektorModel extends Model
{
    use HasFactory;

    protected $table = 'inspeksi_proyektors';

    protected $fillable = [
        'photo_path', 'nomor_aset', 'departemen', 'merek', 'lokasi', 'type',
        'tanggal_inspeksi', 'sn', 'kondisi_casing',
        'tindakan_kondisi_casing', 'kebersihan', 'tindakan_kebersihan',
        'kabel_adaptor', 'tindakan_kabel_adaptor', 'lensa_proyektor',
        'tindakan_lensa_proyektor', 'indikator_lampu',
        'tindakan_indikator_lampu', 'fokus_zoom', 'tindakan_fokus_zoom',
        'kecerahan_kontras', 'tindakan_kecerahan_kontras',
        'koneksi_input_hdmi', 'koneksi_input_vga', 'koneksi_input_usb',
        'keterangan', 'inspektor', 'jabatan_inspektor', 'diketahui_oleh',
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
