<?php

// database/migrations/xxxx_xx_xx_create_anggota_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('id_anggota')->unique()->nullable();
            $table->string('nama');
            $table->string('phone')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('gender', ['l', 'p'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->foreignId('id_desa')->nullable()->constrained('daerah')->onDelete('set null');
            $table->foreignId('id_kecamatan')->nullable()->constrained('daerah')->onDelete('set null');
            $table->string('foto')->nullable();
            $table->integer('total_poin')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
