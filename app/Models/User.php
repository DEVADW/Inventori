<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'nrp',
        'role',
        'password',
        'jabatan',
        'foto',
        'akses_inspeksi',
        'izin_galeri_foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'akses_inspeksi' => 'array',
            'izin_galeri_foto' => 'boolean',
        ];
    }

    /**
     * Daftar modul inspeksi yang tersedia di sistem.
     */
    public static function inspectionModules(): array
    {
        return [
            'stavolt' => ['label' => 'Inspeksi Stavolt', 'route' => 'inspeksi.stavolt.index'],
            'ups' => ['label' => 'Inspeksi UPS', 'route' => 'inspeksi.ups.index'],
            'monitor' => ['label' => 'Inspeksi Monitor/TV', 'route' => 'inspeksi.monitor.index'],
            'proyektor' => ['label' => 'Inspeksi Proyektor', 'route' => 'inspeksi.proyektor.index'],
            'ss6' => ['label' => 'Inspeksi SS6', 'route' => 'inspeksi.ss6.index'],
            'ofa' => ['label' => 'Inspeksi OFA', 'route' => 'inspeksi.ofa.index'],
            'icc' => ['label' => 'Inspeksi ICC', 'route' => 'inspeksi.icc.index'],
        ];
    }

    /**
     * Modul inspeksi yang boleh diakses user ini.
     */
    public function allowedInspectionModules(): array
    {
        $modules = array_keys(self::inspectionModules());

        if ($this->role === 'admin') {
            return $modules;
        }

        if (is_array($this->akses_inspeksi)) {
            return array_values(array_intersect($modules, $this->akses_inspeksi));
        }

        $jabatan = strtolower(trim((string) ($this->jabatan ?? '')));
        $ictTechnician = in_array($jabatan, ['ict_technician', 'ict technician'], true);
        $hardwareEngineer = in_array($jabatan, ['hardware engineer', 'hardware_enggineer', 'hardware engg', 'hardware_engg'], true);
        $isIct = in_array($jabatan, ['ict', 'hardware_enggineer', 'hardware engineer', 'hardware engg', 'hardware_engg', 'ict_technician', 'ict technician', 'non_staff', 'helper'], true);
        $isGL = $jabatan === 'gl';

        if ($ictTechnician) {
            return ['icc'];
        }

        if ($hardwareEngineer) {
            return ['ofa'];
        }

        if ($isGL) {
            return $modules;
        }

        if ($isIct) {
            return ['stavolt', 'ups', 'monitor', 'proyektor', 'ss6'];
        }

        return [];
    }

    /**
     * Teks QR code pengguna (diambil dari nama, NRP, dan jabatan).
     */
    public function getQrCodeAttribute(): string
    {
        return "Nama : {$this->nama} | NRP : {$this->nrp} | Jabatan : {$this->jabatan}";
    }

}
