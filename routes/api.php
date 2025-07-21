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
use App\Http\Controllers\API\InfoPublikController;
use App\Http\Controllers\API\KorwilController;
use App\Http\Controllers\API\DashboardSuaraController;
use App\Http\Controllers\API\DashboardStrukturController;
use App\Http\Controllers\API\DashboardAnggotaController;
use App\Http\Controllers\API\EventController;

// Login & Register
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);
Route::get('/kecamatan', [DaerahController::class, 'getKecamatan']);
Route::get('/get-desa/{id_kecamatan}', [DaerahController::class, 'getDesa']);
Route::get('/wilayah/desa-rtrw/{id_kecamatan}', [DaerahController::class, 'getDesaRTRW']);

Route::get('/dashboard/suara/perbandingan', [DashboardSuaraController::class, 'perbandingan']);
Route::get('/dashboard/suara/detail', [DashboardSuaraController::class, 'detail']);
Route::get('/dashboard/kinerja', [DashboardStrukturController::class, 'pencapaian']);

Route::get('/dashboard/anggota/statistik', [DashboardAnggotaController::class, 'statistik']);
Route::get('/dashboard/anggota/cari', [DashboardAnggotaController::class, 'cari']);


// Protected Routes (require token)
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/profil', function (Request $request) {
    //     return response()->json($request->user());
    // });
    Route::get('/get-wilayah-rtrw', [DaerahController::class, 'getWilayahAdmin']);
    Route::get('/profildesa/{id_desa}', [DaerahController::class, 'profilDesa']);
    Route::put('/profildesa/{id_desa}', [DaerahController::class, 'updateProfilDesa']);
    Route::get('/profilkecamatan/{id_kecamatan}', [DaerahController::class, 'profilKecamatan']);

    
    Route::post('/logout', [AuthenticationController::class, 'logOut']);
    Route::get('/users', [AuthenticationController::class, 'userInfo']);
    Route::get('/profilanggota', [AuthenticationController::class, 'profil']);

    Route::get('/arahan', [InfoPublikController::class, 'arahanIndex']);
    Route::get('/arahan-ketua', [InfoPublikController::class, 'arahanKetua']);
    Route::post('/arahan/store', [InfoPublikController::class, 'arahanStore']);
    Route::get('/kabar-terbaru', [InfoPublikController::class, 'kabarTerbaru']);
    Route::post('/kabar/store', [InfoPublikController::class, 'kabarStore']);
    Route::post('/upload-gambar', [InfoPublikController::class, 'uploadGambar']);

    Route::get('/profil', [ProfilController::class, 'profil']);
    Route::put('/profil/update', [ProfilController::class, 'updateProfil']);
    

    Route::post('/anggota', [AnggotaController::class, 'store']);
    Route::put('/anggota/{id}', [AnggotaController::class, 'update']);
    Route::get('/anggota/statistik', [AnggotaController::class, 'statistik']);
    Route::get('/anggota/statistik/filter', [AnggotaController::class, 'statistikFiltered']);

    
    Route::post('/struktur', [StrukturController::class, 'store']);
    Route::get('/struktur/kecamatan/{id}', [StrukturController::class, 'byKecamatan']);
    Route::get('/struktur/desa/{id}', [StrukturController::class, 'byDesa']);
    Route::get('/struktur/pac', [StrukturController::class, 'indexDpac']);
    Route::get('/struktur/dprt', [StrukturController::class, 'indexDprt']);
    
    Route::get('/korwil', [KorwilController::class, 'index']);
    Route::post('/korwil', [KorwilController::class, 'store']);
    Route::put('/korwil/{id}', [KorwilController::class, 'update']);
    Route::get('/korw/{id}', [KorwilController::class, 'getByDesa']);

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

    Route::prefix('events')->group(function () {
          
            Route::get('/roles', [EventController::class, 'getRoles']);
            Route::post('/', [EventController::class, 'store']);
            Route::get('/', [EventController::class, 'index']);
            Route::get('/admin-index', [EventController::class, 'adminIndex']);
            Route::get('/{event}/qrcode', [EventController::class, 'showQrCode'])->name('events.qrcode');
            Route::post('/attend', [EventController::class, 'attend']);
            Route::get('/{event}/attendees', [EventController::class, 'getAttendees']);

        });

    
});


