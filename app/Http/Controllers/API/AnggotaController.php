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
use Illuminate\Support\Facades\DB;

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
    
    public function statistik(Request $request)
    {
        try {
            $user = $request->user();
            $query = DB::table('anggota');

            // Filter wilayah berdasarkan role
            if ($user->role === 'admin_desa') {
                $query->where('id_desa', $user->id_desa);
            } elseif ($user->role === 'admin_pac') {
                $query->where('id_kecamatan', $user->id_kecamatan);
            }

            $total = $query->count();

            $laki = (clone $query)->whereIn('gender', ['l', 'Laki-laki'])->count();
            $perempuan = (clone $query)->whereIn('gender', ['p', 'Perempuan'])->count();

            $umur_19_30 = (clone $query)
                ->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), [19, 30])
                ->count();

            $umur_31_45 = (clone $query)
                ->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), [31, 45])
                ->count();

            $umur_46_up = (clone $query)
                ->where(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), '>', 45)
                ->count();

            // Ambil semua data anggota (bisa ditambahkan pagination kalau perlu)
            $anggota = $query->orderBy('nama')->get();

            $data = [
                'rekap' => [
                    'total' => $total,
                    'laki_laki' => $laki,
                    'perempuan' => $perempuan,
                    'klasifikasi_umur' => [
                        '19_30' => $umur_19_30,
                        '31_45' => $umur_31_45,
                        '46_up' => $umur_46_up,
                    ],
                ],
                'data' => $anggota
            ];

            return $this->success($data, 'Data anggota berhasil diperbarui.');


        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Statistik Anggota Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data anggota.', 500);
        }
    }

    public function statistikFiltered(Request $request)
    {
        try {

            $id_kecamatan = $request->query('id_kecamatan');
            $id_desa = $request->query('id_desa');

            $query = DB::table('anggota');

            if ($id_desa) {
                $query->where('id_desa', $id_desa);
            } elseif ($id_kecamatan) {
                $query->where('id_kecamatan', $id_kecamatan);
            }

            $total = $query->count();

            $laki = (clone $query)->whereIn('gender', ['l', 'Laki-laki'])->count();
            $perempuan = (clone $query)->whereIn('gender', ['p', 'Perempuan'])->count();

            $umur_19_30 = (clone $query)
                ->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), [19, 30])
                ->count();

            $umur_31_45 = (clone $query)
                ->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), [31, 45])
                ->count();

            $umur_46_up = (clone $query)
                ->where(DB::raw('TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE())'), '>', 45)
                ->count();

            $anggota = $query->orderBy('nama')->get();

            $data = [
                'rekap' => [
                    'total' => $total,
                    'laki_laki' => $laki,
                    'perempuan' => $perempuan,
                    'klasifikasi_umur' => [
                        '19_30' => $umur_19_30,
                        '31_45' => $umur_31_45,
                        '46_up' => $umur_46_up,
                    ],
                ],
                'data' => $anggota
            ];

            return $this->success($data, 'Data anggota berhasil diperbarui.');


        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Statistik Anggota Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data anggota.', 500);
        }
    }
}