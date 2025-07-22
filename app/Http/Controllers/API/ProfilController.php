<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfilController extends Controller
{
    use ApiResponder;

    public function profil(Request $request)
    {
        try {
            $user = auth()->user()->load('anggota');

            $profilData = $user->anggota->toArray();
            // Menambahkan URL foto yang lengkap dan benar
            $profilData['foto_url'] = $user->anggota->foto ? Storage::disk('public')->url($user->anggota->foto) : null;

            $data = [
                'id'     => $user->id,
                'nik'    => $user->nik,
                'role'   => $user->role,
                'profil' => $profilData,
            ];

            return $this->success($data, 'Data profil berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Profil Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil profil.', 500);
        }
    }

    public function updateProfil(Request $request)
    {
        try {
            $user = auth()->user();
            $anggota = Anggota::where('nik', $user->nik)->first();

            if (!$anggota) {
                return $this->error('Data anggota tidak ditemukan.', 404);
            }

            $validator = Validator::make($request->all(), [
                'nama'      => 'sometimes|required|string',
                'phone'     => 'nullable|string',
                'alamat'    => 'nullable|string',
                'tgl_lahir' => 'nullable|date',
                'gender'    => 'nullable|string',
                'pekerjaan' => 'nullable|string',
                'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validatedData = $validator->validated();

            // Logika untuk menangani upload foto
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($anggota->foto) {
                    Storage::disk('public')->delete($anggota->foto);
                }
                // Simpan foto baru dan dapatkan path-nya
                $path = $request->file('foto')->store('profil_kader', 'public');
                $validatedData['foto'] = $path;
            }
            
            $anggota->update($validatedData);

            return $this->success($anggota, 'Profil berhasil diperbarui.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Update Profil Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memperbarui profil.', 500);
        }
    }
}