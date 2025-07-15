<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kabar_terbaru', function (Blueprint $table) {
            $table->id();
            $table->text('deskripsi');
            $table->text('link');
            $table->string('gambar')->nullable(); // path gambar atau URL
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabar_terbaru');
    }
};
