<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriStavoltModel extends Model
{
    use HasFactory;

    protected $table = 'inventori_stavolt';

    protected $fillable = ['no_aset', 'merek', 'tipe', 'serial_number', 'department', 'lokasi'];
}
