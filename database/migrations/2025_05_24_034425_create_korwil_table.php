<?php

// database/migrations/xxxx_xx_xx_create_korwil_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('korwil', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['korw', 'kort']);
            $table->string('nik');
            $table->string('nama');
            $table->string('phone')->nullable();
            $table->string('rt')->nullable(); // kosong jika korw
            $table->string('rw')->nullable();
            $table->string('desa')->nullable();
            $table->unsignedBigInteger('id_desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->unsignedBigInteger('id_kecamatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('korwil');
    }
};
