<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed akun admin.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['nrp' => '250504'],
            [
                'nama' => 'Bayu Rezky Ramadhan',
                'role' => 'admin',
                'email' => 'bayu221200@gmail.com',
                'jabatan' => 'Helper ICT',
                'password' => Hash::make('250504'),
                'akses_inspeksi' => array_keys(User::inspectionModules()),
            ]
        );
    }
}
