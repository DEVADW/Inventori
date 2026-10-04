<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitsModel extends Model
{
    use HasFactory;

    protected $table = "units";

    protected $fillable = [
        'code_unit',
        'model',
        'serial_number',
        'type_unit',
    ];
}

