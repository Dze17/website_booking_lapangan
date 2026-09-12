@extends('layouts.auth')

@section('title', 'Daftar Akun - SM Sport Center')
@section('headline-1', 'Gabung Sekarang.')
@section('headline-2', 'Booking Jadi Lebih Mudah!')

@section('content')

    <h2 class="font-display font-bold text-2xl lg:text-3xl mb-1 text-center lg:text-left">
        Daftar Akun Baru
    </h2>
    <p class="text-sm text-[#4B5C53] mb-7 text-center lg:text-left">
        Buat akun untuk mulai booking lapangan favoritmu.
    </p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="nama" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Nama Lengkap
            </label>
            <input id="nama" type="text" name="nama" value="{{ old('nama') }}"
                   required autofocus placeholder="Nama sesuai KTP"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('nama')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required placeholder="nama@email.com"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('email')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        {{-- No. Telepon --}}
        <div>
            <label for="no_telepon" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                No. Telepon
            </label>
            <input id="no_telepon" type="tel" name="no_telepon" value="{{ old('no_telepon') }}"
                   required placeholder="08xxxxxxxxxx"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('no_telepon')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kata Sandi --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Kata Sandi
            </label>
            <input id="password" type="password" name="password" required
                   placeholder="Minimal 8 karakter"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('password')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Kata Sandi --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Konfirmasi Kata Sandi
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   placeholder="Ulangi kata sandi"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-[#0E2318] py-3.5 text-sm font-semibold text-[#F3F1E6]
                       hover:bg-[#2F7A4D] transition-colors">
            Daftar
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-[#4B5C53]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-[#2F7A4D] hover:underline">Masuk di sini</a>
    </p>

@endsection