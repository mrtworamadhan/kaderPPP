<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suara extends Model
{
    protected $table = 'Suara';
    protected $fillable = [
        'tahun',
        'desa',
        'id_desa',
        'kecamatan',
        'id_kecamatan',
        'dprd',
        'dpr_prov',
        'dpri_ri',
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
