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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama hadiah, e.g., "Umroh Gratis"
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // URL atau path ke gambar hadiah
            $table->unsignedInteger('points_needed'); // Poin yang dibutuhkan
            $table->integer('kuota')->default(-1); // Kuota hadiah, -1 berarti tidak terbatas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
