<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanModel extends Model
{
    //
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = ['nama', 'nrp', 'jabatan', 'departemen', 'qr_code'];
}
