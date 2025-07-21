<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'event_attendances';

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'anggota_id',
        'attended_at',
    ];

    /**
     * Menonaktifkan timestamps (created_at, updated_at) karena kita sudah punya attended_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relasi: Setiap catatan absensi dimiliki oleh satu Event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relasi: Setiap catatan absensi dimiliki oleh satu Anggota.
     */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
