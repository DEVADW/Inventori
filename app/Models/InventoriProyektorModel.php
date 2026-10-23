<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriProyektorModel extends Model
{
    use HasFactory;

    protected $table = 'inventori_proyektor';

    protected $fillable = ['no_aset', 'merek', 'tipe', 'serial_number', 'department', 'lokasi'];
}
