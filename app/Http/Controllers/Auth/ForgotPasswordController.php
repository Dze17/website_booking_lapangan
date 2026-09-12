<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan form permintaan reset password.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Mengirim tautan reset password ke email pelanggan
     * jika email terdaftar di sistem.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Broker "users" dikonfigurasi di config/auth.php
        // (lihat config-snippet/auth-snippet.php)
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Tautan reset kata sandi sudah dikirim ke email kamu.')
            : back()->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
    }
}