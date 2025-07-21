<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\User;
use App\Models\Struktur;
use App\Models\Korwil;
use App\Models\Daerah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StrukturLengkapSeeder extends Seeder
{
    private $faker;

    public function run(): void
    {
        $this->faker = Faker::create('id_ID');

        // Mengosongkan tabel terkait untuk memastikan data bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Struktur::truncate();
        Korwil::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Membuat data struktur dan korwil...');

        // Menggunakan nama jabatan deskriptif
        $strukturJabatan = [
            'MP' => ['Ketua', 'Sekretaris', 'Anggota', 'Anggota'],
            'KSB' => ['Ketua', 'Sekretaris', 'Bendahara'],
            'Bidang Agama & Kerohanian' => array_fill(0, 5, 'Anggota'),
            'Bidang Kepemudaan & Olahraga' => array_fill(0, 5, 'Anggota'),
            'Bidang Pemberdayaan Perempuan' => array_fill(0, 5, 'Anggota'),
            'Bidang Pelatihan & Usaha Kreatif' => array_fill(0, 5, 'Anggota'),
            'Bidang Pengabdian & Pro Rakyat' => array_fill(0, 5, 'Anggota'),
        ];

        // Mengambil 10 kecamatan acak untuk dibuatkan strukturnya
        $kecamatans = Daerah::where('parent_id', 2)->inRandomOrder()->take(10)->get();

        foreach ($kecamatans as $kecamatan) {
            $this->command->warn(">> Memproses Kecamatan: {$kecamatan->nama}");

            // 1. Membuat Struktur DPAC
            foreach ($strukturJabatan as $bagian => $jabatans) {
                $urutan = 1;
                foreach ($jabatans as $jabatan) {
                    $anggota = $this->createAnggotaUser($kecamatan->id, null);
                    Struktur::create([
                        'anggota_id' => $anggota->id,
                        'tingkat' => 'dpac',
                        'jabatan' => $jabatan,
                        'bagian' => $bagian,
                        'urutan' => $urutan++,
                        'id_kecamatan' => $kecamatan->id,
                    ]);
                }
            }

            // 2. Mengambil 5 desa acak di kecamatan ini
            $desas = Daerah::where('parent_id', $kecamatan->id)->inRandomOrder()->take(5)->get();

            foreach ($desas as $desa) {
                $this->command->line("   -> Memproses Desa: {$desa->nama}");

                // 2a. Membuat Struktur DPRt
                foreach ($strukturJabatan as $bagian => $jabatans) {
                     $urutan = 1;
                    foreach ($jabatans as $jabatan) {
                        $anggota = $this->createAnggotaUser($kecamatan->id, $desa->id);
                        Struktur::create([
                            'anggota_id' => $anggota->id,
                            'tingkat' => 'dprt',
                            'jabatan' => $jabatan,
                            'bagian' => $bagian,
                            'urutan' => $urutan++,
                            'id_kecamatan' => $kecamatan->id,
                            'id_desa' => $desa->id,
                        ]);
                    }
                }

                // 2b. Membuat KORW & KORT
                for ($i = 1; $i <= 5; $i++) { // Buat 5 KORW
                    $rw = str_pad($i, 3, '0', STR_PAD_LEFT);
                    $anggotaKorw = $this->createAnggotaUser($kecamatan->id, $desa->id);
                    Korwil::create([
                        'anggota_id' => $anggotaKorw->id,
                        'tingkat' => 'korw', 'rw' => $rw,
                        'id_kecamatan' => $kecamatan->id, 'id_desa' => $desa->id,
                    ]);

                    for ($j = 1; $j <= 3; $j++) { // Buat 3 KORT per KORW
                        $rt = str_pad($j, 3, '0', STR_PAD_LEFT);
                        $anggotaKort = $this->createAnggotaUser($kecamatan->id, $desa->id);
                        Korwil::create([
                            'anggota_id' => $anggotaKort->id,
                            'tingkat' => 'kort', 'rw' => $rw, 'rt' => $rt,
                            'id_kecamatan' => $kecamatan->id, 'id_desa' => $desa->id,
                        ]);
                    }
                }
            }
        }

        $this->command->info('Seeding data struktur dan korwil selesai.');
    }

    /**
     * Helper function untuk membuat Anggota dan User baru.
     * Ini adalah inti dari konsistensi data.
     */
    private function createAnggotaUser($id_kecamatan, $id_desa)
    {
        $gender = $this->faker->randomElement(['l', 'p']);
        $nama = $this->faker->name($gender == 'l' ? 'male' : 'female');
        $nik = $this->faker->unique()->numerify('3201##############');

        // 1. Buat Anggota dulu
        $anggota = Anggota::create([
            'nik' => $nik,
            'nama' => $nama,
            'phone' => $this->faker->phoneNumber,
            'id_kecamatan' => $id_kecamatan,
            'id_desa' => $id_desa,
            'gender' => $gender,
        ]);

        // 2. Generate dan simpan id_anggota (Nomor KTA)
        $anggota->id_anggota = '909013201' . str_pad($anggota->id, 7, '0', STR_PAD_LEFT);
        $anggota->save();

        // 3. Buat User berdasarkan data Anggota
        User::create([
            'nik' => $nik,
            'password' => Hash::make('ekaderapp'),
            'role' => 'anggota',
            'anggota_id' => $anggota->id,
        ]);

        return $anggota;
    }
}
