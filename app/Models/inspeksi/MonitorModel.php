<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorModel extends Model
{
    use HasFactory;

    protected $table = 'inspeksi_monitor';

    protected $fillable = [
        'photo_path', 'nomor_aset', 'merek', 'type', 'sn', 'departemen', 'lokasi',
        'tanggal_inspeksi', 'keterangan', 'tampilan_layer', 'kabel_power',
        'bracket_dudukan', 'kebersihan', 'stop_kontak',
        'tindakan_tampilan_layer', 'tindakan_kabel_power',
        'tindakan_bracket_dudukan', 'tindakan_kebersihan',
        'tindakan_stop_kontak', 'inspektor', 'jabatan_inspektor',
        'diketahui_oleh', 'status_approval', 'approved_by', 'qr_code_persetujuan', 'approved_at',
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
