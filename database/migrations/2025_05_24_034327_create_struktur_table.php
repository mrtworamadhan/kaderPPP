<?php

// database/migrations/xxxx_xx_xx_create_struktur_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('struktur', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['dpc', 'dpac', 'dprt']);
            $table->string('nik'); // relasi ke anggota (by NIK)
            $table->string('nama');
            $table->string('jabatan');
            $table->string('bagian')->nullable(); // bidang/komisi jika ada
            $table->integer('urutan')->nullable();
            $table->string('desa')->nullable();
            $table->unsignedBigInteger('id_desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->unsignedBigInteger('id_kecamatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('struktur');
    }
};
