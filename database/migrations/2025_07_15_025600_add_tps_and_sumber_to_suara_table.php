<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suara', function (Blueprint $table) {
            // Menambahkan kolom setelah kolom 'dpr_ri'
            $table->string('tps')->nullable()->after('dpr_ri');
            $table->string('sumber')->nullable()->after('tps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suara', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn(['tps', 'sumber']);
        });
    }
};