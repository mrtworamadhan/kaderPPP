<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Daerah;
use Illuminate\Http\Request;

class DaerahController extends Controller
{
    // Ambil semua kecamatan (parent_id = 2)
    public function getKecamatan()
    {
        $kecamatan = Daerah::where('parent_id', 2)->get();

        return response()->json([
            'success' => true,
            'data' => $kecamatan
        ]);
    }

    // Ambil semua desa berdasarkan id kecamatan
    public function getDesa($id_kecamatan)
    {
        $desa = Daerah::where('parent_id', $id_kecamatan)->get();

        return response()->json([
            'success' => true,
            'data' => $desa
        ]);
    }
}
