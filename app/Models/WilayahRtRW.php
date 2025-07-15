<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahRtRw extends Model
{
    protected $table = 'wilayah_rtrw';

    protected $fillable = [
        'id_kecamatan',
        'kecamatan',
        'id_desa',
        'desa',
        'jumlah_rw',
        'jumlah_rt',
        'jumlah_tps',
        'jumlah_dpt',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Daerah::class, 'id_kecamatan');
    }

    public function desa()
    {
        return $this->belongsTo(Daerah::class, 'id_desa');
    }
}