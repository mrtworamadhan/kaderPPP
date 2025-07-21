<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebDashboardController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard/kinerja', function () {
    return view('dashboard_kinerja'); 
});

Route::get('/dashboard/anggota', function () {
    return view('dashboard_anggota');
});

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

Route::middleware('auth')->group(function () {
    
});