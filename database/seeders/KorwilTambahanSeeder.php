<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;
use App\Models\Korwil;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class KorwilTambahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Cari semua desa unik yang sudah memiliki struktur DPRt
        $desaDenganStruktur = DB::table('struktur')
                                ->where('tingkat', 'dprt')
                                ->select('id_desa', 'id_kecamatan')
                                ->distinct()
                                ->get();

        if ($desaDenganStruktur->isEmpty()) {
            $this->command->error('Tidak ditemukan desa dengan struktur DPRt. Tidak ada data korwil yang dibuat.');
            return;
        }

        $this->command->info("Ditemukan {$desaDenganStruktur->count()} desa dengan struktur DPRt. Memulai pembuatan korwil...");

        // 2. Loop setiap desa untuk membuat korwil
        foreach ($desaDenganStruktur as $desa) {
            $this->command->line("> Membuat korwil untuk Desa ID: {$desa->id_desa} di Kecamatan ID: {$desa->id_kecamatan}");

            // 3. Buat 5 KORW (Koordinator RW) per desa
            for ($i = 1; $i <= 5; $i++) {
                $nomorRW = str_pad($i, 3, '0', STR_PAD_LEFT);
                $korw = $this->createKorwilAnggotaUser($faker, 'korw', $nomorRW, null, $desa->id_kecamatan, $desa->id_desa);
                
                // 4. Buat 3 KORT (Koordinator RT) untuk setiap KORW
                for ($j = 1; $j <= 3; $j++) {
                    $nomorRT = str_pad($j, 3, '0', STR_PAD_LEFT);
                    $this->createKorwilAnggotaUser($faker, 'kort', $korw->rw, $nomorRT, $desa->id_kecamatan, $desa->id_desa);
                }
            }
        }
        $this->command->info("Selesai membuat data korwil tambahan.");
    }

    private function createKorwilAnggotaUser($faker, $tingkat, $rw, $rt, $id_kecamatan, $id_desa)
    {
        $gender = $faker->randomElement(['l', 'p']);
        $nama = $faker->name($gender == 'l' ? 'male' : 'female');
        $nik = $faker->unique()->numerify('3201##############');
        $lastAnggotaId = Anggota::max('id') ?? 0;

        $anggota = Anggota::create([
            'nik' => $nik,
            'id_anggota' => '909013201' . str_pad($lastAnggotaId + 1, 7, '0', STR_PAD_LEFT),
            'nama' => $nama,
            'phone' => $faker->phoneNumber,
            'alamat' => $faker->streetAddress,
            'tgl_lahir' => $faker->date(),
            'gender' => $gender,
            'pekerjaan' => $faker->jobTitle,
            'jabatan' => $tingkat,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);

        $korwil = Korwil::create([
            'tingkat' => $tingkat,
            'nik' => $nik,
            'nama' => $nama,
            'phone' => $anggota->phone,
            'rt' => $rt,
            'rw' => $rw,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);

        User::create([
            'nik' => $nik,
            'password' => Hash::make('ekaderapp'),
            'role' => 'anggota',
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);
        
        // Mengembalikan model korwil agar bisa diambil nomor RW-nya
        return $korwil;
    }
}