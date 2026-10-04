<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTamuModel extends Model
{
    //
    use HasFactory;

    protected $table = 'buku_tamu';

    protected $primaryKey = 'no';

    protected $fillable = ['nama', 'no_telp', 'nrp', 'instansi', 'keperluan'];
}
