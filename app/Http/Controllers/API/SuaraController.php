<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suara;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SuaraController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        try {
            $query = Suara::with(['kecamatan', 'desa']);

            if ($request->has('id_kecamatan')) {
                $query->where('id_kecamatan', $request->id_kecamatan);
            }

            if ($request->has('id_desa')) {
                $query->where('id_desa', $request->id_desa);
            }

            if ($request->has('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            $data = $query->orderBy('tahun', 'desc')->get();

            return $this->success($data, 'Data suara berhasil diambil.');

        } catch (\Exception $e) {
            Log::error('Get Suara Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengambil data suara.', 500);
        }
    }

    public function store(Request $request)
    {
        try {
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
                'tps' => 'nullable|string|max:255',
                'sumber' => 'nullable|string|max:255',
            ], [
                'id_desa.unique' => 'Data suara untuk wilayah dan tahun ini sudah ada.'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $suara = Suara::create($validator->validated());

            return $this->success($suara, 'Data suara berhasil disimpan', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Store Suara Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat menyimpan data suara.', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $suara = Suara::find($id);

            if (!$suara) {
                return $this->error('Data suara tidak ditemukan.', 404);
            }

            $validator = Validator::make($request->all(), [
                'dprd' => 'sometimes|required|integer|min:0',
                'dpr_prov' => 'sometimes|required|integer|min:0',
                'dpr_ri' => 'sometimes|required|integer|min:0',
                'tps' => 'nullable|string|max:255',
                'sumber' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $suara->update($validator->validated());

            return $this->success($suara, 'Data suara berhasil diperbarui.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            Log::error('Update Suara Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat memperbarui data suara.', 500);
        }
    }
}