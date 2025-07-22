<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;

    protected $table = 'rewards';

    protected $fillable = [
        'name',
        'description',
        'image',
        'points_needed',
        'kuota',
        'created_at',
        'updated_at',
    ];
}
