<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use App\Models\Reward;
use App\Models\ArahanKetua;
use App\Models\Event;
use App\Models\KabarTerbaru;
use App\Models\Role;
use App\Models\Struktur;
use App\Models\Korwil;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KaderDashboardController extends Controller
{
    use ApiResponder;

    /**
     * Mengambil semua data yang dibutuhkan untuk dashboard kader.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $anggota = $user->anggota;

        if (!$anggota) {
            return $this->error('Profil anggota tidak ditemukan.', 404);
        }

        // Section A & B: KTA dan Poin/Rewards
        $ktaData = [
            'nama' => $anggota->nama,
            'id_anggota' => $anggota->id_anggota,
            'foto' => $anggota->foto,
            'total_poin' => $anggota->total_poin,
        ];
        $rewards = Reward::orderBy('points_needed', 'asc')->get();

        // Section C: Arahan Ketua Terbaru
        $arahanKetua = ArahanKetua::latest()->first();

        // Section D: Agenda Terdekat
        $agendaTerdekat = $this->getAgendaTerdekat($anggota->id);

        // Section E: Kabar Terbaru dengan Link Affiliate
        $kabarTerbaru = KabarTerbaru::where(function ($query) {
            $query->whereNull('share_expires_at') // 1. Ambil yang tidak punya tgl kedaluwarsa
                ->orWhere('share_expires_at', '>=', Carbon::now()); // 2. ATAU ambil yang belum kedaluwarsa
        })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($kabar) use ($anggota) {
                if ($kabar->share_code) {
                    $kabar->share_link = route('share.redirect', [
                        'share_code' => $kabar->share_code,
                        'anggota_id' => $anggota->id,
                    ]);
                }
                return $kabar;
            });

        // Gabungkan semua data
        $dashboardData = [
            'kta' => $ktaData,
            'rewards' => $rewards,
            'arahan_ketua' => $arahanKetua,
            'agenda_terdekat' => $agendaTerdekat,
            'kabar_terbaru' => $kabarTerbaru,
        ];

        return $this->success($dashboardData, 'Data dashboard berhasil diambil.');
    }

    /**
     * Helper function untuk mengambil satu agenda terdekat yang relevan.
     */
    private function getAgendaTerdekat($anggotaId)
    {
        $userRoleNames = [];
        $userRoleNames['Seluruh Anggota & Kader Partai'] = true;

        $strukturJabatan = Struktur::where('anggota_id', $anggotaId)->get();
        if ($strukturJabatan->isNotEmpty()) {
            $userRoleNames['Seluruh Tingkatan Pengurus'] = true;
            foreach ($strukturJabatan as $struktur) {
                $tingkatanLabel = (strtolower($struktur->tingkat) === 'dpac') ? 'PAC' : 'Ranting';
                $roleName = Str::ucfirst(strtolower($struktur->jabatan)) . ' ' . $tingkatanLabel;
                $userRoleNames[$roleName] = true;
            }
        }

        $korwilJabatan = Korwil::where('anggota_id', $anggotaId)->get();
        if ($korwilJabatan->isNotEmpty()) {
            $userRoleNames['Seluruh Tingkatan Pengurus'] = true;
            foreach ($korwilJabatan as $korwil) {
                $userRoleNames[strtoupper($korwil->tingkat)] = true;
            }
        }

        $roleIds = Role::whereIn('name', array_keys($userRoleNames))->pluck('id');

        return Event::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds);
        })
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->first();
    }
}
