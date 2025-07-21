<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PoinLog extends Model
{
    use HasFactory;
    protected $table = 'poin_logs';
    protected $fillable = [
        'anggota_id',
        'event_id',
        'points',
        'description'
    ];
    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}