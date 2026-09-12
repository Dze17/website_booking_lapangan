<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservasi_id')
                ->constrained('reservasi')->cascadeOnDelete();

            $table->enum('tipe', ['Konfirmasi', 'Pengingat', 'Pembatalan']);
            $table->text('pesan');
            $table->enum('status_kirim', ['Pending', 'Sent', 'Failed'])->default('Pending');
            $table->timestamp('dikirim_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};