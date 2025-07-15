<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Struktur extends Model
{
    protected $table = 'struktur';
    protected $fillable = [
        'tingkat',
        'nik',
        'nama',
        'jabatan',
        'bagian',
        'urutan',
        'desa',
        'id_desa',
        'kecamatan',
        'id_kecamatan',
    ];
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'nik', 'nik');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Daerah::class, 'id_kecamatan');
    }

    public function desa()
    {
        return $this->belongsTo(Daerah::class, 'id_desa');
    }
}
