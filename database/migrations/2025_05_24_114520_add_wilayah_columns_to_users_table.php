<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kecamatan')->nullable()->after('role');
            $table->unsignedBigInteger('id_desa')->nullable()->after('id_kecamatan');

            // Tambahkan foreign key opsional (jika ingin menjaga relasi)
            $table->foreign('id_kecamatan')->references('id')->on('daerah')->onDelete('set null');
            $table->foreign('id_desa')->references('id')->on('daerah')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_kecamatan']);
            $table->dropForeign(['id_desa']);
            $table->dropColumn(['id_kecamatan', 'id_desa']);
        });
    }
};