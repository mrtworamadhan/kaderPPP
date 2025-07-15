<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suara;
use Illuminate\Support\Facades\Validator;

class SuaraController extends Controller
{
    // ✅ Fungsi Index: ambil semua data suara (filter opsional)
    public function index(Request $request)
    {
        $query = Suara::with(['kecamatan', 'desa']);

        if ($request->has('id_kecamatan')) {
            $query->where('id_kecamatan', $request->id_kecamatan);
        }

        if ($request->has('id_desa')) {
            $query->where('id_desa', $request->id_desa);
        }

        if ($request->has('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $data = $query->orderBy('tahun', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    // ✅ Fungsi Store: simpan data suara (hindari duplikat)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer',
            'id_kecamatan' => 'required|exists:daerah,id',
            'id_desa' => 'required|exists:daerah,id',
            'jumlah_suara' => 'required|integer',
            'tps' => 'nullable|string',
            'sumber' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah sudah ada data untuk wilayah dan tahun yang sama
        $exists = Suara::where('tahun', $request->tahun)
            ->where('id_kecamatan', $request->id_kecamatan)
            ->where('id_desa', $request->id_desa)
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data suara untuk wilayah dan tahun ini sudah ada.'
            ], 409);
        }

        // Simpan data
        $suara = Suara::create([
            'tahun' => $request->tahun,
            'id_kecamatan' => $request->id_kecamatan,
            'id_desa' => $request->id_desa,
            'jumlah_suara' => $request->jumlah_suara,
            'tps' => $request->tps,
            'sumber' => $request->sumber ?? 'Manual',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data suara berhasil disimpan',
            'data' => $suara
        ]);
    }

    // ✅ Fungsi Update: update data suara berdasarkan ID
    public function update(Request $request, $id)
    {
        $suara = Suara::find($id);

        if (!$suara) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'jumlah_suara' => 'required|integer',
            'tps' => 'nullable|string',
            'sumber' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $suara->update([
            'jumlah_suara' => $request->jumlah_suara,
            'tps' => $request->tps,
            'sumber' => $request->sumber ?? $suara->sumber,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data suara berhasil diperbarui',
            'data' => $suara
        ]);
    }
}
