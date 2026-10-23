<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimCardModel extends Model
{
    use HasFactory;

    protected $table = 'sim_cards';

    protected $fillable = ['nomor_msisdn', 'jumlah_paket_kuota', 'status_kartu', 'remark', 'associate'];
}
