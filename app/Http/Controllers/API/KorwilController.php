<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Korwil;
use App\Models\Anggota;
use App\Models\User;
use App\Models\WilayahRtRw;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KorwilController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $query = Korwil::query();

            if ($user->role === 'admin_desa') {
                $query->where('id_desa', $user->id_desa);
            } elseif ($user->role === 'admin_pac') {
                $query->where('id_kecamatan', $user->id_kecamatan);
            }

            $data = $query->orderBy('tingkat')->get()->groupBy('id_desa');

            $formatted = $data->map(function ($group, $id_desa) {
                return [
                    'id_desa' => $id_desa,
                    'desa'    => $group->first()->desa ?? '-',
                    'data'    => $group->map(fn($item) => [
                        'id'      => $item->id,
                        'tingkat' => $item->tingkat,
                        'nik'     => $item->nik,
                        'nama'    => $item->nama,
                        'phone'   => $item->phone,
                        'rt'      => $item->rt,
                        'rw'      => $item->rw,
                    ])->values()
                ];
            })->values();

            return $this->success($formatted, 'Data korwil berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Get Korwil Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data korwil.', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tingkat'      => 'required|in:korw,kort',
                'nik'          => 'required|string|unique:anggota,nik|unique:users,nik|unique:korwil,nik',
                'nama'         => 'required|string',
                'phone'        => 'nullable|string',
                'rt'           => 'nullable|string',
                'rw'           => 'nullable|string',
                'id_desa'      => 'required|exists:daerah,id',
                'id_kecamatan' => 'required|exists:daerah,id',
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validatedData = $validator->validated();
            
            $lastId = Anggota::max('id') ?? 0;
            $no_kta = '909013201' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

            // Simpan ke tabel anggota
            $anggota = Anggota::create([
                'nik'           => $validatedData['nik'],
                'id_anggota'    => $no_kta,
                'nama'          => $validatedData['nama'],
                'phone'         => $validatedData['phone'] ?? null,
                'id_desa'       => $validatedData['id_desa'],
                'id_kecamatan'  => $validatedData['id_kecamatan'],
                'jabatan'       => $validatedData['tingkat'],
            ]);

            // Simpan ke tabel korwil
            $korwil = Korwil::create([
                'tingkat'      => $validatedData['tingkat'],
                'nik'          => $validatedData['nik'],
                'nama'         => $validatedData['nama'],
                'phone'        => $validatedData['phone'] ?? null,
                'rt'           => $validatedData['rt'] ?? null,
                'rw'           => $validatedData['rw'] ?? null,
                'id_desa'      => $validatedData['id_desa'],
                'id_kecamatan' => $validatedData['id_kecamatan'],
            ]);

            // Buat user login
            User::create([
                'nik' => $validatedData['nik'],
                'password' => Hash::make(config('ekader.default_password')),
                'role' => 'anggota',
                'id_desa' => $validatedData['id_desa'],
                'id_kecamatan' => $validatedData['id_kecamatan'],
            ]);

            return $this->success($korwil, 'Data korwil berhasil ditambahkan.', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Store Korwil Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan data korwil.', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $korwil = Korwil::find($id);
            if (!$korwil) {
                return $this->error('Data korwil tidak ditemukan.', 404);
            }
    
            $validator = Validator::make($request->all(), [
                'nama'  => 'required|string',
                'phone' => 'nullable|string',
                'rt'    => 'nullable|string',
                'rw'    => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validated = $validator->validated();
    
            // Update di tabel korwil
            $korwil->update($validated);
    
            // Sync ke anggota
            Anggota::where('nik', $korwil->nik)->update([
                'nama'  => $validated['nama'],
                'phone' => $validated['phone'] ?? $korwil->phone,
            ]);
    
            // Sync ke users (Memperbaiki bug 'username' menjadi 'nik')
            $user = User::where('nik', $korwil->nik)->first();
            if ($user && empty($user->name)) { // Hanya update jika nama di tabel user masih kosong
                $user->update(['name' => $validated['nama']]);
            }
    
            return $this->success($korwil, 'Data korwil berhasil diupdate.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Update Korwil Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memperbarui data korwil.', 500);
        }
    }
    
    public function getByDesa($id_desa)
    {
        try {
            $korwil = Korwil::where('id_desa', $id_desa)->orderBy('tingkat')->get();
            $desaInfo = WilayahRtRw::where('id_desa', $id_desa)->select('jumlah_rw')->first();
    
            $data = [
                'id_desa' => (int) $id_desa,
                'jumlah_rw' => $desaInfo->jumlah_rw ?? 0,
                'data' => $korwil->map(function ($item) {
                    return [
                        'id'       => $item->id,
                        'tingkat'  => $item->tingkat,
                        'nik'      => $item->nik,
                        'nama'     => $item->nama,
                        'phone'    => $item->phone,
                        'rw'       => $item->rw,
                    ];
                })
            ];
    
            return $this->success($data, 'Data korwil berdasarkan desa berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Get Korwil by Desa Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data korwil.', 500);
        }
    }
}