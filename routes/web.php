<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebDashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard-suara', [WebDashboardController::class, 'tampilkanDashboardSuara']);