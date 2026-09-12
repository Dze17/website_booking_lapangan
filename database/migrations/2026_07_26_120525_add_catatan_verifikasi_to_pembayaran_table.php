<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan catatan admin saat memverifikasi/menolak pembayaran
 * (dibutuhkan oleh fitur Transaksi & Keuangan — tombol Verifikasi/Tolak).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->text('catatan_verifikasi')->nullable()->after('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('catatan_verifikasi');
        });
    }
};