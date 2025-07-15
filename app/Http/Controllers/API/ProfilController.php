<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    public function profil(Request $request)
    {
        try {
            $user = $request->user()->load('anggota');

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Data profil berhasil diambil',
                'data_user'     => [
                    'id'     => $user->id,
                    'nik'    => $user->nik,
                    'role'   => $user->role,
                    'profil' => $user->anggota, // Data lengkap dari tabel anggota
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Profil Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Terjadi kesalahan saat mengambil profil',
            ], 500);
        }
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();

        // Ambil data anggota berdasarkan NIK user
        $anggota = Anggota::where('nik', $user->nik)->first();

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Data anggota tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'phone' => 'nullable',
            'alamat' => 'nullable',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'nullable',
            'pekerjaan' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        $anggota->update([
            'nama' => $data['nama'],
            'phone' => $data['phone'] ?? '',
            'alamat' => $data['alamat'] ?? '',
            'tgl_lahir' => $data['tgl_lahir'] ?? null,
            'gender' => $data['gender'] ?? '',
            'pekerjaan' => $data['pekerjaan'] ?? '',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $anggota,
        ]);
    }

}
