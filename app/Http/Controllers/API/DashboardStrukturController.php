<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponder;

class DashboardStrukturController extends Controller
{
    use ApiResponder;

    public function pencapaian(Request $request)
    {
        $request->validate([
            'sort_by' => 'nullable|in:pencapaian,anggota',
            'id_kecamatan' => 'nullable|integer|exists:daerah,id',
        ]);

        $sortBy = $request->input('sort_by', 'pencapaian');
        $id_kecamatan = $request->input('id_kecamatan');

        if ($id_kecamatan) {
            // Tampilan Detail Per Desa
            $query = $this->getQueryPerDesa($id_kecamatan);
        } else {
            // Tampilan Umum Per Kecamatan
            $query = $this->getQueryPerKecamatan();
        }

        $results = $query->get();

        // Kalkulasi skor dan persentase
        $data = $results->map(function ($item) {
            $item->total_anggota = (int) $item->total_anggota;

            // Kalkulasi Persentase
            $item->persentase_dprt = ($item->target_dprt > 0) ? round(($item->terbentuk_dprt / $item->target_dprt) * 100) : 0;
            $item->persentase_korw = ($item->target_korw > 0) ? round(($item->terbentuk_korw / $item->target_korw) * 100) : 0;
            $item->persentase_kort = ($item->target_kort > 0) ? round(($item->terbentuk_kort / $item->target_kort) * 100) : 0;

            // Kalkulasi Skor Pencapaian (rata-rata dari 3 persentase)
            $item->skor_pencapaian = round(($item->persentase_dprt + $item->persentase_korw + $item->persentase_kort) / 3);

            return $item;
        });

        // Urutkan berdasarkan pilihan filter
        if ($sortBy === 'anggota') {
            $data = $data->sortByDesc('total_anggota')->values();
        } else {
            $data = $data->sortByDesc('skor_pencapaian')->values();
        }

        return $this->success($data, 'Data pencapaian struktur berhasil diambil.');
    }

    private function getQueryPerKecamatan()
    {
        return DB::table('daerah as kecamatan')
            ->where('kecamatan.parent_id', 2)
            ->leftJoin(DB::raw('(SELECT id_kecamatan, COUNT(id) as total_anggota FROM anggota GROUP BY id_kecamatan) as anggota'), 'kecamatan.id', '=', 'anggota.id_kecamatan')
            ->leftJoin(DB::raw('(SELECT id_kecamatan, COUNT(DISTINCT id_desa) as target_dprt, SUM(jumlah_rw) as target_korw, SUM(jumlah_rt) as target_kort FROM wilayah_rtrw GROUP BY id_kecamatan) as target'), 'kecamatan.id', '=', 'target.id_kecamatan')

            // --- INI BAGIAN YANG DIPERBAIKI ---
            ->leftJoin(DB::raw('
            (SELECT
                k.id as id_kecamatan, -- FIX: Seharusnya k.id di-alias-kan sebagai id_kecamatan
                COUNT(DISTINCT s.id_desa) as terbentuk_dprt,
                COUNT(DISTINCT CASE WHEN kw.tingkat = "korw" THEN kw.id END) as terbentuk_korw,
                COUNT(DISTINCT CASE WHEN kw.tingkat = "kort" THEN kw.id END) as terbentuk_kort
            FROM daerah k
            LEFT JOIN struktur s ON k.id = s.id_kecamatan AND s.tingkat = "dprt"
            LEFT JOIN korwil kw ON k.id = kw.id_kecamatan
            WHERE k.parent_id = 2
            GROUP BY k.id) as terbentuk
        '), 'kecamatan.id', '=', 'terbentuk.id_kecamatan')
            // --- BATAS PERBAIKAN ---

            ->select(
                'kecamatan.id as id_wilayah',
                'kecamatan.nama as nama_wilayah',
                DB::raw('COALESCE(target.target_dprt, 0) as target_dprt'),
                DB::raw('COALESCE(terbentuk.terbentuk_dprt, 0) as terbentuk_dprt'),
                DB::raw('COALESCE(target.target_korw, 0) as target_korw'),
                DB::raw('COALESCE(terbentuk.terbentuk_korw, 0) as terbentuk_korw'),
                DB::raw('COALESCE(target.target_kort, 0) as target_kort'),
                DB::raw('COALESCE(terbentuk.terbentuk_kort, 0) as terbentuk_kort'),
                DB::raw('COALESCE(anggota.total_anggota, 0) as total_anggota')
            )
            ->groupBy(
                'kecamatan.id',
                'kecamatan.nama',
                'anggota.total_anggota',
                'target.target_dprt',
                'target.target_korw',
                'target.target_kort',
                'terbentuk.terbentuk_dprt',
                'terbentuk.terbentuk_korw',
                'terbentuk.terbentuk_kort'
            );
    }

    private function getQueryPerDesa($id_kecamatan)
    {
        return DB::table('daerah as desa')
            ->where('desa.parent_id', $id_kecamatan)
            ->leftJoin(DB::raw('(SELECT id_desa, 1 as is_terbentuk FROM struktur WHERE tingkat = "dprt" GROUP BY id_desa) as dprt'), 'desa.id', '=', 'dprt.id_desa')

            // --- PERBAIKAN LOGIKA KORW & KORT DI SINI ---
            ->leftJoin(DB::raw('(SELECT id_desa, COUNT(DISTINCT rw) as terbentuk_korw FROM korwil WHERE tingkat = "korw" GROUP BY id_desa) as korw'), 'desa.id', '=', 'korw.id_desa')
            ->leftJoin(DB::raw('(SELECT id_desa, COUNT(id) as terbentuk_kort FROM korwil WHERE tingkat = "kort" GROUP BY id_desa) as kort'), 'desa.id', '=', 'kort.id_desa')

            ->leftJoin(DB::raw('(SELECT id_desa, COUNT(id) as total_anggota FROM anggota GROUP BY id_desa) as anggota'), 'desa.id', '=', 'anggota.id_desa')
            ->leftJoin('wilayah_rtrw as target_wilayah', 'desa.id', '=', 'target_wilayah.id_desa')
            ->select(
                'desa.id as id_wilayah',
                'desa.nama as nama_wilayah',
                DB::raw('1 as target_dprt'),
                DB::raw('COALESCE(dprt.is_terbentuk, 0) as terbentuk_dprt'),

                DB::raw('COALESCE(target_wilayah.jumlah_rw, 0) as target_korw'),
                DB::raw('COALESCE(korw.terbentuk_korw, 0) as terbentuk_korw'), // <-- Sekarang hasilnya akurat

                DB::raw('COALESCE(target_wilayah.jumlah_rt, 0) as target_kort'),
                DB::raw('COALESCE(kort.terbentuk_kort, 0) as terbentuk_kort'), // <-- KORT tetap count(id)

                DB::raw('COALESCE(anggota.total_anggota, 0) as total_anggota')
            )
            ->groupBy('desa.id', 'desa.nama', 'dprt.is_terbentuk', 'target_wilayah.jumlah_rw', 'korw.terbentuk_korw', 'target_wilayah.jumlah_rt', 'kort.terbentuk_kort', 'anggota.total_anggota');
    }
}