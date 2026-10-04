<?php

namespace App\Models\inspeksi;

use App\Models\KaryawanModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ss6Model extends Model
{
    //
    use HasFactory;

    protected $table = 'inspeksi_ss6';

    protected $fillable = [
        'photo_path',
        'no_asset',
        'no_lambung',
        'tanggal_inspeksi',
        'serial_number',

        'kondisi_monitor',
        'kondisi_bracket',
        'kondisi_car_charger',
        'kondisi_kabel_power',
        'kondisi_app_lock',
        'software_ppa_teams',
        'kondisi_baterai',
        'output_powercharge',

        'keterangan',

        'diinspeksi_oleh',
        'diperiksa_oleh',
        'status_approval', 'approved_by', 'qr_code_persetujuan', 'approved_at',
    ];

    protected $casts = [
        'kondisi_monitor' => 'array',
        'kondisi_bracket' => 'array',
        'kondisi_car_charger' => 'array',
        'kondisi_kabel_power' => 'array',
        'kondisi_app_lock' => 'array',
        'software_ppa_teams' => 'array',
        'kondisi_baterai' => 'array',
        'approved_at' => 'datetime',
    ];

    public function QrCodeKaryawan() {
        return $this->belongsTo(KaryawanModel::class, 'diinspeksi_oleh', 'nrp');
    }
}
