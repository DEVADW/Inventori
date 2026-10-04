<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IccUnitModels extends Model
{
    //

    protected $table = "icc_units";

    protected $fillable = ['device_name', 'fleet', 'sim', 'imei'];
}
