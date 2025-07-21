<?php

// database/migrations/xxxx_xx_xx_create_suara_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('suara', function (Blueprint $table) {
            $table->id();
            $table->string('tahun')->nullable();
            $table->string('desa')->nullable();
            $table->unsignedBigInteger('id_desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->unsignedBigInteger('id_kecamatan')->nullable();
            $table->integer('dprd')->default(0);
            $table->integer('dpr_prov')->default(0);
            $table->integer('dpr_ri')->default(0);
            $table->string('tps')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('suara');
    }
};
