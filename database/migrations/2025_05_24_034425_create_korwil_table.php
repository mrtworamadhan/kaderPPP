<?php

// database/migrations/xxxx_xx_xx_create_korwil_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('korwil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->enum('tingkat', ['korw', 'kort']);
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->foreignId('id_desa')->nullable()->constrained('daerah')->onDelete('set null');
            $table->foreignId('id_kecamatan')->nullable()->constrained('daerah')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korwil');
    }
};
