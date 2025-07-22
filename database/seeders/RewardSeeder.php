<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data contoh untuk Rewards...');

        // Kosongkan tabel sebelum mengisi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Reward::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rewards = [
            [
                'name' => 'Uang Tunai Rp 500.000',
                'description' => 'Tukarkan poin Anda dengan uang tunai, langsung ditransfer.',
                'points_needed' => 1000,
                'kuota' => 20, // Hanya untuk 50 kader pertama
            ],
            [
                'name' => 'Smartphone Keren',
                'description' => 'Dapatkan smartphone terbaru untuk menunjang aktivitas Anda.',
                'points_needed' => 5000,
                'kuota' => 5,
            ],
            [
                'name' => 'Paket Umroh',
                'description' => 'Hadiah utama bagi kader paling aktif dan berdedikasi.',
                'points_needed' => 30000,
                'kuota' => 1,
            ],
            [
                'name' => 'Pulsa Rp 100.000',
                'description' => 'Tukar poin dengan pulsa untuk semua operator.',
                'points_needed' => 250,
                'kuota' => -1, // -1 berarti kuota tidak terbatas
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }

        $this->command->info('Data contoh Rewards berhasil dibuat.');
    }
}
