<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KetuaBakomstraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nik' => 'ketuadpc',
            'password' => Hash::make('demokratbogorjuara'),
            'role' => 'admin',
        ]);
        User::create([
            'nik' => 'bakomstra',
            'password' => Hash::make('demokratbogorjuara'),
            'role' => 'admin',
        ]);
    }
}
