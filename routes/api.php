<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\AnggotaController;
use App\Http\Controllers\API\ProfilController;
use App\Http\Controllers\API\StrukturController;
use App\Http\Controllers\API\DaerahController;
use App\Http\Controllers\API\SuaraController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\Api\InfoPublikController;


// Login & Register
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);

// Protected Routes (require token)
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/profil', function (Request $request) {
    //     return response()->json($request->user());
    // });

    Route::post('/logout', [AuthenticationController::class, 'logOut']);
    Route::get('/users', [AuthenticationController::class, 'userInfo']);
    Route::get('/profilanggota', [AuthenticationController::class, 'profil']);

    Route::get('/arahan-ketua', [InfoPublikController::class, 'arahanKetua']);
    Route::post('/arahan/store', [InfoPublikController::class, 'arahanStore']);
    Route::get('/kabar-terbaru', [InfoPublikController::class, 'kabarTerbaru']);
    Route::post('/kabar/store', [InfoPublikController::class, 'kabarStore']);

    Route::get('/profil', [ProfilController::class, 'profil']);
    Route::put('/profil/update', [ProfilController::class, 'updateProfil']);

    Route::post('/anggota', [AnggotaController::class, 'store']);
    Route::put('/anggota/{id}', [AnggotaController::class, 'update']);

    Route::post('/struktur', [StrukturController::class, 'store']);
    Route::get('/struktur/kecamatan/{id}', [StrukturController::class, 'byKecamatan']);
    Route::get('/struktur/desa/{id}', [StrukturController::class, 'byDesa']);


    Route::get('/kecamatan', [DaerahController::class, 'getKecamatan']);
    Route::get('/get-desa/{id_kecamatan}', [DaerahController::class, 'getDesa']);

    Route::get('/suara', [SuaraController::class, 'index']);
    Route::post('/suara', [SuaraController::class, 'store']);
    Route::put('/suara/{id}', [SuaraController::class, 'update']);

    Route::get('/admin/users', [AdminController::class, 'index']);

    // Update data struktur partai (multi row update atau satuan)
    Route::post('/admin/struktur', [AdminController::class, 'storeStruktur']);
    Route::put('/admin/struktur/{id}', [AdminController::class, 'updateStruktur']);

    // Input data suara (per wilayah)
    Route::post('/admin/suara', [AdminController::class, 'storeSuara']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

});


