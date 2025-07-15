<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticationController extends Controller
{
    /**
     * Register a new user account.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik'      => 'required|string|max:255|unique:users,nik',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'nik'      => $validated['nik'],
                'password' => Hash::make($validated['password']),
                'role'     => 'anggota',
            ]);

            return response()->json([
                'response_code' => 201,
                'status'        => 'success',
                'message'       => 'Registrasi berhasil',
                'user_info'     => [
                    'id'  => $user->id,
                    'nik' => $user->nik,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validasi gagal',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Terjadi kesalahan saat registrasi',
            ], 500);
        }
    }

    /**
     * Login and return auth token.
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik'      => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('nik', $validated['nik'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Login gagal, NIK atau password salah',
                ], 401);
            }

            $token = $user->createToken('mobile-token')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Login berhasil',
                'user_info'     => [
                    'id'  => $user->id,
                    'nik' => $user->nik,
                    'role' => $user->role,
                ],
                'token'      => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validasi gagal',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Terjadi kesalahan saat login',
            ], 500);
        }
    }

    /**
     * Logout user and revoke tokens — protected route.
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'Logout berhasil',
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status'        => 'error',
                'message'       => 'User tidak terautentikasi',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Terjadi kesalahan saat logout',
            ], 500);
        }
    }

    /**
     * Get list of users — protected route.
     */
    public function userInfo()
    {
        try {
            $users = User::latest()->paginate(10);

            return response()->json([
                'response_code'  => 200,
                'status'         => 'success',
                'message'        => 'Data user berhasil diambil',
                'data_user_list' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('User List Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Terjadi kesalahan saat mengambil data',
            ], 500);
        }
    }

    public function profil(Request $request)
    {
        try {
            $user = $request->user()->load('anggota');

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Data profil berhasil diambil',
                'data_user'     => [
                    'id'     => $user->id,
                    'nik'    => $user->nik,
                    'role'   => $user->role,
                    'anggota' => $user->anggota, // Data lengkap dari tabel anggota
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Profil Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Terjadi kesalahan saat mengambil profil',
            ], 500);
        }
    }

}
