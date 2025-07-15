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

class InfoPublikController extends Controller
{
    use ApiResponder;

    public function arahanKetua()
    {
        try {
            $data = ArahanKetua::orderBy('tanggal', 'desc')->first();
            if (!$data) {
                return $this->success(null, 'Belum ada arahan ketua.');
            }
            $formattedData = [
                'tanggal' => $data->tanggal->format('d-m-Y'),
                'arahan' => $data->arahan,
            ];
            return $this->success($formattedData, 'Arahan ketua terbaru berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Arahan Ketua Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil arahan ketua.', 500);
        }
    }
    
    public function arahanIndex()
    {
        try {
            $data = ArahanKetua::orderBy('tanggal', 'desc')->get();
            $formattedData = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => Carbon::parse($item->tanggal)->format('d-m-Y'),
                    'arahan' => $item->arahan,
                ];
            });
            return $this->success($formattedData, 'Daftar arahan berhasil diambil.');
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
        try {
            $validator = Validator::make($request->all(), [
                'deskripsi' => 'required|string',
                'link' => 'required|url',
                'gambar' => 'required|string', // base64
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validatedData = $validator->validated();
            $base64_image = $validatedData['gambar'];
    
            // Proses upload gambar dari base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                $base64_image = substr($base64_image, strpos($base64_image, ',') + 1);
                $ext = strtolower($type[1]);
    
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    return $this->error('Format gambar tidak didukung. Hanya jpg, jpeg, dan png.', 422);
                }
                
                $image_data = base64_decode($base64_image);
                if ($image_data === false) {
                    return $this->error('Gagal mendekode data gambar base64.', 400);
                }

                $filename = 'kabar/' . Str::uuid() . '.' . $ext;
                // Menggunakan Laravel Storage untuk keamanan dan fleksibilitas
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $image_data);

            } else {
                return $this->error('Data gambar tidak valid. Harus dalam format base64 lengkap.', 422);
            }
            
            $kabar = KabarTerbaru::create([
                'deskripsi' => $validatedData['deskripsi'],
                'link' => $validatedData['link'],
                'gambar' => $filename, // Simpan path relatif dari storage
            ]);
    
            return $this->success($kabar, 'Kabar berhasil disimpan.', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Store Kabar Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan kabar.', 500);
        }
    }
}