<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_time',
        'end_time',
        'points_reward',
        'qr_code_token'
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'event_role');
    }
    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }
}