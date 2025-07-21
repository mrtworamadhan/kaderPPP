<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Role;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 2. AMBIL SEMUA DATA EVENT, URUTKAN DARI YANG TERBARU
        $events = Event::latest()->get();

        // 3. KIRIM DATA KE VIEW
        return view('agenda.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('agenda.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi data, termasuk array roles
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'roles' => 'required|array', // Pastikan roles adalah array
            'roles.*' => 'exists:roles,id', // Pastikan setiap isinya ada di tabel roles
        ]);

        // 2. Buat event terlebih dahulu
        $event = Event::create($validatedData);

        // 3. Lampirkan (attach) relasi roles ke event yang baru dibuat
        $event->roles()->attach($request->roles);

        // 4. Alihkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
