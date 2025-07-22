<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebDashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\Admin\ContentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard/kinerja', function () {
    return view('dashboard_kinerja'); 
});

Route::get('/dashboard/anggota', function () {
    return view('dashboard_anggota');
});
Route::get('/kader/dashboard', function () {
    return view('kader.dashboard');
})->name('kader.dashboard');
Route::get('/kader/kabar-terbaru', function () {
    return view('kader.kabar_terbaru');
})->name('kader.kabar_terbaru');

Route::get('/kader/arahan-ketua', function () {
    return view('kader.arahan_ketua');
})->name('kader.arahan_ketua');

Route::get('/kader/rewards', function () {
    return view('kader.rewards');
})->name('kader.rewards');

Route::get('/kader/profil', function () {
    return view('kader.profil');
})->name('kader.profil');

Route::get('/konten/create', [ContentController::class, 'create'])->name('admin.konten.create');
    
    // Rute untuk menyimpan data dari form
    Route::post('/arahan-ketua', [ContentController::class, 'storeArahan'])->name('admin.arahan.store');
    Route::post('/kabar-terbaru', [ContentController::class, 'storeKabar'])->name('admin.kabar.store');


Route::get('/dashboard-suara', [WebDashboardController::class, 'tampilkanDashboardSuara']);

Route::get('/kader/agenda', function () {
        return view('kader.agenda');
    })->name('kader.agenda');

Route::get('/admin/events/create', function () {
    return view('admin.create_event');
})->name('admin.events.create');

Route::get('/admin/events/{event}/show-qrcode', function (App\Models\Event $event) {
    return view('admin.show_qrcode', compact('event'));
})->name('admin.events.show_qrcode');

Route::get('/admin/events', function () {
    return view('admin.events_index');
})->name('admin.events.index');

Route::get('/konten/create', [ContentController::class, 'create'])->name('admin.konten.create');
    
    // Rute untuk menyimpan data dari form
Route::post('/arahan-ketua', [ContentController::class, 'storeArahan'])->name('admin.arahan.store');
Route::post('/kabar-terbaru', [ContentController::class, 'storeKabar'])->name('admin.kabar.store');

Route::get('/kader/leaderboard', function () {
    return view('kader.leaderboard');
})->name('kader.leaderboard');

Route::get('/s/{share_code}/{anggota_id}', [ShareController::class, 'redirectAndTrack'])->name('share.redirect');

Route::middleware('auth')->group(function () {
    
});