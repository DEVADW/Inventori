<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionSettingModel extends Model
{
    protected $table = 'inspection_settings';

    protected $fillable = ['module', 'interval_months'];

    public static function intervalMonths(string $module): int
    {
        return (int) (static::where('module', $module)->value('interval_months') ?? 1);
    }
}
