<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KabarTerbaru;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KabarTerbaruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data contoh untuk Kabar Terbaru...');
        $faker = Faker::create('id_ID');

        // Kosongkan tabel sebelum mengisi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KabarTerbaru::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat 20 berita contoh
        for ($i = 0; $i < 20; $i++) {
            $isShareable = $faker->boolean(70); // 70% kemungkinan berita ini bisa di-share

            KabarTerbaru::create([
                'deskripsi' => $faker->sentence(6),
                'gambar' => 'https://placehold.co/600x400/28a745/FFFFFF?text=Berita+' . ($i + 1),
                
                // Kolom untuk fitur affiliate
                'url_target' => $isShareable ? 'https://www.instagram.com/' : null,
                'points_per_click' => $isShareable ? $faker->randomElement([5, 10, 15]) : 0,
                'share_code' => $isShareable ? Str::random(6) : null,
                'share_expires_at' => $isShareable ? Carbon::now()->addDays(7) : null, // Poin affiliate berlaku 7 hari
            ]);
        }
        
        $this->command->info('Data contoh Kabar Terbaru berhasil dibuat.');
    }
}
