<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArahanKetua;
use App\Models\KabarTerbaru;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // <-- Gunakan HTTP Client bawaan Laravel
use Symfony\Component\DomCrawler\Crawler; // <-- Gunakan DomCrawler

class ContentController extends Controller
{
    /**
     * Menampilkan halaman form untuk membuat konten baru.
     */
    public function create()
    {
        return view('admin.content_create');
    }

    /**
     * Menyimpan Arahan Ketua baru.
     */
    public function storeArahan(Request $request)
    {
        $request->validate([
            // 'judul' => 'required|string|max:255',
            'arahan' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        ArahanKetua::create($request->all());

        return back()->with('success', 'Arahan Ketua berhasil disimpan!');
    }

    /**
     * Menyimpan Kabar Terbaru baru.
     */
    public function storeKabar(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            // 'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // 'status' => 'required|in:aktif,tidak aktif',
            'url_target' => 'nullable|url',
            'points_per_click' => 'nullable|integer|min:0',
            'share_expires_at' => 'nullable|date',
        ]);


        // Logika untuk gambar otomatis
        if ($request->hasFile('foto')) {
            $validatedData['foto'] = $request->file('foto')->store('kabar_terbaru', 'public');
        } elseif (!empty($validatedData['url_target'])) {
            $validatedData['foto'] = $this->fetchOpenGraphImage($validatedData['url_target']);
        }

        if (!empty($validatedData['url_target'])) {
            $validatedData['share_code'] = Str::random(6);
        }

        KabarTerbaru::create($validatedData);

        return back()->with('success', 'Kabar Terbaru berhasil disimpan!');
    }

    private function fetchOpenGraphImage(string $url): ?string
    {
        try {
            // 1. Ambil konten HTML dari URL menggunakan HTTP Client Laravel
            $response = Http::get($url);

            if (!$response->successful()) {
                return null;
            }
            
            // 2. Buat instance Crawler baru dari konten HTML
            $crawler = new Crawler($response->body());
            
            // 3. Cari meta tag dengan property 'og:image'
            $imageNode = $crawler->filter('meta[property="og:image"]');

            if ($imageNode->count() > 0) {
                return $imageNode->attr('content'); // Ambil isi dari atribut 'content'
            }
            return null;
        } catch (\Exception $e) {
            // Jika gagal (misal: timeout), kembalikan null
            return null;
        }
    }
}
