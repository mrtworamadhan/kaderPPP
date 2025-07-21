<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_rtrw', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kecamatan');
            $table->string('kecamatan');
            $table->unsignedBigInteger('id_desa');
            $table->string('desa');
            $table->integer('jumlah_rw')->default(0);
            $table->integer('jumlah_rt')->default(0);
            $table->integer('jumlah_tps')->default(0);
            $table->integer('jumlah_dpt')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_rtrw');
    }
};