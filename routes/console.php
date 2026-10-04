<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Reset Inspeksi Bulanan - Tanggal 1 setiap bulan jam 00:00
Schedule::command('app:reset-monthly-inspection')
    ->monthlyOn(1, '00:00')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Log::error('Reset Monthly Inspection Command Failed');
    })
    ->onSuccess(function () {
        \Log::info('Reset Monthly Inspection Command Success');
    });

