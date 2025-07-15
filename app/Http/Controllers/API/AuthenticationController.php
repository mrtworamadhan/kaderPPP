<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Traits\ApiResponder;

class AuthenticationController extends Controller
{
    use ApiResponder;
    /**
     * Register a new user account.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik' => 'required|string|max:255|unique:users,nik',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'nik' => $validated['nik'],
                'password' => Hash::make($validated['password']),
                'role' => 'anggota',
            ]);

            $data = [
                'id' => $user->id,
                'nik' => $user->nik,
            ];

            return $this->success($data, 'Registrasi berhasil.', 201);

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat registrasi.', 500);
        }
    }

    /**
     * Login and return auth token.
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('nik', $validated['nik'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                // KODE YANG SUDAH BENAR
                return $this->error('Login gagal, NIK atau password salah.', 401);
            }

            $token = $user->createToken('mobile-token')->plainTextToken;

            $data = [
                'user_info' => [
                    'id' => $user->id,
                    'nik' => $user->nik,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->success($data, 'Login berhasil.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat login.', 500);
        }
    }


    /**
     * Logout user and revoke tokens — protected route.
     */
    public function logOut(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->success(null, 'Logout berhasil.');

        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat logout.', 500);
        }
    }

    /**
     * Get list of users — protected route.
     */
    public function userInfo()
    {
        try {
            $users = User::latest()->paginate(10);

            $data = $users;
            return $this->success($data, 'Data Berhasil ditampilkan.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat Mengambil Data.', 500);
        }
    }

    public function profil(Request $request)
    {
        try {
            $user = $request->user()->load('anggota');
            $data = [
                'id' => $user->id,
                'nik' => $user->nik,
                'role' => $user->role,
                'anggota' => $user->anggota, // Data lengkap dari tabel anggota
            ];

            return $this->success($data, 'Data Profil Berhasil ditampilkan.');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat Mengambil Data.', 500);
        }
    }

}
