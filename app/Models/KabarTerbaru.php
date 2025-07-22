<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KabarTerbaru extends Model
{
    use HasFactory;

    protected $table = 'kabar_terbaru';

    protected $fillable = [
        'judul',
        'link',
        'foto',
        'url_target',
        'points_per_click',
        'share_expires_at',
        'share_code',
    ];
}
