<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Struktur;
use App\Models\Korwil;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding data roles (jabatan) versi final...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $allRoles = [];

        // 1. Tambahkan Role Dasar / Fundamental yang baru
        $allRoles['Seluruh Anggota & Kader Partai'] = true;
        $allRoles['Seluruh Tingkatan Pengurus'] = true;

        // 2. Definisikan Jabatan DPC yang sudah diketahui
        // Ini adalah bagian yang nanti bisa dibuat custom oleh admin
        $jabatanDPC = [
            'Ketua DPC', 'Sekretaris DPC', 'Bendahara DPC',
            'Ketua Majelis Pertimbangan Cabang', 'Sekretaris Majelis Pertimbangan Cabang',
            'Anggota Bidang Agama & Kerohanian (DPC)',
            'Anggota Bidang Kepemudaan & Olahraga (DPC)',
            'Anggota Bidang Pemberdayaan Perempuan (DPC)',
            'Anggota Bidang Pelatihan & Usaha Kreatif (DPC)',
            'Anggota Bidang Pengabdian & Pro Rakyat (DPC)',
        ];
        foreach ($jabatanDPC as $roleName) {
            $allRoles[$roleName] = true;
        }

        // 3. Ambil jabatan unik dari DPAC dan DPRt, lalu sederhanakan
        $jabatanStruktur = Struktur::whereIn('tingkat', ['dpac', 'dprt'])->select('tingkat', 'jabatan')->distinct()->get();
        foreach ($jabatanStruktur as $js) {
            $tingkatanLabel = (strtolower($js->tingkat) === 'dpac') ? 'PAC' : 'Ranting';
            // Gabungkan jabatan + tingkatan, e.g., "Ketua PAC", "Sekretaris Ranting"
            $roleName = Str::ucfirst(strtolower($js->jabatan)) . ' ' . $tingkatanLabel;
            $allRoles[$roleName] = true;
        }

        // 4. Ambil tingkatan unik dari tabel 'korwil'
        $jabatanKorwil = Korwil::select('tingkat')->distinct()->get();
        foreach ($jabatanKorwil as $jk) {
            $roleName = strtoupper($jk->tingkat);
            $allRoles[$roleName] = true;
        }

        // 5. Masukkan semua role unik ke database
        $roleCollection = [];
        foreach (array_keys($allRoles) as $roleName) {
            $roleCollection[] = ['name' => $roleName];
        }
        
        Role::insert($roleCollection);

        $this->command->info('Seeding data roles selesai. ' . count($allRoles) . ' role unik telah ditambahkan.');
    }
}
