<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $fillable = [
        'nik',
        'id_anggota',
        'nama',
        'phone',
        'alamat',
        'tgl_lahir',
        'gender',
        'pekerjaan',
        'desa',
        'id_desa',
        'kecamatan',
        'id_kecamatan',
        'jabatan',
        'foto',
    ];

}
