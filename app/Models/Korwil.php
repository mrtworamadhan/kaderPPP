<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Korwil extends Model
{
    protected $table = 'Korwil';
    
    protected $fillable = [
        'tingkat',
        'nik',
        'nama',
        'phone',
        'rt',
        'rw',
        'desa',
        'id_desa',
        'kecamatan',
        'id_kecamatan',
    ];
}
