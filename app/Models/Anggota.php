<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';

    // Hapus 'jabatan' dari fillable
    protected $fillable = [
        'nik', 'id_anggota', 'nama', 'phone', 'alamat', 'tgl_lahir',
        'gender', 'pekerjaan', 'id_desa', 'id_kecamatan', 'foto', 'total_poin'
    ];

    /**
     * Relasi: Satu Anggota memiliki satu User login.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Relasi: Satu Anggota bisa punya banyak jabatan di tabel 'struktur'.
     */
    public function struktur()
    {
        return $this->hasMany(Struktur::class);
    }

    /**
     * Relasi: Satu Anggota bisa punya banyak jabatan di tabel 'korwil'.
     */
    public function korwil()
    {
        return $this->hasMany(Korwil::class);
    }
    
    /**
     * Relasi: Satu Anggota bisa punya banyak catatan poin.
     */
    public function poinLogs()
    {
        return $this->hasMany(PoinLog::class);
    }

    // Relasi ke daerah (tetap sama)
    public function kecamatan() { return $this->belongsTo(Daerah::class, 'id_kecamatan'); }
    public function desa() { return $this->belongsTo(Daerah::class, 'id_desa'); }
}