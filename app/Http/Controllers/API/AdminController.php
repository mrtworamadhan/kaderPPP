<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Struktur;
use App\Models\Anggota;
use App\Models\Suara;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    use ApiResponder;

    public function index()
    {
        try {
            $authUser = auth()->user();
            $query = User::query();

            if ($authUser->role === 'admin_pac') {
                $query->where('id_kecamatan', $authUser->id_kecamatan);
            } elseif ($authUser->role === 'admin_desa') {
                $query->where('id_desa', $authUser->id_desa);
            }

            return $this->success($query->get(), 'Data pengguna berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Admin Get Users Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data pengguna.', 500);
        }
    }

    public function updateStruktur(Request $request)
    {
        try {
            // Validasi dimasukkan ke dalam blok try
            $validator = Validator::make($request->all(), [
                'struktur' => 'required|array',
                'struktur.*.nik' => 'required|digits:16|exists:anggota,nik',
                'struktur.*.jabatan' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

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
            
            // Mengembalikan pesan sukses tanpa data
            return $this->success(null, 'Struktur berhasil diperbarui.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Update Struktur Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memperbarui struktur.', 500);
        }
    }
    
    public function storeSuara(Request $request)
    {
        try {
            // Menggunakan validasi yang sama dengan SuaraController untuk konsistensi
            $validator = Validator::make($request->all(), [
                'tahun' => 'required|integer|digits:4',
                'id_kecamatan' => 'required|exists:daerah,id',
                'id_desa' => [
                    'required',
                    'exists:daerah,id',
                    Rule::unique('suara')->where(function ($query) use ($request) {
                        return $query->where('tahun', $request->tahun)
                                     ->where('id_kecamatan', $request->id_kecamatan);
                    }),
                ],
                'dprd' => 'required|integer|min:0',
                'dpr_prov' => 'required|integer|min:0',
                'dpr_ri' => 'required|integer|min:0',
            ], [
                'id_desa.unique' => 'Data suara untuk wilayah dan tahun ini sudah ada.'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $suara = Suara::create($validator->validated());

            return $this->success($suara, 'Data suara berhasil disimpan.', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Admin Store Suara Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan data suara.', 500);
        }
    }
}