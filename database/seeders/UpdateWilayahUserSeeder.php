<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateWilayahUserSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/data/id_wilayah.csv');

        if (!file_exists($file)) {
            $this->command->error("File CSV tidak ditemukan: $file");
            return;
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->command->error("Gagal membuka file CSV.");
            return;
        }

        $headers = fgetcsv($handle, 0, ';');

        // Bersihkan karakter BOM dari header pertama
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        $this->command->info("Headers: " . json_encode($headers));

        $updatedCount = 0;
        $skippedCount = 0;
        $lineNumber = 1;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $lineNumber++;
            $this->command->info("Line $lineNumber: " . json_encode($row));

            if (count($row) !== count($headers)) {
                $this->command->warn("Baris $lineNumber tidak sesuai format, dilewati.");
                $skippedCount++;
                continue;
            }

            $data = array_combine($headers, $row);

            if (empty($data['id']) || !is_numeric($data['id'])) {
                $this->command->warn("Baris $lineNumber kolom 'id' kosong atau tidak valid, dilewati.");
                $skippedCount++;
                continue;
            }

            $updateResult = DB::table('users')
                ->where('id', (int) $data['id'])
                ->update([
                    'id_desa' => strtolower(trim($data['id_desa'])) !== 'null' && $data['id_desa'] !== '' ? (int)$data['id_desa'] : null,
                    'id_kecamatan' => strtolower(trim($data['id_kecamatan'])) !== 'null' && $data['id_kecamatan'] !== '' ? (int)$data['id_kecamatan'] : null,
                ]);

            if ($updateResult) {
                $updatedCount++;
            } else {
                $this->command->warn("Baris $lineNumber gagal update. ID {$data['id']} mungkin tidak ada di DB.");
                $skippedCount++;
            }
        }

        fclose($handle);

        $this->command->info("Update wilayah selesai. Total baris update: $updatedCount, dilewati: $skippedCount.");
    }
}
