<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Struktur;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class StrukturController extends Controller
{
    public function store(Request $request)
    {
        $data_array = $request->input('data', []);

        if (!is_array($data_array)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
            ], 422);
        }

        $inserted = 0;

        foreach ($data_array as $item) {
            // Cari anggota berdasarkan NIK
            $anggota = Anggota::where('nik', $item['nik'])->first();

            // Jika belum ada, buat data anggota dan user login-nya
            if (!$anggota) {
                $lastId = Anggota::max('id') ?? 0;
                $no_kta = '909013201' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

                $anggota = Anggota::create([
                    'nik' => $item['nik'],
                    'id_anggota' => $no_kta,
                    'nama' => $item['nama'],
                    'desa' => null,
                    'id_desa' => null,
                    'kecamatan' => null,
                    'id_kecamatan' => null,
                    'jabatan' => $item['tingkat'] ?? null,
                ]);

                User::create([
                    'nik' => $item['nik'],
                    'password' => Hash::make('demokratjuara'),
                    'role' => 'anggota',
                ]);
            } else {
                // Jika anggota sudah ada, update jabatannya jika perlu
                $anggota->update([
                    'jabatan' => $item['tingkat'] ?? $anggota->jabatan,
                ]);
            }

            // Simpan struktur baru
            Struktur::create([
                'tingkat' => $item['tingkat'] ?? '',
                'nik' => $item['nik'] ?? '',
                'nama' => $item['nama'] ?? '',
                'jabatan' => $item['jabatan'] ?? '',
                'bagian' => $item['bagian'] ?? '',
                'urutan' => $item['urutan'] ?? 0,
                'desa' => $item['desa'] ?? '',
                'id_desa' => $item['id_desa'] ?? null,
                'kecamatan' => $item['kecamatan'] ?? '',
                'id_kecamatan' => $item['id_kecamatan'] ?? null,
            ]);

            $inserted++;
        }

        return response()->json([
            'success' => true,
            'message' => "$inserted data berhasil disimpan"
        ]);
    }

    public function byKecamatan($id)
    {
        $struktur = Struktur::with(['anggota', 'kecamatan', 'desa'])
            ->where('id_kecamatan', $id)
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $struktur
        ]);
    }
    public function byDesa($id)
    {
        $struktur = Struktur::with(['anggota', 'kecamatan', 'desa'])
            ->where('id_desa', $id)
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $struktur
        ]);
    }

}
