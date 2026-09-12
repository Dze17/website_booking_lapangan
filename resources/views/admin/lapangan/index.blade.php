@extends('layouts.admin')

@section('title', 'Kelola Data Lapangan - SM Sport Center')
@section('page-title', 'Kelola Data Lapangan')

@section('header-action')
    <button type="button" onclick="openTambahLapangan()"
            class="px-5 py-3 bg-[#10B981] hover:bg-emerald-600 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg shadow-[#10B981]/25 transition active:scale-[0.98]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span class="hidden sm:inline">Tambah Lapangan Baru</span>
        <span class="sm:hidden">Tambah</span>
    </button>
@endsection

@section('content')

    @if (session('success'))
        <div class="bg-[#DCFCE7] text-[#15803D] text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <p class="text-xs text-slate-500 -mt-2">
        <span class="font-bold text-[#10B981]">{{ $jumlahAktif }} Lapangan Aktif</span> dari {{ $lapangan->count() }} total &middot; 2 Cabang Olahraga (Futsal & Badminton)
    </p>

    {{-- ============ SEARCH & FILTER ============ --}}
    <form method="GET" action="{{ route('admin.lapangan.index') }}"
          class="flex flex-wrap items-center gap-2.5">
        <div class="relative flex-1 sm:w-64 sm:flex-none">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Cari nama atau kode lapangan..."
                   class="w-full pl-10 pr-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#10B981]">
        </div>

        <select name="jenis" class="px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A]">
            <option value="">Semua Olahraga</option>
            <option value="Futsal" {{ ($filter['jenis'] ?? null) === 'Futsal' ? 'selected' : '' }}>Futsal</option>
            <option value="Badminton" {{ ($filter['jenis'] ?? null) === 'Badminton' ? 'selected' : '' }}>Badminton</option>
        </select>

        <select name="status" class="px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A]">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ ($filter['status'] ?? null) === 'Aktif' ? 'selected' : '' }}>Aktif Beroperasi</option>
            <option value="Maintenance" {{ ($filter['status'] ?? null) === 'Maintenance' ? 'selected' : '' }}>Perbaikan / Maintenance</option>
            <option value="Nonaktif" {{ ($filter['status'] ?? null) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit" class="px-3.5 py-2.5 bg-[#0F172A] text-white rounded-xl text-xs font-semibold">Terapkan</button>
        <a href="{{ route('admin.lapangan.index') }}" class="px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-500">Reset</a>
    </form>

    {{-- ============ GRID KARTU LAPANGAN ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($lapangan as $l)
            @php
                $isMaintenance = $l->status === 'Maintenance';
                $jenisColor = $l->jenis_lapangan === 'Futsal' ? 'bg-[#10B981]' : 'bg-blue-600';
                $jenisIcon = $l->jenis_lapangan === 'Futsal' ? '⚽' : '🏸';
            @endphp
            <div class="bg-white rounded-2xl border {{ $isMaintenance ? 'border-red-200' : 'border-[#E2E8F0]' }} overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between {{ $isMaintenance ? 'opacity-90' : '' }}">
                <div>
                    {{-- Thumbnail --}}
                    <div class="relative h-44 bg-slate-900">
                        @if ($l->gambar_url)
                            <img src="{{ $l->gambar_url }}" alt="{{ $l->nama_lapangan }}"
                                 class="w-full h-full object-cover {{ $isMaintenance ? 'opacity-60 grayscale' : 'opacity-90' }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl {{ $isMaintenance ? 'grayscale opacity-50' : 'opacity-70' }}">
                                {{ $jenisIcon }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t {{ $isMaintenance ? 'from-[#0F172A]/90' : 'from-[#0F172A]/80' }} via-transparent to-transparent"></div>

                        <div class="absolute top-3 left-3 right-3 flex justify-between items-center">
                            <span class="px-2.5 py-1 {{ $jenisColor }} text-white text-[10px] font-extrabold rounded-lg uppercase tracking-wider">
                                {{ $jenisIcon }} {{ $l->jenis_lapangan }}
                            </span>
                            @if ($isMaintenance)
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-lg border border-red-300">🛠️ Maintenance</span>
                            @elseif ($l->status === 'Nonaktif')
                                <span class="px-2.5 py-1 bg-slate-200 text-slate-600 text-[10px] font-bold rounded-lg">Nonaktif</span>
                            @else
                                <span class="px-2.5 py-1 bg-emerald-100 text-[#10B981] text-[10px] font-bold rounded-lg border border-emerald-300">● Aktif</span>
                            @endif
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            @if ($l->kode_lapangan)
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">KODE: {{ $l->kode_lapangan }}</span>
                            @endif
                            <h3 class="text-base font-extrabold leading-snug">{{ $l->nama_lapangan }}</h3>
                        </div>
                    </div>

                    {{-- Detail --}}
                    <div class="p-5 space-y-4 text-xs">
                        @if ($l->tipe_lantai || $l->lokasi)
                            <div class="grid grid-cols-2 gap-3 py-2 border-b border-[#E2E8F0]">
                                <div>
                                    <p class="text-slate-400 font-medium">Tipe Lantai</p>
                                    <p class="font-bold text-[#0F172A] mt-0.5">{{ $l->tipe_lantai ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400 font-medium">Lokasi</p>
                                    <p class="font-bold text-[#0F172A] mt-0.5">{{ $l->lokasi ?? '-' }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Tarif Sewa</span>
                            <span class="font-extrabold text-[#0F172A]">
                                Rp{{ number_format($l->harga_per_jam, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 font-normal">/jam</span>
                            </span>
                        </div>

                        @if (!empty($l->fasilitas))
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach ($l->fasilitas as $f)
                                    <span class="px-2 py-0.5 bg-[#F8FAFC] border border-[#E2E8F0] text-slate-600 text-[10px] rounded-md font-semibold">{{ $f }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Aksi --}}
                <div class="p-4 {{ $isMaintenance ? 'bg-red-50/50 border-t border-red-100' : 'bg-[#F8FAFC] border-t border-[#E2E8F0]' }} flex items-center justify-between gap-2">
                    <button type="button" onclick='openEditLapangan(@json($l))'
                            class="px-3 py-2 bg-white border border-[#E2E8F0] hover:bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs transition">
                        ✏️ Edit Data
                    </button>

                    <form method="POST" action="{{ route('admin.lapangan.toggle-status', $l) }}"
                          onsubmit="return confirm('{{ $isMaintenance ? 'Aktifkan kembali lapangan ini?' : 'Tandai lapangan ini sedang perbaikan?' }}')">
                        @csrf @method('PATCH')
                        @if ($isMaintenance)
                            <button class="px-3 py-2 bg-[#10B981] hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition">
                                ✓ Aktifkan Lapangan
                            </button>
                        @else
                            <button class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-xl text-xs transition">
                                🔒 Perbaikan
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-slate-400 text-sm py-16">
                Tidak ada lapangan yang cocok dengan filter ini.
            </div>
        @endforelse
    </div>

    @include('admin.lapangan._modal-form')

@endsection