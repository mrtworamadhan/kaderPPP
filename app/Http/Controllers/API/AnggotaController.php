<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AnggotaController extends Controller
{
    use ApiResponder; // <-- Trait sudah digunakan

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|unique:anggota,nik',
                'nama' => 'required|string',
                'phone' => 'nullable|string',
                'alamat' => 'nullable|string',
                'tgl_lahir' => 'nullable|date',
                'gender' => 'nullable|string',
                'pekerjaan' => 'nullable|string',
                'desa' => 'nullable|string',
                'id_desa' => 'nullable|integer',
                'id_kecamatan' => 'nullable|integer',
                'jabatan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validatedData = $validator->validated();

            // Auto generate no_kta
            $lastId = Anggota::max('id') ?? 0;
            $validatedData['id_anggota'] = '909013201' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

            $anggota = Anggota::create($validatedData);

            // Buat user login baru untuk anggota
            User::create([
                'nik' => $validatedData['nik'],
                'password' => Hash::make(config('ekader.default_password')),
                'role' => 'anggota',
            ]);

            return $this->success($anggota, 'Data anggota berhasil disimpan.', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Store Anggota Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan data anggota.', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $anggota = Anggota::find($id);

            if (!$anggota) {
                return $this->error('Data anggota tidak ditemukan.', 404);
            }

            $validator = Validator::make($request->all(), [
                'nik' => ['required', 'string', Rule::unique('anggota')->ignore($anggota->id)],
                'nama' => 'required|string',
                'phone' => 'nullable|string',
                'alamat' => 'nullable|string',
                'tgl_lahir' => 'nullable|date',
                'gender' => 'nullable|string',
                'pekerjaan' => 'nullable|string',
                'desa' => 'nullable|string',
                'id_desa' => 'nullable|integer',
                'id_kecamatan' => 'nullable|integer',
                'jabatan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            // Praktik yang lebih aman: hanya update data yang sudah divalidasi
            $anggota->update($validator->validated());

            return $this->success($anggota, 'Data anggota berhasil diperbarui.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Update Anggota Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memperbarui data anggota.', 500);
        }
    }
}