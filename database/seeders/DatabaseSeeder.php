<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KabarTerbaruSeeder::class,
            RewardSeeder::class
            // AdminSeeder::class,
            // StrukturLengkapSeeder::class,
            // SuaraKabupatenSeeder::class,
        ]);
    }
}