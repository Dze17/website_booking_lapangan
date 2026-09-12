<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lapangan', 50);
            $table->enum('jenis_lapangan', ['Futsal', 'Badminton']);
            $table->decimal('harga_per_jam', 10, 2);
            $table->time('jam_buka')->default('08:00:00');
            $table->time('jam_tutup')->default('23:00:00');
            $table->enum('status', ['Aktif', 'Nonaktif', 'Maintenance'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapangan');
    }
};