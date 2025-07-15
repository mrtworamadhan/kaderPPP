<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:anggota,nik',
            'nama' => 'required',
            'phone' => 'nullable',
            'alamat' => 'nullable',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'nullable',
            'pekerjaan' => 'nullable',
            'desa' => 'nullable',
            'id_desa' => 'nullable',
            'kecamatan' => 'nullable',
            'id_kecamatan' => 'nullable',
            'jabatan' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();


        // Auto generate no_kta
        $lastId = Anggota::max('id') ?? 0;
        $no_kta = '909013201' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        $anggota = Anggota::create([
            'nik' => $data['nik'],
            'id_anggota' => $no_kta,
            'nama' => $data['nama'],
            'phone' => $data['phone'] ?? '',
            'alamat' => $data['alamat'] ?? '',
            'tgl_lahir' => $data['tgl_lahir'] ?? null,
            'gender' => $data['gender'] ?? '',
            'pekerjaan' => $data['pekerjaan'] ?? '',
            'desa' => $data['desa'] ?? '',
            'id_desa' => $data['id_desa'] ?? '',
            'kecamatan' => $data['kecamatan'] ?? '',
            'id_kecamatan' => $data['id_kecamatan'] ?? '',
            'jabatan' => $data['jabatan'] ?? '',
        ]);

        User::create([
                'nik' => $data['nik'],
                'password' => Hash::make('demokratjuara'),
                'role' => 'anggota',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $anggota,
        ]);
    }

    public function update(Request $request, $id)
    {
        $anggota = Anggota::find($id);

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Data anggota tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:anggota,nik',
            'nama' => 'required',
            'phone' => 'nullable',
            'alamat' => 'nullable',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'nullable',
            'pekerjaan' => 'nullable',
            'desa' => 'nullable',
            'id_desa' => 'nullable',
            'kecamatan' => 'nullable',
            'id_kecamatan' => 'nullable',
            'jabatan' => 'nullable',
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
            'nik' => $data['nik'],
            'nama' => $data['nama'],
            'phone' => $data['phone'] ?? '',
            'alamat' => $data['alamat'] ?? '',
            'tgl_lahir' => $data['tgl_lahir'] ?? null,
            'gender' => $data['gender'] ?? '',
            'pekerjaan' => $data['pekerjaan'] ?? '',
            'desa' => $data['desa'] ?? '',
            'id_desa' => $data['id_desa'] ?? '',
            'kecamatan' => $data['kecamatan'] ?? '',
            'id_kecamatan' => $data['id_kecamatan'] ?? '',
            'jabatan' => $data['jabatan'] ?? '',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil diperbarui',
            'data' => $anggota,
        ]);
    }

}

