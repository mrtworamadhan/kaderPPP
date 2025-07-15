<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $queryAnggota = DB::table('anggota');
        $queryStruktur = DB::table('struktur');
        $queryKorwil = DB::table('korwil');

        // Filter berdasarkan role dan wilayah
        if ($user->role === 'admin_desa') {
            $queryAnggota->where('id_desa', $user->id_desa);
            $queryStruktur->where('id_desa', $user->id_desa);
            $queryKorwil->where('id_desa', $user->id_desa);
        } elseif ($user->role === 'admin_pac') {
            $queryAnggota->where('id_kecamatan', $user->id_kecamatan);
            $queryStruktur->where('id_kecamatan', $user->id_kecamatan);
            $queryKorwil->where('id_kecamatan', $user->id_kecamatan);
        }
        // Admin pusat: tidak ada filter wilayah

        // === Data Anggota ===
        $total     = $queryAnggota->count();
        // $ber_kta   = (clone $queryAnggota)
        //                 ->whereNotNull('no_kta')
        //                 ->whereRaw("TRIM(no_kta) != ''")
        //                 ->count();
        // $belum_kta = $total - $ber_kta;

        $laki      = (clone $queryAnggota)->whereIn('gender', ['l', 'Laki-laki'])->count();
        $perempuan = (clone $queryAnggota)->whereIn('gender', ['p', 'Perempuan'])->count();

        $umur_19_30 = (clone $queryAnggota)
                        ->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), [19, 30])
                        ->count();
        $umur_31_45 = (clone $queryAnggota)
                        ->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), [31, 45])
                        ->count();
        $umur_46_up = (clone $queryAnggota)
                        ->where(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), '>', 45)
                        ->count();

        // === Struktur Partai ===
        $pac_terbentuk  = (clone $queryStruktur)->where('tingkat', 'DPAC')->distinct()->count('id_kecamatan');
        $dprt_terbentuk = (clone $queryStruktur)->where('tingkat', 'DPRt')->distinct()->count('id_desa');
        $korw_terbentuk = (clone $queryKorwil)->where('tingkat', 'korw')->distinct()->count('id_desa');
        $kort_terbentuk = (clone $queryKorwil)->where('tingkat', 'kort')->distinct()->count('id_desa');

        return response()->json([
            'success' => true,
            'anggota' => [
                'total' => $total,
                // 'ber_kta' => $ber_kta,
                // 'belum_kta' => $belum_kta,
                'laki_laki' => $laki,
                'perempuan' => $perempuan,
                'klasifikasi_umur' => [
                    '19_30' => $umur_19_30,
                    '31_45' => $umur_31_45,
                    '46_up' => $umur_46_up,
                ],
            ],
            'struktur' => [
                'pac_terbentuk' => $pac_terbentuk,
                'dprt_terbentuk' => $dprt_terbentuk,
                'korw_terbentuk' => $korw_terbentuk,
                'kort_terbentuk' => $kort_terbentuk,
            ]
        ]);
    }
}
