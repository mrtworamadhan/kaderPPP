<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class KaderProfilController extends Controller
{
    use ApiResponder;

    /**
     * Menampilkan data profil kader yang sedang login.
     */
    public function show(Request $request)
    {
        $anggota = $request->user()->anggota;

        if (!$anggota) {
            return $this->error('Profil anggota tidak ditemukan.', 404);
        }

        return $this->success($anggota, 'Profil berhasil diambil.');
    }

    /**
     * Memperbarui data profil kader yang sedang login.
     */
    public function update(Request $request)
    {
        $anggota = $request->user()->anggota;

        if (!$anggota) {
            return $this->error('Profil anggota tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tgl_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk foto
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $validatedData = $validator->validated();

        // Handle upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($anggota->foto) {
                Storage::disk('public')->delete($anggota->foto);
            }
            // Simpan foto baru
            $validatedData['foto'] = $request->file('foto')->store('profil_anggota', 'public');
        }

        $anggota->update($validatedData);

        return $this->success($anggota, 'Profil berhasil diperbarui.');
    }
}
