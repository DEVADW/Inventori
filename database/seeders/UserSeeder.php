<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['nrp' => '250504'],
            [
                'nama' => 'Bayu Rezky Ramadan',
                'jabatan' => 'helper',
                'password' => Hash::make('250504'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['nrp' => '26000009'],
            [
                'nama' => 'Elprida Panjaitan',
                'jabatan' => 'admin',
                'password' => Hash::make('250504'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['nrp' => '01010101',],
            [
                'nama' => 'security',
                'jabatan' => 'security',
                'password' => Hash::make('01010101'),
                'role' => 'user'
            ]
        );
    }
}
