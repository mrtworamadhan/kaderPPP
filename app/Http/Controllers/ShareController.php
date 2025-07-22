<?php

namespace App\Http\Controllers;

use App\Models\KabarTerbaru;
use App\Models\Anggota;
use App\Models\PoinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShareController extends Controller
{
    public function redirectAndTrack($share_code, $anggota_id)
    {
        // 1. Cari konten berdasarkan kode unik
        $konten = KabarTerbaru::where('share_code', $share_code)->where('status', 'aktif')->first();

        // 2. Cari anggota yang me-share
        $anggota = Anggota::find($anggota_id);

        // Jika konten atau anggota tidak ditemukan, redirect saja tanpa memberi poin
        if (!$konten || !$anggota || empty($konten->url_target)) {
            // Redirect ke halaman utama jika link tidak valid
            return redirect('/');
        }

        // 3. Beri poin (jika ada)
        if ($konten->points_per_click > 0) {
            try {
                DB::beginTransaction();
                // Tambah poin ke anggota
                $anggota->increment('total_poin', $konten->points_per_click);
                // Catat di log poin
                PoinLog::create([
                    'anggota_id' => $anggota->id,
                    'points' => $konten->points_per_click,
                    'description' => 'Poin dari share konten: ' . $konten->judul,
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // Jika gagal, tidak apa-apa, tetap redirect
            }
        }

        // 4. Redirect pengguna ke URL target
        return redirect()->away($konten->url_target);
    }
}
