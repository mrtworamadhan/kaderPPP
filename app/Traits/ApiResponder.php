<?php

namespace App\Traits;

trait ApiResponder
{
    /**
     * Membangun respons sukses.
     *
     * @param  mixed  $data
     * @param  string $message
     * @param  int    $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, $message = 'Permintaan berhasil.', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null
        ], $code);
    }

    /**
     * Membangun respons error.
     *
     * @param  string $message
     * @param  int    $code
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message = 'Terjadi kesalahan.', $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors
        ], $code);
    }

    /**
     * Membangun respons untuk validasi yang gagal.
     *
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationError($errors)
    {
        return $this->error('Validasi gagal.', 422, $errors);
    }
}