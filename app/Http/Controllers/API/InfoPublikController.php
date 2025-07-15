<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ArahanKetua;
use App\Models\KabarTerbaru;

class InfoPublikController extends Controller
{
    public function arahanKetua()
    {
        $data = ArahanKetua::orderBy('tanggal', 'desc')->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Belum ada arahan ketua'
            ]);
        }

        return response()->json([
            'status' => true,
            'tanggal' => $data->tanggal->format('d-m-Y'),
            'arahan' => $data->arahan,
        ]);
    }

    public function arahanStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'arahan' => 'required|string',
        ]);

        ArahanKetua::create([
            'tanggal' => $request->tanggal,
            'arahan' => $request->arahan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Arahan berhasil disimpan.',
        ]);
    }

    public function kabarTerbaru()
    {
        $data = KabarTerbaru::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'status' => true,
            'data' => $data->map(function ($item) {
                return [
                    'deskripsi' => $item->deskripsi,
                    'link' => $item->link,
                    'gambar' => asset($item->gambar),
                ];
            })
        ]);
    }

    public function kabarStore(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string',
            'link' => 'required|url',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan gambar ke public/kabar
        $file = $request->file('gambar');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('kabar'), $filename);

        KabarTerbaru::create([
            'deskripsi' => $request->deskripsi,
            'link' => $request->link,
            'gambar' => 'kabar/' . $filename,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Kabar berhasil disimpan.',
        ]);
    }

}

