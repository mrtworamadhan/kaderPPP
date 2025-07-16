<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\Struktur;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StrukturSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // =================================================================
        // 1. BUAT STRUKTUR DPC (DEWAN PIMPINAN CABANG)
        // =================================================================
        $this->command->info('Membuat data struktur DPC...');
        $strukturDpc = [
            'mp' => ['1', '2', '3', '4'],
            'ksb' => ['1', '2', '3'],
            'Bidang Agama & Kerohanian' => array_fill(0, 5, 'Anggota'),
            'Bidang Kepemudaan & Olahraga' => array_fill(0, 5, 'Anggota'),
            'Bidang Pemberdayaan Perempuan' => array_fill(0, 5, 'Anggota'),
            'Bidang Pelatihan & Usaha Kreatif' => array_fill(0, 5, 'Anggota'),
            'Bidang Pengabdian & Pro Rakyat' => array_fill(0, 5, 'Anggota'),
        ];

        foreach ($strukturDpc as $bagian => $jabatans) {
            $urutan = 1;
            foreach ($jabatans as $jabatan) {
                $this->createStrukturAnggotaUser(
                    faker: $faker,
                    tingkat: 'dpc',
                    bagian: $bagian,
                    jabatan: $jabatan,
                    urutan: $urutan++,
                    id_kecamatan: null, // DPC tidak terikat kecamatan spesifik
                    id_desa: null
                );
            }
        }
        $this->command->info('Struktur DPC selesai dibuat.');


        // =================================================================
        // 2. BUAT STRUKTUR DPRt (DEWAN PIMPINAN RANTING)
        // =================================================================
        $this->command->info('Membuat data struktur DPRt untuk Kecamatan Cibinong...');
        $desaCibinong = [
            43 => 'Pondok Rajeg', 44 => 'Karadenan', 45 => 'Harapan Jaya',
            46 => 'Nanggewer', 47 => 'Nanggewer Mekar', 48 => 'Cibinong',
            49 => 'Pakansari', 50 => 'Tengah', 51 => 'Sukahati',
            52 => 'Ciriung', 53 => 'Ciri Mekar', 54 => 'Pabuaran',
            55 => 'Pabuaran Mekar'
        ];

        $strukturDprt = [
            'mp' => ['1', '2', '3', '4'],
            'ksb' => ['1', '2', '3'],
            'Bidang Agama & Kerohanian' => array_fill(0, 5, 'Anggota'),
            'Bidang Kepemudaan & Olahraga' => array_fill(0, 5, 'Anggota'),
            'Bidang Pemberdayaan Perempuan' => array_fill(0, 5, 'Anggota'),
            'Bidang Pelatihan & Usaha Kreatif' => array_fill(0, 5, 'Anggota'),
            'Bidang Pengabdian & Pro Rakyat' => array_fill(0, 5, 'Anggota'),
        ];
        
        foreach($desaCibinong as $id_desa => $nama_desa) {
            $this->command->line(" > Membuat struktur untuk Ranting: {$nama_desa}");
            foreach ($strukturDprt as $bagian => $jabatans) {
                $urutan = 1;
                foreach ($jabatans as $jabatan) {
                     $this->createStrukturAnggotaUser(
                        faker: $faker,
                        tingkat: 'dprt',
                        bagian: $bagian,
                        jabatan: $jabatan,
                        urutan: $urutan++,
                        id_kecamatan: 3, // ID Kecamatan Cibinong
                        id_desa: $id_desa
                    );
                }
            }
        }
        $this->command->info('Struktur DPRt selesai dibuat.');
    }

    /**
     * Helper function to create Struktur, Anggota, and User
     */
    private function createStrukturAnggotaUser($faker, $tingkat, $bagian, $jabatan, $urutan, $id_kecamatan, $id_desa)
    {
        // Buat data anggota
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

        // Buat data struktur
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

        // Buat data user
        User::create([
            'nik' => $anggota->nik,
            'password' => Hash::make('ekaderapp'),
            'role' => 'anggota',
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
        ]);
    }
}