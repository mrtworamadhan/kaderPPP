<?php
// database/migrations/xxxx_xx_xx_create_daerah_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('daerah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedBigInteger('parent_id')->nullable(); // null = kecamatan, isi = desa
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('daerah');
    }
};
