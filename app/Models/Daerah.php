<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Daerah extends Model
{
    protected $table = 'daerah';

    protected $fillable = [
        'nama',
        'parent_id',
    ];

    // Relasi ke daerah induk (misalnya desa ke kecamatan)
    public function parent()
    {
        return $this->belongsTo(Daerah::class, 'parent_id');
    }

    // Relasi ke anak-anak daerah (misalnya kecamatan ke daftar desa)
    public function children()
    {
        return $this->hasMany(Daerah::class, 'parent_id');
    }
}
