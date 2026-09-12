<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - SM Sport Center')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex">

    @php
        $navItems = [
            ['label' => 'Dashboard Overview', 'route' => 'admin.dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
            ['label' => 'Kelola Reservasi', 'route' => 'admin.reservasi.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Data Lapangan', 'route' => 'admin.lapangan.index', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['label' => 'Transaksi & Keuangan', 'route' => 'admin.pembayaran.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ];
    @endphp

    {{-- ============ SIDEBAR (Desktop) ============ --}}
    <aside class="w-64 bg-[#0F172A] text-white hidden md:flex flex-col justify-between shrink-0 p-5 fixed h-screen top-0 left-0">
        <div class="space-y-8">
            <div class="flex items-center gap-3 px-2">
                <div class="flex items-center justify-center shadow-md shadow-[#10B981]/30">
                    <img src="{{ asset('images/logo.svg') }}" class="w-18 h-18" alt="Logo SM Sport Center">
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-wider block">SM SPORT CENTER</span>
                    <span class="text-[10px] font-bold text-[#10B981] tracking-widest uppercase">Admin Panel</span>
                </div>
            </div>

            <nav class="space-y-1 text-xs font-semibold">
                @foreach ($navItems as $item)
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                              {{ request()->routeIs($item['route'])
                                    ? 'bg-[#10B981] text-white shadow-md shadow-[#10B981]/20'
                                    : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&background=10B981&color=fff"
                     class="w-8 h-8 rounded-lg" alt="{{ auth()->user()->nama }}">
                <div class="text-xs">
                    <p class="font-bold text-white">{{ auth()->user()->nama }}</p>
                    <p class="text-[10px] text-slate-400">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-400 transition" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- ============ SIDEBAR (Mobile off-canvas) ============ --}}
    <div id="mobileSidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>
    <aside id="mobileSidebar"
           class="fixed inset-y-0 left-0 w-64 bg-[#0F172A] text-white z-50 p-5 -translate-x-full transition-transform duration-200 md:hidden">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-[#10B981] rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-extrabold tracking-wider">SM SPORT CENTER</span>
            </div>
            <button id="closeMobileSidebar" class="text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <nav class="space-y-1 text-xs font-semibold">
            @foreach ($navItems as $item)
                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                          {{ request()->routeIs($item['route']) ? 'bg-[#10B981] text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </aside>

    {{-- ============ MAIN AREA ============ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <header class="bg-white border-b border-[#E2E8F0] h-16 flex items-center justify-between px-4 sm:px-6 z-20 ml-64">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <button id="openMobileSidebar" class="md:hidden text-slate-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18M3 18h18" /></svg>
                </button>
                <h1 class="text-base sm:text-lg font-extrabold text-[#0F172A] truncate">@yield('page-title', 'Ringkasan Operasional')</h1>
                <span class="hidden sm:inline-block px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg shrink-0">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @yield('header-action')
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 ml-64">
            @yield('content')
        </main>
    </div>

    <script>
        const mobileSidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('mobileSidebarOverlay');
        const openBtn = document.getElementById('openMobileSidebar');
        const closeBtn = document.getElementById('closeMobileSidebar');

        function openSidebar() {
            mobileSidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }
        function closeSidebar() {
            mobileSidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    </script>
</body>
</html>