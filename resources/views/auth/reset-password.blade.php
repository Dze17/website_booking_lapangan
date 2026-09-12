@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi - SM Sport Center')
@section('headline-1', 'Buat Kata Sandi')
@section('headline-2', 'Baru yang Aman.')

@section('content')

    <h2 class="font-display font-bold text-2xl lg:text-3xl mb-1 text-center lg:text-left">
        Atur Ulang Kata Sandi
    </h2>
    <p class="text-sm text-[#4B5C53] mb-7 text-center lg:text-left">
        Buat kata sandi baru untuk akunmu.
    </p>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}"
                   required autofocus placeholder="nama@email.com"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('email')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Kata Sandi Baru
            </label>
            <input id="password" type="password" name="password" required
                   placeholder="Minimal 8 karakter"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('password')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Konfirmasi Kata Sandi Baru
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   placeholder="Ulangi kata sandi baru"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-[#0E2318] py-3.5 text-sm font-semibold text-[#F3F1E6]
                       hover:bg-[#2F7A4D] transition-colors">
            Simpan Kata Sandi Baru
        </button>
    </form>

@endsection