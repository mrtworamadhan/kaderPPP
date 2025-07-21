<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Struktur extends Model
{
    protected $table = 'struktur';

    // Sesuaikan fillable dengan struktur baru
    protected $fillable = [
        'anggota_id',
        'tingkat',
        'jabatan',
        'bagian',
        'urutan',
        'id_desa',
        'id_kecamatan',
    ];

    /**
     * Relasi: Setiap jabatan struktur dimiliki oleh satu Anggota.
     */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
    
    // Relasi ke kecamatan dan desa bisa tetap ada untuk kemudahan query
    public function kecamatan()
    {
        return $this->belongsTo(Daerah::class, 'id_kecamatan');
    }

    public function desa()
    {
        return $this->belongsTo(Daerah::class, 'id_desa');
    }
}