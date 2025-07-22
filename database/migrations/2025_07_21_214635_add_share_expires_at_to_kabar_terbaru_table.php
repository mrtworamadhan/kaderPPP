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
        Schema::table('kabar_terbaru', function (Blueprint $table) {
            // Menambahkan kolom untuk tanggal kedaluwarsa poin affiliate
            // Dibuat nullable agar konten lama tidak error dan tidak semua konten harus punya tanggal kedaluwarsa
            $table->timestamp('share_expires_at')->nullable()->after('points_per_click');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kabar_terbaru', function (Blueprint $table) {
            $table->dropColumn('share_expires_at');
        });
    }
};
