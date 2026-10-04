<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KaryawanModel;

class karyawanSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        KaryawanModel::create([
            'nama' => 'Bayu Rezky Ramadan',
            'nrp' => '250504',
            'jabatan' => 'Helper',
            'departemen' => 'ICT MD',
            'qr_code' => 'Nama : Bayu Rezky Ramadan | NRP : 250504 | Jabatan : Helper'
        ]);
    }
}
