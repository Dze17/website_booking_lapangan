<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboardController;
use App\Http\Controllers\Pelanggan\BookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tambahkan blok route di bawah ini ke routes/web.php yang sudah ada.
| Jangan ganti seluruh isi routes/web.php, cukup gabungkan bagian ini.
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Hanya bisa diakses jika BELUM login sebagai user
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // Registrasi
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Lupa kata sandi (kirim tautan reset)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    // Atur ulang kata sandi (dari tautan email)
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

// Hanya bisa diakses jika SUDAH login sebagai pelanggan
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');
 
    Route::get('/booking/slot-tersedia', [BookingController::class, 'slotTersedia'])->name('booking.slot-tersedia');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    
    Route::get('/reservasi-saya', [BookingController::class, 'index'])->name('reservasi-saya.index');
    Route::get('/reservasi-saya/{reservasi}', [BookingController::class, 'show'])->name('reservasi-saya.show');
    Route::patch('/reservasi-saya/{reservasi}/batalkan', [BookingController::class, 'cancel'])->name('reservasi-saya.cancel');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Sementara diarahkan ke view placeholder;
    // akan diganti dengan DashboardController pada tahap berikutnya.
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
});

// Seluruh route /admin/* (dashboard, reservasi, lapangan, pembayaran, dst.)
// didaftarkan di file terpisah routes/admin.php supaya tidak menumpuk di sini.
require __DIR__.'/admin.php';