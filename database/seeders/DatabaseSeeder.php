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
            AdminSeeder::class,
            StrukturSeeder::class,
            KorwilSeeder::class,
            SuaraSeeder::class,
        ]);
    }
}