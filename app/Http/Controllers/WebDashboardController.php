<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard suara.
     */
    public function tampilkanDashboardSuara()
    {
        // Cukup kembalikan view yang sudah kita buat
        return view('dashboard_suara');
    }
}