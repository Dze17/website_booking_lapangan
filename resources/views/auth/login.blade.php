@extends('layouts.auth')

@section('title', 'Masuk - SM Sport Center')

@section('content')

    <h2 class="font-display font-bold text-2xl lg:text-3xl mb-1 text-center lg:text-left">
        LOGIN
    </h2>
    <p class="text-sm text-gray-600 mb-7 text-center lg:text-left">
        Kelola reservasi lapanganmu di SM Sport Center.
    </p>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-[#DCEBE0] text-[#2F7A4D] text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus placeholder="nama@email.com"
                   class="w-full rounded-lg border border-gray-400 px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('email')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Kata Sandi
            </label>
            <input id="password" type="password" name="password" required
                   placeholder="••••••••"
                   class="w-full rounded-lg border border-gray-400 px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('password')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ingat saya & Lupa kata sandi --}}
        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 text-gray-700 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-gray-400">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="font-semibold text-green-700 hover:underline">
                Lupa Kata Sandi?
            </a>
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-gray-800 py-3.5 text-sm font-semibold text-gray-100
                       hover:bg-green-800 transition-colors">
            Masuk
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-gray-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-green-700 hover:underline">Daftar Sekarang</a>
    </p>

@endsection