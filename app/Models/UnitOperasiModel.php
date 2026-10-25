<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOperasiModel extends Model
{
    use HasFactory;

    protected $table = 'unit_operasi';

    protected $fillable = [
        'code_unit', 'type_unit', 'remark', 'ofa', 'icc', 'ss6',
        'serial_number_module', 'serial_number_tab', 'no_aset_tab',
    ];

    protected $casts = [
        'ofa' => 'boolean',
        'icc' => 'boolean',
        'ss6' => 'boolean',
    ];

    public function simCards()
    {
        return $this->hasMany(SimCardModel::class, 'unit_operasi_id');
    }
}
