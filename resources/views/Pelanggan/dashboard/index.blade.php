@extends('layouts.pelanggan')

@section('title', 'Dashboard - SM Sport Center')

@section('content')

    {{-- ============ WELCOME BANNER ============ --}}
    <div class="relative bg-[#0F172A] rounded-3xl p-6 sm:p-8 text-white overflow-hidden shadow-xl">
        <div class="absolute right-0 top-0 bottom-0 w-1/2 opacity-20 bg-cover bg-center hidden md:block"
             style="background-image: url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80');"></div>
        <div class="relative z-10 max-w-xl">
            <span class="px-3 py-1 bg-[#10B981]/20 text-[#10B981] text-xs font-semibold rounded-full border border-[#10B981]/30">
                Halo, {{ auth()->user()->nama }} 👋
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-3 leading-tight">
                Siap Olahraga Hari Ini? Lapangan Favoritmu Menunggu.
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-2">
                Cek jadwal main terdekatmu atau langsung pesan slot lapangan baru sekarang!
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <button onclick="openModal('modalBookingBaru')"
                        class="px-5 py-2.5 bg-[#10B981] hover:bg-emerald-600 text-white font-semibold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-[#10B981]/30">
                    + Pesan Lapangan Baru
                </button>
                <a href="#jadwalTerdekat" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs sm:text-sm backdrop-blur-md transition">
                    Lihat Riwayat Main
                </a>
            </div>
        </div>
    </div>

    {{-- ============ STATS ============ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-[#10B981] rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jadwal Mendatang</p>
                <p class="text-xl font-bold text-[#0F172A]">{{ $jumlahMendatang }} Pemesanan</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-slate-100 text-[#0F172A] rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Jam Main</p>
                <p class="text-xl font-bold text-[#0F172A]">{{ $totalJamMain }} Jam</p>
            </div>
        </div>
    </div>

    {{-- ============ PESAN LAYANAN CEPAT ============ --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#0F172A]">Pesan Layanan Cepat</h2>
                <p class="text-xs text-slate-500">Pilih lapangan favoritmu dan amankan slot waktu dalam hitungan detik</p>
            </div>
            <span class="hidden sm:block text-xs font-semibold text-slate-400">← Geser untuk lihat semua →</span>
        </div>

        <div class="flex gap-5 overflow-x-auto pb-4 pt-1 snap-x snap-mandatory no-scrollbar">
            @forelse ($lapanganList as $l)
                @php
                    $jenisColor = $l->jenis_lapangan === 'Futsal' ? 'bg-[#10B981]' : 'bg-blue-600';
                @endphp
                <div class="min-w-[280px] max-w-[280px] bg-white rounded-2xl border border-[#E2E8F0] overflow-hidden shadow-sm hover:shadow-md transition shrink-0 snap-start flex flex-col justify-between">
                    <div>
                        <div class="relative h-32 bg-slate-900">
                            @if ($l->gambar_url)
                                <img src="{{ $l->gambar_url }}" alt="{{ $l->nama_lapangan }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl opacity-70">
                                    {{ $l->jenis_lapangan === 'Futsal' ? '⚽' : '🏸' }}
                                </div>
                            @endif
                            <span class="absolute top-3 left-3 px-2 py-0.5 {{ $jenisColor }} text-white text-[10px] font-extrabold rounded-md uppercase">
                                {{ $l->jenis_lapangan }}
                            </span>
                        </div>
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-[#0F172A]">
                                    Rp{{ number_format($l->harga_per_jam, 0, ',', '.') }} <span class="text-slate-400 font-normal">/jam</span>
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $l->lokasi ?? 'Indoor' }}</span>
                            </div>
                            <h4 class="font-bold text-sm text-[#0F172A] line-clamp-1">{{ $l->nama_lapangan }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-1">{{ $l->tipe_lantai ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <button type="button"
                                onclick="openQuickBookModal({{ $l->id }}, '{{ addslashes($l->nama_lapangan) }}', {{ $l->harga_per_jam }}, '{{ $l->gambar_url ?? '' }}')"
                                class="w-full py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition active:scale-[0.98]">
                            Pilih Jam
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 py-6">Belum ada lapangan aktif tersedia.</p>
            @endforelse
        </div>
    </div>

    {{-- ============ JADWAL MAIN TERDEKAT ============ --}}
    <div id="jadwalTerdekat" class="space-y-4 pt-4 border-t border-slate-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#0F172A]">Jadwal Main Terdekat</h2>
            <a href="{{ Route::has('reservasi-saya.index') ? route('reservasi-saya.index') : '#' }}"
               class="text-xs font-semibold text-[#10B981] hover:underline">Lihat Semua Pesanan</a>
        </div>

        @if ($reservasiTerdekat)
            @php
                $statusBadge = $reservasiTerdekat->status_reservasi === 'Dikonfirmasi'
                    ? ['label' => 'Dikonfirmasi', 'class' => 'bg-emerald-100 text-[#10B981]']
                    : ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-amber-100 text-amber-700'];
                $barColor = $reservasiTerdekat->status_reservasi === 'Dikonfirmasi' ? 'bg-[#10B981]' : 'bg-amber-400';
            @endphp
            <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 shadow-sm relative overflow-hidden flex flex-col md:flex-row justify-between gap-6">
                <div class="absolute left-0 top-0 bottom-0 w-2 {{ $barColor }}"></div>

                <div class="space-y-4 flex-1 pl-2">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 {{ $statusBadge['class'] }} text-[11px] font-bold rounded-lg uppercase">{{ $statusBadge['label'] }}</span>
                        <span class="text-xs font-semibold text-slate-400">ID Booking: #SP-{{ str_pad($reservasiTerdekat->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-[#0F172A]">{{ $reservasiTerdekat->lapangan->nama_lapangan }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">SM Sport Center &middot; {{ $reservasiTerdekat->lapangan->lokasi ?? 'Indoor' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-3 border-y border-dashed border-slate-200 text-xs">
                        <div>
                            <p class="text-slate-400 font-medium">Tanggal</p>
                            <p class="font-bold text-[#0F172A] mt-0.5">{{ $reservasiTerdekat->tanggal_main->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Waktu / Jam</p>
                            <p class="font-bold text-[#10B981] mt-0.5">
                                {{ \Carbon\Carbon::parse($reservasiTerdekat->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasiTerdekat->jam_selesai)->format('H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Ganti blok QR dengan tombol Detail Transaksi --}}
                <div class="bg-[#F8FAFC] border border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center shrink-0 w-full md:w-44 text-center gap-2">
                    <div class="w-14 h-14 bg-white rounded-full border border-slate-200 shadow-sm flex items-center justify-center text-2xl">
                        🎟️
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Rp{{ number_format($reservasiTerdekat->total_harga, 0, ',', '.') }}</p>
                    <button type="button" onclick="openDetailTransaksi({{ $reservasiTerdekat->id }})"
                            class="w-full py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-lg text-[11px] font-bold transition">
                        Lihat Detail Transaksi
                    </button>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-dashed border-[#E2E8F0] p-10 text-center">
                <p class="text-sm text-slate-500">Belum ada jadwal main mendatang.</p>
                <button onclick="openModal('modalBookingBaru')" class="mt-3 text-xs font-bold text-[#10B981] hover:underline">
                    Yuk, pesan lapangan sekarang →
                </button>
            </div>
        @endif
    </div>

@endsection

@section('modals')
    @include('Pelanggan.dashboard._modal-booking-baru')
    @include('Pelanggan.dashboard._modal-quick-book')
    @include('Pelanggan.dashboard._modal-detail-transaksi')
@endsection