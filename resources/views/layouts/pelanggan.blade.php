<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - SM Sport Center')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex flex-col">

    @php
        $navItems = [
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Pesanan Saya', 'route' => 'reservasi-saya.index'],
        ];
    @endphp

    {{-- ============ NAVBAR ============ --}}
    <nav class="bg-white border-b border-[#E2E8F0] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <div class="flex items-center justify-center shadow-md shadow-[#10B981]/20">
                            <img src="{{ asset('images/logo.svg') }}" alt="SM Sport Center" class="w-10 h-10">
                        </div>
                        <span class="text-lg font-bold text-[#0F172A] tracking-wider">SM SPORT</span>
                    </a>

                    <div class="hidden md:flex items-center gap-6 text-sm font-semibold">
                        @foreach ($navItems as $item)
                            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                               class="pb-5 pt-5 transition {{ request()->routeIs($item['route']) ? 'text-[#10B981] border-b-2 border-[#10B981]' : 'text-slate-500 hover:text-[#0F172A]' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <button class="p-2 text-slate-400 hover:text-[#0F172A] rounded-xl hover:bg-slate-100 transition relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#10B981] rounded-full"></span>
                    </button>

                    <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-slate-200">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&background=0F172A&color=fff"
                             alt="{{ auth()->user()->nama }}" class="w-9 h-9 rounded-xl">
                        <div class="text-left">
                            <p class="text-xs font-bold text-[#0F172A]">{{ auth()->user()->nama }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-[10px] font-semibold text-slate-400 hover:text-red-500">Keluar</button>
                            </form>
                        </div>
                    </div>

                    <button id="burgerBtn" class="md:hidden p-2 text-slate-500 hover:text-[#0F172A] rounded-xl hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18M3 18h18" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Nav mobile (off-canvas sederhana, dropdown ke bawah) --}}
        <div id="navMobile" class="hidden md:hidden border-t border-[#E2E8F0] bg-white">
            <div class="px-4 py-2 space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="block px-2 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs($item['route']) ? 'text-[#10B981] bg-emerald-50' : 'text-slate-600' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <div class="flex items-center gap-3 px-2 py-3 border-t border-[#E2E8F0] mt-1">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&background=0F172A&color=fff"
                         class="w-8 h-8 rounded-xl" alt="{{ auth()->user()->nama }}">
                    <div>
                        <p class="text-xs font-bold text-[#0F172A]">{{ auth()->user()->nama }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-[10px] font-semibold text-slate-400">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if (session('success'))
            <div class="bg-[#DCFCE7] text-[#15803D] text-sm font-medium px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @yield('modals')

    <script>
        document.getElementById('burgerBtn')?.addEventListener('click', () => {
            document.getElementById('navMobile').classList.toggle('hidden');
        });

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.getElementById(modalId).classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.getElementById(modalId).classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    </script>

    @yield('scripts')
</body>
</html>