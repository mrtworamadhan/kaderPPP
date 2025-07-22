<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArahanKetua;
use App\Models\KabarTerbaru;
use App\Traits\ApiResponder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http; // <-- Gunakan HTTP Client bawaan Laravel
use Symfony\Component\DomCrawler\Crawler; // <-- Gunakan DomCrawler


class InfoPublikController extends Controller
{
    use ApiResponder;

    public function arahanKetua()
    {
        try {
            $data = ArahanKetua::latest('tanggal')->first(); // Gunakan latest() lebih ringkas
            if (!$data) {
                return $this->success(null, 'Belum ada arahan ketua.');
            }
            return $this->success($data, 'Arahan ketua terbaru berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Arahan Ketua Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil arahan ketua.', 500);
        }
    }

    public function arahanIndex()
    {
        try {
            // --- PENYESUAIAN DI SINI ---
            // Menggunakan paginate() untuk efisiensi
            $data = ArahanKetua::latest('tanggal')->paginate(10); // Ambil 10 per halaman

            return $this->success($data, 'Daftar arahan berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Arahan Index Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil daftar arahan.', 500);
        }
    }

    public function arahanStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date',
                'arahan' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $arahan = ArahanKetua::create($validator->validated());
            return $this->success($arahan, 'Arahan berhasil disimpan.', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Store Arahan Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan arahan.', 500);
        }
    }

    public function kabarTerbaru()
    {
        try {
            $data = KabarTerbaru::orderBy('created_at', 'desc')->take(5)->get();
            $formattedData = $data->map(function ($item) {
                return [
                    'deskripsi' => $item->deskripsi,
                    'link' => $item->link,
                    'gambar' => asset($item->gambar),
                ];
            });
            return $this->success($formattedData, 'Kabar terbaru berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Kabar Terbaru Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil kabar terbaru.', 500);
        }
    }

    public function kabarStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:aktif,tidak aktif',
            'url_target' => 'nullable|url',
            'points_per_click' => 'nullable|integer|min:0',
            'share_expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $validatedData = $validator->validated();

        // Logika untuk gambar otomatis
        if ($request->hasFile('foto')) {
            $validatedData['foto'] = $request->file('foto')->store('kabar_terbaru', 'public');
        } elseif (!empty($validatedData['url_target'])) {
            $validatedData['foto'] = $this->fetchOpenGraphImage($validatedData['url_target']);
        }

        if (!empty($validatedData['url_target'])) {
            $validatedData['share_code'] = Str::random(6);
        }

        $kabar = KabarTerbaru::create($validatedData);

        return $this->success($kabar, 'Kabar terbaru berhasil ditambahkan.', 201);
    }

    // ... (method update() juga perlu disesuaikan dengan logika yang sama)

    /**
     * Helper function untuk mengambil URL gambar dari meta tag Open Graph (og:image).
     */
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

    public function paginatedIndex(Request $request)
    {
        $user = $request->user();
        $anggota = $user->anggota;

        if (!$anggota) {
            return $this->error('Profil anggota tidak ditemukan.', 404);
        }

        $kabarTerbaru = KabarTerbaru::whereDate('share_expires_at', '>=', Carbon::today())
            ->latest()
            ->paginate(10); // Ambil 10 berita per halaman

        // Tambahkan share_link unik untuk setiap item
        $kabarTerbaru->getCollection()->transform(function ($kabar) use ($anggota) {
            if ($kabar->share_code) {
                $kabar->share_link = route('share.redirect', [
                    'share_code' => $kabar->share_code,
                    'anggota_id' => $anggota->id,
                ]);
            }
            return $kabar;
        });

        return $this->success($kabarTerbaru, 'Daftar kabar terbaru berhasil diambil.');
    }
}