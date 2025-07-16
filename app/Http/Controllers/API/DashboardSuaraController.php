<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponder;

class DashboardSuaraController extends Controller
{
    use ApiResponder;

    public function perbandingan(Request $request)
    {
        // Validasi input, jika ada filter by wilayah
        $request->validate([
            'id_kecamatan' => 'nullable|integer|exists:daerah,id',
            'id_desa' => 'nullable|integer|exists:daerah,id',
        ]);

        $id_kecamatan = $request->input('id_kecamatan');
        $id_desa = $request->input('id_desa');
        $wilayah_label = "Kabupaten Bogor"; // Default

        // Query untuk mengambil total suara
        $query = DB::table('suara')
            ->select(
                'tahun',
                DB::raw('SUM(dprd) as total_dprd'),
                DB::raw('SUM(dpr_prov) as total_dpr_prov'),
                DB::raw('SUM(dpr_ri) as total_dpr_ri')
            )
            ->groupBy('tahun');

        // Terapkan filter jika ada
        if ($id_desa) {
            $query->where('id_desa', $id_desa);
            $desa = DB::table('daerah')->where('id', $id_desa)->first();
            $wilayah_label = "Desa/Kel. " . ($desa->nama ?? 'N/A');
        } elseif ($id_kecamatan) {
            $query->where('id_kecamatan', $id_kecamatan);
            $kecamatan = DB::table('daerah')->where('id', $id_kecamatan)->first();
            $wilayah_label = "Kec. " . ($kecamatan->nama ?? 'N/A');
        }

        $results = $query->get()->keyBy('tahun');

        // Siapkan data untuk tahun 2019 dan 2024
        $suara2019 = $results->get(2019);
        $suara2024 = $results->get(2024);

        // Fungsi helper untuk menghitung perbandingan
        $calculateComparison = function ($val2019, $val2024) {
            $selisih = $val2024 - $val2019;
            $status = ($selisih >= 0) ? 'naik' : 'turun';
            return ["2019" => (int) $val2019, "2024" => (int) $val2024, "selisih" => $selisih, "status" => $status];
        };

        // Format respons JSON
        $data = [
            "wilayah" => $wilayah_label,
            "data" => [
                "dprd" => $calculateComparison($suara2019->total_dprd ?? 0, $suara2024->total_dprd ?? 0),
                "dpr_prov" => $calculateComparison($suara2019->total_dpr_prov ?? 0, $suara2024->total_dpr_prov ?? 0),
                "dpr_ri" => $calculateComparison($suara2019->total_dpr_ri ?? 0, $suara2024->total_dpr_ri ?? 0),
            ]
        ];

        return $this->success($data, 'Data perbandingan suara berhasil diambil.');
    }

    public function detail(Request $request)
{
    $request->validate([
        'tahun' => 'required|integer',
        'id_kecamatan' => 'nullable|integer|exists:daerah,id',
        'id_desa' => 'nullable|integer|exists:daerah,id',
    ]);

    $tahun = $request->input('tahun');
    $id_kecamatan = $request->input('id_kecamatan');
    $id_desa = $request->input('id_desa');
    
    $query_builder = null;
    $level = '';
    $wilayah_label = '';

    if ($id_desa && $id_kecamatan) {
        $level = 'tps';
        $wilayah_label = "Desa " . DB::table('daerah')->where('id', $id_desa)->value('nama');
        $query_builder = DB::table('suara')
            ->where('tahun', $tahun)
            ->where('id_desa', $id_desa)
            ->select('tps', 'dprd', 'dpr_prov', 'dpr_ri')
            ->orderBy('tps', 'asc');

    } elseif ($id_kecamatan) {
        $level = 'desa';
        $wilayah_label = "Kecamatan " . DB::table('daerah')->where('id', $id_kecamatan)->value('nama');
        $query_builder = DB::table('daerah')
            ->where('daerah.parent_id', $id_kecamatan)
            ->leftJoin('suara', function($join) use ($tahun, $id_kecamatan) {
                $join->on('daerah.id', '=', 'suara.id_desa')
                     ->where('suara.tahun', '=', $tahun)
                     ->where('suara.id_kecamatan', '=', $id_kecamatan);
            })
            ->select(
                DB::raw("$id_kecamatan as id_kecamatan"),
                'daerah.id as id_desa', 
                'daerah.nama as desa', 
                // --- PERBAIKAN DI SINI ---
                DB::raw('COALESCE(SUM(suara.dprd), 0) as total_dprd'),
                DB::raw('COALESCE(SUM(suara.dpr_prov), 0) as total_dpr_prov'),
                DB::raw('COALESCE(SUM(suara.dpr_ri), 0) as total_dpr_ri')
            )
            ->groupBy('daerah.id', 'daerah.nama', 'id_kecamatan')
            ->orderBy('total_dprd', 'desc');

    } else {
        $level = 'kecamatan';
        $wilayah_label = "Kabupaten Bogor";
        $query_builder = DB::table('daerah')
            ->where('daerah.parent_id', 2)
            ->leftJoin('suara', function($join) use ($tahun) {
                $join->on('daerah.id', '=', 'suara.id_kecamatan')
                     ->where('suara.tahun', '=', $tahun);
            })
            ->select(
                'daerah.id as id_kecamatan', 
                'daerah.nama as kecamatan', 
                // --- PERBAIKAN DI SINI JUGA ---
                DB::raw('COALESCE(SUM(suara.dprd), 0) as total_dprd'),
                DB::raw('COALESCE(SUM(suara.dpr_prov), 0) as total_dpr_prov'),
                DB::raw('COALESCE(SUM(suara.dpr_ri), 0) as total_dpr_ri')
            )
            ->groupBy('daerah.id', 'daerah.nama')
            ->orderBy('total_dprd', 'desc');
    }

    $results = $query_builder->get()->map(function($item) {
        foreach ($item as $key => $value) {
            if (is_numeric($value) && !in_array($key, ['id_kecamatan', 'id_desa'])) {
                $item->$key = (int) $value;
            }
        }
        return $item;
    });

    $data = [
        'tahun' => (int)$tahun,
        'wilayah_label' => $wilayah_label,
        'level' => $level,
        'data' => $results
    ];

    return $this->success($data, "Data detail suara level {$level} berhasil diambil.");
}
}