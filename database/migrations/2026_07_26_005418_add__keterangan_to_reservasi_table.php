<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom alasan pembatalan — dibutuhkan oleh modal Detail Reservasi
 * (field "Alasan Pembatalan" diisi admin saat menolak/membatalkan reservasi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->string('keterangan', 255)->nullable()->after('status_reservasi');
        });
    }

    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};