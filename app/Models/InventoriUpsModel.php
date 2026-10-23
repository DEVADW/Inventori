<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriUpsModel extends Model
{
    use HasFactory;

    protected $table = 'inventori_ups';

    protected $fillable = ['no_aset', 'merek', 'tipe', 'serial_number', 'department', 'lokasi'];
}
