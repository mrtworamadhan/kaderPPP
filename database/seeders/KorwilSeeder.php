<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\Korwil;
use App\Models\User;
use App\Models\WilayahRtRW;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class KorwilSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Data desa di Kecamatan Cibinong
        $desaCibinong = [
            43 => 'Pondok Rajeg', 44 => 'Karadenan', 45 => 'Harapan Jaya',
            46 => 'Nanggewer', 47 => 'Nanggewer Mekar', 48 => 'Cibinong',
            49 => 'Pakansari', 50 => 'Tengah', 51 => 'Sukahati',
            52 => 'Ciriung', 53 => 'Ciri Mekar', 54 => 'Pabuaran',
            55 => 'Pabuaran Mekar'
        ];
        
        $this->command->info('Membuat data KORW & KORT berdasarkan data wilayah_rtrw...');

        foreach ($desaCibinong as $id_desa => $nama_desa) {
            $this->command->line(" > Memproses Desa: {$nama_desa} (ID: {$id_desa})");

            // Ambil data wilayah dari tabel wilayah_rtrw
            $wilayah = WilayahRtRW::where('id_desa', $id_desa)->first();

            if (!$wilayah || $wilayah->jumlah_rw == 0) {
                $this->command->warn("   ! Data wilayah untuk desa {$nama_desa} tidak ditemukan atau jumlah RW nol. Dilewati.");
                continue;
            }
            
            $jumlah_rw = $wilayah->jumlah_rw;
            // Asumsi jumlah RT per RW merata, jika data spesifik tidak ada
            $jumlah_rt_per_rw = ($wilayah->jumlah_rt > 0 && $jumlah_rw > 0) ? floor($wilayah->jumlah_rt / $jumlah_rw) : 4;
            
            $this->command->info("   - Ditemukan: {$jumlah_rw} RW dan {$wilayah->jumlah_rt} RT. Membuat koordinator...");

            // Membuat KORW (Koordinator RW)
            for ($i = 1; $i <= $jumlah_rw; $i++) {
                $rw = str_pad($i, 3, '0', STR_PAD_LEFT);
                $this->createKorwilAnggotaUser($faker, 'korw', $rw, null, 3, $id_desa, $nama_desa);

                // Membuat KORT (Koordinator RT) untuk setiap RW
                for ($j = 1; $j <= $jumlah_rt_per_rw; $j++) {
                    $rt = str_pad($j, 3, '0', STR_PAD_LEFT);
                    $this->createKorwilAnggotaUser($faker, 'kort', $rw, $rt, 3, $id_desa, $nama_desa);
                }
            }
        }
        $this->command->info('Data KORW & KORT selesai dibuat.');
    }

    private function createKorwilAnggotaUser($faker, $tingkat, $rw, $rt, $id_kecamatan, $id_desa, $nama_desa)
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
            'alamat' => "{$nama_desa} RT {$rt} / RW {$rw}",
            'tgl_lahir' => $faker->date(),
            'gender' => $gender,
            'pekerjaan' => $faker->jobTitle,
            'jabatan' => $tingkat,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
            'desa' => $nama_desa,
            'kecamatan' => 'Cibinong'
        ]);
        
        Korwil::create([
            'tingkat' => $tingkat,
            'nik' => $nik,
            'nama' => $nama,
            'phone' => $anggota->phone,
            'rt' => $rt,
            'rw' => $rw,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
            'desa' => $nama_desa,
            'kecamatan' => 'Cibinong'
        ]);

        User::create([
            'nik' => $nik,
            'password' => Hash::make('ekaderapp'),
            'role' => 'anggota',
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);
    }
}