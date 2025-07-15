<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $queryAnggota = DB::table('anggota');
            $queryStruktur = DB::table('struktur');
            $queryKorwil = DB::table('korwil');
            $queryRTRW = DB::table('wilayah_rtrw');
    
            // Filter berdasarkan role dan wilayah
            if ($user->role === 'admin_desa') {
                $queryAnggota->where('id_desa', $user->id_desa);
                $queryStruktur->where('id_desa', $user->id_desa);
                $queryKorwil->where('id_desa', $user->id_desa);
                $queryRTRW->where('id_desa', $user->id_desa);
            } elseif ($user->role === 'admin_pac') {
                $queryAnggota->where('id_kecamatan', $user->id_kecamatan);
                $queryStruktur->where('id_kecamatan', $user->id_kecamatan);
                $queryKorwil->where('id_kecamatan', $user->id_kecamatan);
                $queryRTRW->where('id_kecamatan', $user->id_kecamatan);
            }
        
            // === Data Anggota ===
            $total = (clone $queryAnggota)->count();
            $laki = (clone $queryAnggota)->whereIn('gender', ['l', 'Laki-laki'])->count();
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
    
            // === Data Wilayah RT/RW ===
            $total_rw = (clone $queryRTRW)->sum('jumlah_rw');
            $total_rt = (clone $queryRTRW)->sum('jumlah_rt');
            $total_desa = (clone $queryRTRW)->distinct()->count('id_desa');

            $data = [
                'anggota' => [
                    'total' => $total,
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
                ],
                'wilayah' => [
                    'total_desa' => $total_desa,
                    'total_rw' => (int) $total_rw,
                    'total_rt' => (int) $total_rt,
                ],
            ];

            return $this->success($data, 'Data dashboard berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memuat data dashboard.', 500);
        }
    }
}