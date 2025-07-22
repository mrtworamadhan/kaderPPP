<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use App\Models\Anggota;

class LeaderboardController extends Controller
{
    use ApiResponder;

    /**
     * Mengambil daftar peringkat kader berdasarkan total poin.
     */
    public function index(Request $request)
    {
        // Validasi input untuk filter jumlah tampilan
        $request->validate([
            'limit' => 'nullable|integer|in:10,15,20,25',
        ]);

        // Tentukan jumlah item per halaman, default 10
        $limit = $request->input('limit', 10);

        $leaderboard = Anggota::select('id', 'nama', 'foto', 'total_poin')
            ->orderBy('total_poin', 'desc')
            ->orderBy('nama', 'asc') // Urutan kedua jika poin sama
            ->paginate($limit);

        return $this->success($leaderboard, 'Data leaderboard berhasil diambil.');
    }
}
