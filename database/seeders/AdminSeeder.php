<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Admin Pusat
        User::create([
            'nik' => 'admin_dpc',
            'password' => Hash::make('adminekaderapp'),
            'role' => 'admin_pusat',
        ]);

        // 2. Admin PAC (Kecamatan Cibinong)
        User::create([
            'nik' => 'pac_cibinong',
            'password' => Hash::make('pacekaderapp'),
            'role' => 'admin_pac',
            'id_kecamatan' => 3, // ID Kecamatan Cibinong
        ]);

        // 3. Admin Ranting (Desa/Kelurahan di Kec. Cibinong)
        $adminRanting = [
            43 => 'rantingpondokrajeg_cibinong',
            44 => 'rantingkaradenan_cibinong',
            45 => 'rantingharapanjaya_cibinong',
            46 => 'rantingnanggewer_cibinong',
            47 => 'rantingnanggewermekar_cibinong',
            48 => 'rantingcibinong_cibinong',
            49 => 'rantingpakansari_cibinong',
            50 => 'rantingtengah_cibinong',
            51 => 'rantingsukahati_cibinong',
            52 => 'rantingciriung_cibinong',
            53 => 'rantingcirimekar_cibinong',
            54 => 'rantingpabuaran_cibinong',
            55 => 'rantingpabuaranmekar_cibinong',
        ];

        foreach ($adminRanting as $id_desa => $nik) {
            User::create([
                'nik' => $nik,
                'password' => Hash::make('rantingekaderapp'),
                'role' => 'admin_desa', // Role untuk admin ranting
                'id_kecamatan' => 3, // ID Kecamatan Cibinong
                'id_desa' => $id_desa,
            ]);
        }
    }
}