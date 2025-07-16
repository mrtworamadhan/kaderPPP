<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Suara;
use Faker\Factory as Faker;

class SuaraSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $desaCibinong = [
            43 => 'Pondok Rajeg', 44 => 'Karadenan', 45 => 'Harapan Jaya',
            46 => 'Nanggewer', 47 => 'Nanggewer Mekar', 48 => 'Cibinong',
            49 => 'Pakansari', 50 => 'Tengah', 51 => 'Sukahati',
            52 => 'Ciriung', 53 => 'Ciri Mekar', 54 => 'Pabuaran',
            55 => 'Pabuaran Mekar'
        ];

        $tahunPemilu = [2019, 2024];

        $this->command->info('Membuat data Suara untuk Kecamatan Cibinong...');

        foreach ($tahunPemilu as $tahun) {
            $this->command->line(" > Memproses tahun: {$tahun}");
            foreach ($desaCibinong as $id_desa => $nama_desa) {
                Suara::create([
                    'tahun' => $tahun,
                    'id_kecamatan' => 3, // Cibinong
                    'id_desa' => $id_desa,
                    'kecamatan' => 'Cibinong', // Optional
                    'desa' => $nama_desa, //Optional
                    'dprd' => $faker->numberBetween(500, 3000),
                    'dpr_prov' => $faker->numberBetween(400, 2500),
                    'dpr_ri' => $faker->numberBetween(300, 2000),
                    'tps' => 'TPS ' . $faker->numberBetween(1, 25),
                    'sumber' => 'manual input',
                ]);
            }
        }
        $this->command->info('Data Suara selesai dibuat.');
    }
}