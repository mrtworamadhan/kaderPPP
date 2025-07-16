<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Suara;
use App\Models\WilayahRtrw;
use Faker\Factory as Faker;

class SuaraKabupatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Mengambil semua data wilayah unik (desa) dari tabel wilayah_rtrw
        $semua_wilayah = WilayahRtrw::select('id_kecamatan', 'kecamatan', 'id_desa', 'desa')
                                    ->distinct('id_desa')
                                    ->get();

        if ($semua_wilayah->isEmpty()) {
            $this->command->error('Tabel wilayah_rtrw kosong. Tidak ada data suara yang bisa dibuat.');
            return;
        }

        $this->command->info('Membuat data Suara untuk seluruh kabupaten...');
        $this->command->getOutput()->progressStart($semua_wilayah->count());
        
        // Looping untuk setiap desa
        foreach ($semua_wilayah as $wilayah) {
            
            // Membuat 5 data TPS per desa untuk tahun 2024
            for ($i = 1; $i <= 5; $i++) {
                Suara::create([
                    'tahun' => 2024,
                    'id_kecamatan' => $wilayah->id_kecamatan,
                    'kecamatan' => $wilayah->kecamatan,
                    'id_desa' => $wilayah->id_desa,
                    'desa' => $wilayah->desa,
                    'dprd' => $faker->numberBetween(50, 300),
                    'dpr_prov' => $faker->numberBetween(40, 250),
                    'dpr_ri' => $faker->numberBetween(30, 200),
                    'tps' => 'TPS ' . $i,
                    'sumber' => 'manual input',
                ]);
            }
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Data Suara untuk seluruh kabupaten berhasil dibuat.');
    }
}