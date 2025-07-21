<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Daerah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat akun administrator (tanpa profil anggota)...');

        // --- 1. Admin Pusat (DPC) ---
        $this->command->line('> Membuat 3 akun Admin Pusat (DPC)...');
        for ($i = 1; $i <= 3; $i++) {
            $nik = "admin_dpc_{$i}";
            User::firstOrCreate(['nik' => $nik], [
                'password' => Hash::make('password'),
                'role' => 'admin_pusat',
                // anggota_id dibiarkan null
            ]);
        }

        // --- 2. Admin PAC (Per Kecamatan) ---
        $this->command->line('> Membuat akun Admin PAC untuk setiap kecamatan...');
        $kecamatans = Daerah::where('parent_id', 2)->get();
        foreach ($kecamatans as $kecamatan) {
            $nik = 'pac.' . Str::slug($kecamatan->nama, '');
            User::firstOrCreate(['nik' => $nik], [
                'password' => Hash::make('password'),
                'role' => 'admin_pac',
                'id_kecamatan' => $kecamatan->id,
                // anggota_id dibiarkan null
            ]);
        }

        // --- 3. Admin Ranting (Per Desa) ---
        $this->command->line('> Membuat akun Admin Ranting untuk setiap desa...');
        $desas = Daerah::whereNotNull('parent_id')->with('parent')->get();
        foreach ($desas as $desa) {
            if ($desa->parent) {
                $nik = Str::slug($desa->nama, '') . '.' . Str::slug($desa->parent->nama, '');
                User::firstOrCreate(['nik' => $nik], [
                    'password' => Hash::make('password'),
                    'role' => 'admin_desa',
                    'id_kecamatan' => $desa->parent_id,
                    'id_desa' => $desa->id,
                    // anggota_id dibiarkan null
                ]);
            }
        }

        $this->command->info('Semua akun administrator berhasil dibuat.');
    }
}