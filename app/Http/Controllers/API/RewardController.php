<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use App\Models\Reward;

class RewardController extends Controller
{
    use ApiResponder;

    /**
     * Mengambil data untuk halaman rewards kader.
     */
    public function index(Request $request)
    {
        $anggota = $request->user()->anggota;

        if (!$anggota) {
            return $this->error('Profil anggota tidak ditemukan.', 404);
        }

        // Ambil semua hadiah, diurutkan dari poin terkecil
        $rewards = Reward::orderBy('points_needed', 'asc')->get();

        $data = [
            'total_poin_user' => $anggota->total_poin,
            'rewards' => $rewards,
        ];

        return $this->success($data, 'Data halaman rewards berhasil diambil.');
    }
}
