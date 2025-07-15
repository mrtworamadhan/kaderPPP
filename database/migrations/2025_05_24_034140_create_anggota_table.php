<?php

// database/migrations/xxxx_xx_xx_create_anggota_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('id_anggota')->unique(); // format: 90901320100001
            $table->string('nama');
            $table->string('phone')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('gender', ['l', 'p'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('desa')->nullable();
            $table->unsignedBigInteger('id_desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->unsignedBigInteger('id_kecamatan')->nullable();
            $table->string('jabatan')->nullable(); // dpc, dpac, dprt, anggota
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('anggota');
    }
};
