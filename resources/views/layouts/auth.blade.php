<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SM Sport Center')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-display { font-family: 'Barlow Condensed', sans-serif; }
    </style>
</head>
<body class="font-sans antialiased bg-[#EFF2EC] text-[#12201A]">

    <div class="min-h-screen lg:grid lg:grid-cols-2">

        {{-- =======================================================
             PANEL KIRI — hanya tampil di layar desktop (lg ke atas)
             Judul besar bisa diganti tiap halaman lewat @section
             ======================================================= --}}
        <div class="hidden lg:flex relative overflow-hidden bg-[#0E2318]">
            {{-- Ganti src di bawah dengan foto lapangan futsal/badminton SM Sport Center --}}
            <img src="{{ asset('images/download.jpg') }}" alt="Lapangan SM Sport Center"
                 onerror="this.style.display='none'"
                 class="absolute inset-0 h-full w-full object-cover opacity-70">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0E2318] via-[#0E2318]/50 to-[#0E2318]/10"></div>

            <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo SM Sport Center" class="w-20 h-20">
                    <span class="font-display font-bold text-[19px] tracking-wide text-[#F3F1E6]">SM SPORT CENTER</span>
                </div>

                <div class="max-w-md">
                    <h1 class="font-display font-bold text-4xl leading-tight text-[#FBFAF5] mb-4">
                        @yield('headline-1', 'Booking Lapangan Tanpa Ribet.')
                        <span class="text-[#E8A63B]">@yield('headline-2', 'Pesan Sekarang!')</span>
                    </h1>
                    <p class="text-sm text-[#C9D6CD]">
                        2 Lapangan Futsal &middot; 3 Lapangan Badminton &middot; Buka setiap hari 08.00&ndash;23.00
                    </p>
                </div>
            </div>
        </div>

        {{-- =======================================================
             PANEL KANAN — konten spesifik tiap halaman (@yield content)
             ======================================================= --}}
        <div class="flex items-center justify-center px-6 py-10 lg:py-16">
            <div class="w-full max-w-sm">

                {{-- Logo, hanya tampil di mobile --}}
                <div class="flex flex-col items-center lg:hidden mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-[#0E2318] flex items-center justify-center mb-3">

                        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <!-- Badminton Shuttlecock Feather / Arc -->
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                            <!-- Soccer Ball Pentagram Lines -->
                            <circle cx="12" cy="17" r="4" />
                            <path d="M12 13v8" />
                            <path d="M8 17h8" />
                        </svg>
                    </div>
                    <span class="font-display font-bold text-[19px] tracking-wide text-[#F3F1E6]">SM SPORT CENTER</span>
                </div>

                @yield('content')

            </div>
        </div>
    </div>
</body>
</html>