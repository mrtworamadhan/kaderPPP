<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponder;
use App\Models\Anggota;

class DashboardAnggotaController extends Controller
{
    use ApiResponder;

    /**
     * Menghasilkan data statistik umum anggota.
     */
    public function statistik()
    {
        $totalAnggota = Anggota::count();

        $gender = Anggota::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender');

        // --- PERUBAHAN STATISTIK USIA ---
        $usia = Anggota::select(DB::raw('
            CASE
                WHEN TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) < 35 THEN "muda"
                WHEN TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) >= 35 THEN "senior"
                ELSE "lainnya"
            END as kelompok_usia
        '), DB::raw('count(*) as total'))
            ->whereNotNull('tgl_lahir')
            ->groupBy('kelompok_usia')
            ->pluck('total', 'kelompok_usia');

        $totalUsia = ($usia['muda'] ?? 0) + ($usia['senior'] ?? 0);

        $data = [
            'total_anggota' => $totalAnggota,
            'gender' => [
                'laki_laki' => $gender['l'] ?? 0,
                'perempuan' => $gender['p'] ?? 0,
            ],
            'kelompok_usia' => [
                'muda' => $usia['muda'] ?? 0,
                'senior' => $usia['senior'] ?? 0,
                'persentase_muda' => ($totalUsia > 0) ? round((($usia['muda'] ?? 0) / $totalUsia) * 100) : 0,
                'persentase_senior' => ($totalUsia > 0) ? round((($usia['senior'] ?? 0) / $totalUsia) * 100) : 0,
            ]
        ];

        return $this->success($data, 'Data statistik anggota berhasil diambil.');
    }

    /**
     * Mencari anggota berdasarkan NIK atau Nama dengan pagination.
     */
    // app/Http/Controllers/API/DashboardAnggotaController.php

    public function cari(Request $request)
    {
        $keyword = $request->input('keyword');
        
        // Mulai query dari model Anggota
        $query = Anggota::query();

        // Gabungkan dengan tabel daerah untuk kecamatan dan desa menggunakan LEFT JOIN
        // Ini penting agar anggota yang tidak punya id_kecamatan/id_desa tetap muncul
        $query->leftJoin('daerah as kecamatan', function($join) {
            $join->on('anggota.id_kecamatan', '=', 'kecamatan.id');
        });
        $query->leftJoin('daerah as desa', function($join) {
            $join->on('anggota.id_desa', '=', 'desa.id');
        });

        // Logika untuk menyusun kolom 'wilayah_lengkap' langsung di SQL
        $query->select(
            'anggota.nik',
            'anggota.nama',
            'anggota.phone',
            'anggota.jabatan',
            DB::raw('
                CASE
                    WHEN UPPER(anggota.jabatan) = "DPC" THEN "Kabupaten Bogor"
                    WHEN desa.nama IS NOT NULL AND kecamatan.nama IS NOT NULL THEN CONCAT("Desa ", desa.nama, ", Kec. ", kecamatan.nama)
                    WHEN kecamatan.nama IS NOT NULL THEN CONCAT("Kec. ", kecamatan.nama)
                    ELSE "-"
                END as wilayah_lengkap
            ')
        );

        // Terapkan filter pencarian jika ada
        if ($keyword && strlen($keyword) >= 3) {
            $request->validate(['keyword' => 'string|min:3']);
            $query->where(function ($q) use ($keyword) {
                $q->where('anggota.nik', 'LIKE', "%{$keyword}%")
                  ->orWhere('anggota.nama', 'LIKE', "%{$keyword}%");
            });
        }

        // Lakukan pagination
        $results = $query->orderBy('anggota.nama', 'asc')->paginate(25);

        return $this->success($results, 'Data anggota berhasil diambil.');
    }
}