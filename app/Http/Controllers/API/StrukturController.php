<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Struktur;
use App\Models\Anggota;
use App\Models\User;
use App\Models\Daerah;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StrukturController extends Controller
{
    use ApiResponder;

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'data' => 'required|array|min:1',
                'data.*.nik' => 'required|string',
                'data.*.nama' => 'required|string',
                'data.*.tingkat' => 'required|string|in:dpc,dpac,dprt',
                'data.*.jabatan' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data_array = $validator->validated()['data'];
            $inserted = 0;

            foreach ($data_array as $item) {
                $anggota = Anggota::where('nik', $item['nik'])->first();

                if (!$anggota) {
                    $lastId = Anggota::max('id') ?? 0;
                    $no_kta = '909013201' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

                    $anggota = Anggota::create([
                        'nik' => $item['nik'],
                        'id_anggota' => $no_kta,
                        'nama' => $item['nama'],
                        'id_desa'      => $item['id_desa'] ?? null,
                        'id_kecamatan' => $item['id_kecamatan'] ?? null,
                        'jabatan'      => $item['tingkat'],
                    ]);

                    User::create([
                        'nik' => $item['nik'],
                        'password' => Hash::make(config('ekader.default_password')),
                        'role' => 'anggota',
                        'id_desa' => $item['id_desa'] ?? null,
                        'id_kecamatan' => $item['id_kecamatan'] ?? null,
                    ]);
                }

                Struktur::create([
                    'tingkat'      => $item['tingkat'],
                    'nik'          => $item['nik'],
                    'nama'         => $item['nama'],
                    'jabatan'      => $item['jabatan'],
                    'bagian'       => $item['bagian'] ?? '',
                    'urutan'       => $item['urutan'] ?? 0,
                    'id_desa'      => $item['id_desa'] ?? null,
                    'id_kecamatan' => $item['id_kecamatan'] ?? null,
                ]);
                $inserted++;
            }

            return $this->success(null, "$inserted data struktur berhasil disimpan.", 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Store Struktur Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan data struktur.', 500);
        }
    }

    public function byKecamatan($id)
    {
        try {
            $struktur = Struktur::where('id_kecamatan', $id)->orderBy('urutan')->get();
            return $this->success($struktur, 'Data struktur by kecamatan berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Struktur by Kecamatan Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data struktur.', 500);
        }
    }
    
    public function byDesa($id)
    {
        try {
            $struktur = Struktur::with(['anggota', 'kecamatan', 'desa'])->where('id_desa', $id)->orderBy('urutan')->get();
            return $this->success($struktur, 'Data struktur by desa berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Struktur by Desa Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data struktur.', 500);
        }
    }
    
    public function indexDpac(Request $request)
    {
        try {
            // Logika otorisasi bisa disempurnakan dengan Gate/Policy nanti
            if ($request->user()->role !== 'admin_pusat') {
                return $this->error('Anda tidak memiliki hak akses.', 403);
            }
    
            $kecamatanIds = DB::table('struktur')->where('tingkat', 'DPAC')->distinct()->pluck('id_kecamatan');
            $nama_kecamatan = Daerah::whereIn('id', $kecamatanIds)->get(['id', 'nama']);
    
            $response = [
                'jumlah' => $nama_kecamatan->count(),
                'data' => $nama_kecamatan
            ];
    
            return $this->success($response, 'Data struktur DPAC berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Index DPAC Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data DPAC.', 500);
        }
    }
    
    public function indexDprt(Request $request)
    {
        try {
            $user = $request->user();
            $query = DB::table('struktur')->where('tingkat', 'DPRt');
    
            if ($user->role === 'admin_pac') {
                $query->where('id_kecamatan', $user->id_kecamatan);
            } elseif ($user->role === 'admin_pusat') {
                $request->validate(['id_kecamatan' => 'required|exists:daerah,id']);
                $query->where('id_kecamatan', $request->id_kecamatan);
            } else {
                return $this->error('Anda tidak memiliki hak akses.', 403);
            }
    
            $desaIds = $query->distinct()->pluck('id_desa');
            $nama_desa = Daerah::whereIn('id', $desaIds)->get(['id', 'nama']);
            
            $response = [
                'jumlah' => $nama_desa->count(),
                'data' => $nama_desa
            ];
    
            return $this->success($response, 'Data struktur DPRt berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Index DPRT Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data DPRt.', 500);
        }
    }
}