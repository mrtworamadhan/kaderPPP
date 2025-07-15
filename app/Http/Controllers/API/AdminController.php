<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Struktur;
use App\Models\Anggota;
use App\Models\Suara;

class AdminController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $query = User::query();

        if ($authUser->role === 'admin_pac') {
            $query->where('id_kecamatan', $authUser->id_kecamatan);
        } elseif ($authUser->role === 'admin_desa') {
            $query->where('id_desa', $authUser->id_desa);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    public function updateStruktur(Request $request)
    {
        $request->validate([
            'struktur' => 'required|array',
            'struktur.*.nik' => 'required|digits:16|exists:anggota,nik',
            'struktur.*.jabatan' => 'required|string',
        ]);

        foreach ($request->struktur as $data) {
            $anggota = Anggota::where('nik', $data['nik'])->first();

            if ($anggota) {
                Struktur::updateOrCreate(
                    ['id_anggota' => $anggota->id],
                    [
                        'jabatan' => $data['jabatan'],
                        'id_desa' => $anggota->id_desa,
                        'id_kecamatan' => $anggota->id_kecamatan,
                    ]
                );
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Struktur diperbarui']);
    }
    public function inputSuara(Request $request)
    {
        $request->validate([
            'id_anggota' => 'required|exists:anggota,id',
            'jumlah_suara' => 'required|integer|min:0',
        ]);

        $user = auth()->user();

        $suara = Suara::create([
            'id_anggota' => $request->id_anggota,
            'jumlah_suara' => $request->jumlah_suara,
            'id_desa' => $user->id_desa,
            'id_kecamatan' => $user->id_kecamatan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data suara berhasil disimpan',
            'data' => $suara,
        ]);
    }


}
