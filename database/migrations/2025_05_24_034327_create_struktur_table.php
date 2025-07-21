<?php

// database/migrations/xxxx_xx_xx_create_struktur_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('struktur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->enum('tingkat', ['dpac', 'dprt']);
            $table->string('jabatan');
            $table->string('bagian')->nullable();
            $table->integer('urutan')->nullable();
            $table->foreignId('id_desa')->nullable()->constrained('daerah')->onDelete('set null');
            $table->foreignId('id_kecamatan')->nullable()->constrained('daerah')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('struktur');
    }
};
