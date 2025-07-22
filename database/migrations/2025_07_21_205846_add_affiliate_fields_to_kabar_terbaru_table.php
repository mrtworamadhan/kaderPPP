<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('kabar_terbaru', function (Blueprint $table) {
            // Link asli ke media sosial (misal: Instagram, Facebook, TikTok)
            $table->string('url_target')->nullable()->after('gambar');
            // Poin yang didapat kader per klik unik
            $table->integer('points_per_click')->default(0)->after('url_target');
            // Kode unik untuk shorten link, agar link share lebih pendek
            $table->string('share_code')->unique()->nullable()->after('points_per_click');
        });
    }

    public function down(): void
    {
        Schema::table('kabar_terbaru', function (Blueprint $table) {
            $table->dropColumn(['url_target', 'points_per_click', 'share_code']);
        });
    }

};
