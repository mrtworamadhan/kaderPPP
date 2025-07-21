<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('struktur', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('anggota_id')->constrained('roles')->onDelete('set null');
        });
        Schema::table('korwil', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('anggota_id')->constrained('roles')->onDelete('set null');
        });
    }
    
    public function down(): void {
        Schema::table('struktur', function (Blueprint $table) { $table->dropForeign(['role_id']); $table->dropColumn('role_id'); });
        Schema::table('korwil', function (Blueprint $table) { $table->dropForeign(['role_id']); $table->dropColumn('role_id'); });
    }
};