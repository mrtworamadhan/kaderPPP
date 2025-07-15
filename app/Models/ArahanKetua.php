<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArahanKetua extends Model
{
    use HasFactory;

    protected $table = 'arahan_ketua';

    protected $fillable = [
        'tanggal',
        'arahan',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
    ];
}
