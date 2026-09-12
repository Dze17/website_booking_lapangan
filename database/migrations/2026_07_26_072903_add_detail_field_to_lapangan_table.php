<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            $table->string('kode_lapangan', 20)->nullable()->unique()->after('id');
            $table->string('tipe_lantai', 100)->nullable()->after('jenis_lapangan');
            $table->string('lokasi', 100)->nullable()->after('tipe_lantai');
            $table->json('fasilitas')->nullable()->after('status');
            $table->string('gambar')->nullable()->comment('path relatif di storage/app/public')->after('fasilitas');
        });
    }

    public function down(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            $table->dropColumn(['kode_lapangan', 'tipe_lantai', 'lokasi', 'fasilitas', 'gambar']);
        });
    }
};