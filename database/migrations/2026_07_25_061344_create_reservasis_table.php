<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();

            // NULL jika pelanggan walk-in tanpa akun
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->foreignId('lapangan_id')
                ->constrained('lapangan')->restrictOnDelete();

            // Siapa yang menginput data reservasi ini (pelanggan sendiri / admin)
            $table->foreignId('dibuat_oleh')->nullable()
                ->constrained('users')->nullOnDelete();

            // Diisi jika reservasi walk-in tanpa akun (user_id NULL)
            $table->string('nama_tamu', 100)->nullable();
            $table->string('no_telepon_tamu', 20)->nullable();

            $table->date('tanggal_main');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_harga', 10, 2);

            $table->enum('tipe_input', ['Online', 'Manual'])->default('Online');
            $table->enum('status_reservasi', ['Pending', 'Dikonfirmasi', 'Dibatalkan', 'Selesai'])
                ->default('Pending');

            $table->timestamps();

            // Mencegah double booking: satu lapangan tidak bisa dipesan pada
            // tanggal & jam mulai yang sama lebih dari satu kali.
            $table->unique(['lapangan_id', 'tanggal_main', 'jam_mulai'], 'uq_jadwal_lapangan');

            $table->index('tanggal_main');
            $table->index('status_reservasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};