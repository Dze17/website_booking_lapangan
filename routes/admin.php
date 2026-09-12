<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\LapanganController;
use App\Http\Controllers\Admin\PembayaranController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
        Route::get('/reservasi/{reservasi}', [ReservasiController::class, 'show'])->name('reservasi.show');
        Route::get('/reservasi/{reservasi}/edit', [ReservasiController::class, 'edit'])->name('reservasi.edit');
        Route::put('/reservasi/{reservasi}', [ReservasiController::class, 'update'])->name('reservasi.update');
        Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
        Route::patch('/reservasi/{reservasi}/konfirmasi', [ReservasiController::class, 'confirm'])->name('reservasi.confirm');
        Route::patch('/reservasi/{reservasi}/batalkan', [ReservasiController::class, 'cancel'])->name('reservasi.cancel');
        Route::post('/reservasi/{reservasi}/kirim-ulang', [ReservasiController::class, 'resendNotifikasi'])->name('reservasi.resend');

        Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
        Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');
        Route::put('/lapangan/{lapangan}', [LapanganController::class, 'update'])->name('lapangan.update');
        Route::patch('/lapangan/{lapangan}/toggle-status', [LapanganController::class, 'toggleStatus'])->name('lapangan.toggle-status');

        // Penting: /export didaftarkan SEBELUM route lain di prefix yang sama
        // supaya tidak tertangkap route model binding {pembayaran} (walau di sini
        // kita tidak punya GET /pembayaran/{id}, ini tetap best practice).
        Route::get('/pembayaran/export', [PembayaranController::class, 'export'])->name('pembayaran.export');
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::patch('/pembayaran/{pembayaran}/verifikasi', [PembayaranController::class, 'verify'])->name('pembayaran.verify');
        Route::patch('/pembayaran/{pembayaran}/tolak', [PembayaranController::class, 'reject'])->name('pembayaran.reject');
    });