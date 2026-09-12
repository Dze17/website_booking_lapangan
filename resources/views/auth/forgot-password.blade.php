@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi - SM Sport Center')
@section('headline-1', 'Lupa Kata Sandi?')
@section('headline-2', 'Tenang, Kami Bantu.')

@section('content')

    <h2 class="font-display font-bold text-2xl lg:text-3xl mb-1 text-center lg:text-left">
        Lupa Kata Sandi
    </h2>
    <p class="text-sm text-[#4B5C53] mb-7 text-center lg:text-left">
        Masukkan email akunmu, kami kirimkan tautan untuk atur ulang kata sandi.
    </p>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-[#DCEBE0] text-[#2F7A4D] text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-[#4B5C53] mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus placeholder="nama@email.com"
                   class="w-full rounded-lg border border-[#DCE2D8] px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#DCEBE0] focus:border-[#2F7A4D]">
            @error('email')
                <p class="mt-1.5 text-xs text-[#C1594A]">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-[#0E2318] py-3.5 text-sm font-semibold text-[#F3F1E6]
                       hover:bg-[#2F7A4D] transition-colors">
            Kirim Tautan Reset
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-[#4B5C53]">
        Ingat kata sandimu?
        <a href="{{ route('login') }}" class="font-bold text-[#2F7A4D] hover:underline">Kembali ke Masuk</a>
    </p>

@endsection