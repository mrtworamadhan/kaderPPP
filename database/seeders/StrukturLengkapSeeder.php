<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\Struktur;
use App\Models\User;
use App\Models\Daerah; // Kita akan pakai model Daerah
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StrukturLengkapSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Struktur Jabatan yang akan digunakan berulang
        $strukturJabatan = [
            'MP' => ['Ketua', 'Sekretaris', 'Anggota', 'Anggota'],
            'KSB' => ['Ketua', 'Sekretaris', 'Bendahara'],
            'Bidang Agama & Kerohanian' => array_fill(0, 5, 'Anggota'),
            'Bidang Kepemudaan & Olahraga' => array_fill(0, 5, 'Anggota'),
            'Bidang Pemberdayaan Perempuan' => array_fill(0, 5, 'Anggota'),
            'Bidang Pelatihan & Usaha Kreatif' => array_fill(0, 5, 'Anggota'),
            'Bidang Pengabdian & Pro Rakyat' => array_fill(0, 5, 'Anggota'),
        ];

        // 1. Ambil 10 Kecamatan secara acak
        // parent_id = 2 biasanya adalah ID untuk Kabupaten Bogor
        $kecamatans = Daerah::where('parent_id', 2)->inRandomOrder()->take(10)->get();

        $this->command->info("Memulai pembuatan struktur untuk 10 DPAC dan 50 DPRt...");

        foreach ($kecamatans as $kecamatan) {
            $this->command->warn(">> Membuat Struktur DPAC untuk: {$kecamatan->nama}");

            // 2. Buat Struktur DPAC untuk setiap kecamatan
            foreach ($strukturJabatan as $bagian => $jabatans) {
                $urutan = 1;
                foreach ($jabatans as $jabatan) {
                    $this->createStrukturAnggotaUser(
                        $faker, 'dpac', $bagian, $jabatan, $urutan++, $kecamatan->id, null
                    );
                }
            }

            // 3. Ambil 5 Desa/Ranting acak dari kecamatan tersebut
            $desas = Daerah::where('parent_id', $kecamatan->id)->inRandomOrder()->take(5)->get();
            
            if ($desas->isEmpty()) {
                $this->command->line("   -> Kecamatan {$kecamatan->nama} tidak memiliki data desa, dilewati.");
                continue;
            }

            foreach ($desas as $desa) {
                $this->command->info("   -> Membuat Struktur DPRt untuk: {$desa->nama}");

                // 4. Buat Struktur DPRt untuk setiap desa terpilih
                foreach ($strukturJabatan as $bagian => $jabatans) {
                    $urutan = 1;
                    foreach ($jabatans as $jabatan) {
                        $this->createStrukturAnggotaUser(
                            $faker, 'dprt', $bagian, $jabatan, $urutan++, $kecamatan->id, $desa->id
                        );
                    }
                }
            }
        }
        
        $this->command->info("Pembuatan struktur lengkap selesai.");
    }

    private function createStrukturAnggotaUser($faker, $tingkat, $bagian, $jabatan, $urutan, $id_kecamatan, $id_desa)
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
            'alamat' => $faker->address,
            'tgl_lahir' => $faker->date(),
            'gender' => $gender,
            'pekerjaan' => $faker->jobTitle,
            'jabatan' => $tingkat,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);

        Struktur::create([
            'tingkat' => $tingkat,
            'nik' => $anggota->nik,
            'nama' => $anggota->nama,
            'jabatan' => $jabatan,
            'bagian' => $bagian,
            'urutan' => $urutan,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);

        User::create([
            'nik' => $anggota->nik,
            'password' => Hash::make('ekaderapp'),
            'role' => 'anggota',
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);
    }
}