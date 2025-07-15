<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Admin Pusat
        User::create([
            'nik' => 'admin_dpc',
            'password' => Hash::make('bogorjuara'),
            'role' => 'admin_pusat',
        ]);
        User::create([
            'nik' => 'bpokk_dpc',
            'password' => Hash::make('bogorjuara'),
            'role' => 'admin_pusat',
        ]);

        // Admin PAC Kecamatan
        $adminsPac = [
            'pac_cibinong',
            'pac_sukaraja',
            'pac_citeureup',
            'pac_babakan madang',
            'pac_klapanunggal',
            'pac_gunung putri',
            'pac_cileungsi',
            'pac_jonggol',
            'pac_cariu',
            'pac_tanjungsari',
            'pac_sukamakmur',
            'pac_ciawi',
            'pac_megamendung',
            'pac_cisarua',
            'pac_caringin',
            'pac_cigombong',
            'pac_cijeruk',
            'pac_tamansari',
            'pac_ciomas',
            'pac_dramaga',
            'pac_ciampea',
            'pac_cibungbulang',
            'pac_tenjolaya',
            'pac_pamijahan',
            'pac_leuwiliang',
            'pac_leuwisadeng',
            'pac_nanggung',
            'pac_sukajaya',
            'pac_cigudeg',
            'pac_jasinga',
            'pac_rumpin',
            'pac_tenjo',
            'pac_parung panjang',
            'pac_ranca bungur',
            'pac_kemang',
            'pac_tajurhalang',
            'pac_ciseeng',
            'pac_gunung sindur',
            'pac_parung',
            'pac_bojong gede',
        ];

        foreach ($adminsPac as $nik) {
            User::create([
                'nik' => $nik,
                'password' => Hash::make('harapanrakyat'),
                'role' => 'admin_pac',
            ]);
        }
    }
}
