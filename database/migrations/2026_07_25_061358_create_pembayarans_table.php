<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservasi_id')
                ->constrained('reservasi')->cascadeOnDelete();

            // Admin yang memverifikasi pembayaran (nullable, cash bisa tanpa verifikasi manual)
            $table->foreignId('diverifikasi_oleh')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->enum('jenis_pembayaran', ['DP', 'Pelunasan', 'Full'])->default('Full');
            $table->enum('metode', ['Cash', 'Transfer', 'E-Wallet', 'Payment Gateway']);
            $table->decimal('jumlah_bayar', 10, 2);
            $table->string('bukti_pembayaran')->nullable()->comment('path file bukti transfer');
            $table->enum('status_pembayaran', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->timestamp('tanggal_bayar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};