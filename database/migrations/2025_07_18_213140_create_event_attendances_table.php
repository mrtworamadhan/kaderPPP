<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();

            // Menghubungkan ke event mana
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            // Menghubungkan ke anggota mana yang hadir
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');

            // Mencatat waktu absensi
            $table->timestamp('attended_at')->useCurrent();

            // Menambahkan unique constraint agar satu anggota tidak bisa absen dua kali di event yang sama
            $table->unique(['event_id', 'anggota_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
