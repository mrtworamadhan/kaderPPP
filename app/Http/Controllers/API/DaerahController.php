<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Daerah;
use App\Models\WilayahRtRW;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DaerahController extends Controller
{
    use ApiResponder;

    public function getKecamatan()
    {
        try {
            $kecamatan = Daerah::where('parent_id', 2)->get();
            return $this->success($kecamatan, 'Data kecamatan berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Kecamatan Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data kecamatan.', 500);
        }
    }

    public function getDesa($id_kecamatan)
    {
        try {
            $desa = Daerah::where('parent_id', $id_kecamatan)->get();
            return $this->success($desa, 'Data desa berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Get Desa Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data desa.', 500);
        }
    }
    
    public function profilKecamatan($id_kecamatan)
    {
        try {
            $kecamatan = Daerah::find($id_kecamatan);
            if (!$kecamatan) {
                return $this->error('Kecamatan tidak ditemukan.', 404);
            }

            $data = WilayahRtRW::where('id_kecamatan', $id_kecamatan)
                ->select('id_desa', 'desa', 'jumlah_rw', 'jumlah_rt', 'jumlah_tps', 'jumlah_dpt')
                ->get();

            $summary = [
                'jumlah_desa' => $data->count(),
                'total_rw'    => $data->sum('jumlah_rw'),
                'total_rt'    => $data->sum('jumlah_rt'),
                'total_tps'   => $data->sum('jumlah_tps'),
                'total_dpt'   => $data->sum('jumlah_dpt'),
            ];

            $response = [
                'induk' => [
                    'id'   => $kecamatan->id,
                    'nama' => $kecamatan->nama,
                ],
                'data'    => $data,
                'summary' => $summary,
            ];

            return $this->success($response, 'Profil kecamatan berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Profil Kecamatan Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil profil kecamatan.', 500);
        }
    }
    
    public function profilDesa($id_desa)
    {
        try {
            $data = WilayahRtRW::where('id_desa', $id_desa)
                ->select('id_kecamatan', 'kecamatan', 'id_desa', 'desa', 'jumlah_rw', 'jumlah_rt', 'jumlah_tps', 'jumlah_dpt')
                ->first();

            if (!$data) {
                return $this->error('Data profil desa tidak ditemukan.', 404);
            }
    
            return $this->success($data, 'Profil desa berhasil diambil.');
        } catch (\Exception $e) {
            Log::error('Profil Desa Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil profil desa.', 500);
        }
    }
    
    public function getWilayahAdmin(Request $request)
    {
        try {
            $user = $request->user();
            $response = null;

            if ($user->role === 'admin_desa') {
                $data = WilayahRtRW::where('id_desa', $user->id_desa)->first();
                if (!$data) return $this->error('Data wilayah untuk desa ini tidak ditemukan.', 404);
                
                $response = [
                    'induk'   => ['id' => $data->id_kecamatan, 'nama' => $data->kecamatan ?? '-'],
                    'data'    => [['id_desa' => $data->id_desa, 'desa' => $data->desa ?? '-', 'jumlah_rw' => $data->jumlah_rw, 'jumlah_rt' => $data->jumlah_rt, 'jumlah_tps' => $data->jumlah_tps, 'jumlah_dpt' => $data->jumlah_dpt]],
                    'summary' => ['jumlah_desa' => 1, 'total_rw' => $data->jumlah_rw, 'total_rt' => $data->jumlah_rt, 'total_tps' => $data->jumlah_tps, 'total_dpt' => $data->jumlah_dpt]
                ];

            } elseif ($user->role === 'admin_pac') {
                $data = WilayahRtRW::where('id_kecamatan', $user->id_kecamatan)->get();
                $kecamatan = Daerah::find($user->id_kecamatan);
                
                $response = [
                    'induk' => ['id' => $kecamatan->id ?? null, 'nama' => $kecamatan->nama ?? '-'],
                    'data'  => $data->map(fn($item) => ['id_desa' => $item->id_desa, 'desa' => $item->desa ?? '-', 'jumlah_rw' => $item->jumlah_rw, 'jumlah_rt' => $item->jumlah_rt, 'jumlah_tps' => $item->jumlah_tps, 'jumlah_dpt' => $item->jumlah_dpt]),
                    'summary' => ['jumlah_desa' => $data->count(), 'total_rw' => $data->sum('jumlah_rw'), 'total_rt' => $data->sum('jumlah_rt'), 'total_tps' => $data->sum('jumlah_tps'), 'total_dpt' => $data->sum('jumlah_dpt')]
                ];
                
            } elseif ($user->role === 'admin_pusat') {
                $data = WilayahRtRW::with('kecamatan')->selectRaw('id_kecamatan, SUM(jumlah_rw) as jumlah_rw, SUM(jumlah_rt) as jumlah_rt, SUM(jumlah_tps) as jumlah_tps, SUM(jumlah_dpt) as jumlah_dpt')->groupBy('id_kecamatan')->get();
                
                $formatted = $data->map(fn($item) => ['id_kecamatan' => $item->id_kecamatan, 'kecamatan' => $item->kecamatan->nama ?? '-', 'jumlah_rw' => (int)$item->jumlah_rw, 'jumlah_rt' => (int)$item->jumlah_rt, 'jumlah_tps' => (int)$item->jumlah_tps, 'jumlah_dpt' => (int)$item->jumlah_dpt]);

                $response = [
                    'induk'   => ['id' => 2, 'nama' => 'Kabupaten Bogor'],
                    'summary' => ['total_rw' => $formatted->sum('jumlah_rw'), 'total_rt' => $formatted->sum('jumlah_rt'), 'total_tps' => $formatted->sum('jumlah_tps'), 'total_dpt' => $formatted->sum('jumlah_dpt')],
                    'data'    => $formatted
                ];
            }

            if (is_null($response)) {
                return $this->error('Role tidak dikenali atau tidak memiliki akses.', 403);
            }

            return $this->success($response, 'Data wilayah berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Get Wilayah Admin Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data wilayah.', 500);
        }
    }
    
    public function updateProfilDesa(Request $request, $id_desa)
    {
        try {
            $validator = Validator::make($request->all(), [
                'jumlah_rw'  => 'required|integer|min:0',
                'jumlah_rt'  => 'required|integer|min:0',
                'jumlah_tps' => 'required|integer|min:0',
                'jumlah_dpt' => 'required|integer|min:0',
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $wilayah = WilayahRtRW::where('id_desa', $id_desa)->first();
            if (!$wilayah) {
                return $this->error('Data wilayah tidak ditemukan untuk desa ini.', 404);
            }
    
            $wilayah->update($validator->validated());
    
            return $this->success($wilayah, 'Data profil desa berhasil diperbarui.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Update Profil Desa Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memperbarui profil desa.', 500);
        }
    }
}