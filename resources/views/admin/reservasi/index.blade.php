@extends('layouts.admin')

@section('title', 'Kelola Reservasi - SM Sport Center')
@section('page-title', 'Kelola Reservasi')

@section('header-action')
  <button type="button" onclick="openBookingModal()"
      class="px-4 py-2.5 bg-[#10B981] hover:bg-emerald-600 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    <span class="hidden sm:inline">Booking Manual (Walk-In)</span>
    <span class="sm:hidden">Walk-In</span>
  </button>
@endsection

@section('content')

  @if (session('success'))
    <div class="bg-[#DCFCE7] text-[#15803D] text-sm font-medium px-4 py-3 rounded-xl">
      {{ session('success') }}
    </div>
  @endif

  <p class="text-[11px] text-slate-400 font-medium -mt-2">
    {{ $ringkasan['total_bulan_ini'] }} reservasi bulan ini &middot;
    {{ $ringkasan['menunggu_konfirmasi'] }} menunggu konfirmasi
  </p>

  {{-- ============ FILTER TOOLBAR ============ --}}
  <form method="GET" action="{{ route('admin.reservasi.index') }}"
    class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-wrap items-center gap-3">

    <div class="relative flex-1 min-w-[220px]">
      <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
      </svg>
      <input type="text" name="cari" value="{{ $filter['cari'] ?? '' }}"
        placeholder="Cari nama pelanggan atau no. telepon..."
        class="w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-medium placeholder:text-slate-400 focus:outline-none">
    </div>

    <input type="date" name="tanggal" value="{{ $filter['tanggal'] ?? '' }}"
      class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">

    <select name="lapangan_id" class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
      <option value="">Semua Lapangan</option>
      @foreach ($lapanganList as $l)
        <option value="{{ $l->id }}" {{ ($filter['lapangan_id'] ?? null) == $l->id ? 'selected' : '' }}>
          {{ $l->nama_lapangan }}
        </option>
      @endforeach
    </select>

    <select name="status" class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
      <option value="">Semua Status</option>
      @foreach (['Pending', 'Dikonfirmasi', 'Dibatalkan', 'Selesai'] as $s)
        <option value="{{ $s }}" {{ ($filter['status'] ?? null) === $s ? 'selected' : '' }}>{{ $s }}</option>
      @endforeach
    </select>

    <select name="tipe" class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
      <option value="">Online & Manual</option>
      <option value="Online" {{ ($filter['tipe'] ?? null) === 'Online' ? 'selected' : '' }}>Online</option>
      <option value="Manual" {{ ($filter['tipe'] ?? null) === 'Manual' ? 'selected' : '' }}>Manual</option>
    </select>

    <button type="submit" class="px-3.5 py-2.5 bg-[#0F172A] text-white rounded-xl text-xs font-semibold">
      Terapkan
    </button>
    <a href="{{ route('admin.reservasi.index') }}" class="px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-500">
      Reset
    </a>
  </form>

  {{-- ============ TABEL ============ --}}
  <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#F8FAFC] text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-[#E2E8F0]">
            <th class="p-4 pl-6">Pelanggan / Tamu</th>
            <th class="p-4">Lapangan</th>
            <th class="p-4">Jadwal</th>
            <th class="p-4">Status Reservasi</th>
            <th class="p-4">Pembayaran</th>
            <th class="p-4">Tipe</th>
            <th class="p-4">Total</th>
            <th class="p-4 pr-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#E2E8F0] text-xs font-medium">
          @forelse ($reservasi as $r)
            @php
              $telepon = $r->pelanggan->no_telepon ?? $r->no_telepon_tamu ?? '-';
              $lapWarna = $r->lapangan->jenis_lapangan === 'Futsal'
                ? 'bg-emerald-50 text-[#10B981]' : 'bg-blue-50 text-blue-600';

              $statusBadge = match ($r->status_reservasi) {
                'Pending' => 'bg-amber-100 text-amber-600',
                'Dikonfirmasi' => 'bg-emerald-100 text-[#10B981]',
                'Dibatalkan' => 'bg-red-100 text-red-600',
                'Selesai' => 'bg-slate-100 text-slate-500',
                default => 'bg-slate-100 text-slate-500',
              };

              $pembayaranTerakhir = $r->pembayaran->first();
              $pembayaranBadge = match ($pembayaranTerakhir?->status_pembayaran) {
                'Verified' => ['label' => 'Lunas', 'class' => 'bg-emerald-100 text-[#10B981]'],
                'Pending' => ['label' => 'Menunggu', 'class' => 'bg-amber-100 text-amber-600'],
                'Rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-600'],
                default => ['label' => '–', 'class' => 'bg-slate-100 text-slate-400'],
              };
            @endphp
            <tr class="hover:bg-slate-50">
              <td class="p-4 pl-6">
                <p class="font-bold text-[#0F172A]">{{ $r->nama_pemesan }}</p>
                <p class="text-[10px] text-slate-400">{{ $telepon }}</p>
              </td>
              <td class="p-4">
                <span class="px-2 py-1 {{ $lapWarna }} text-[10px] font-bold rounded-md">
                    {{ $r->lapangan->nama_lapangan }}
                </span>
              </td>
              <td class="p-4 whitespace-nowrap">
                {{ $r->tanggal_main->translatedFormat('d M') }} &middot;
                {{ \Carbon\Carbon::parse($r->jam_mulai)->format('H.i') }}–{{ \Carbon\Carbon::parse($r->jam_selesai)->format('H.i') }}
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 {{ $statusBadge }} text-[10px] font-bold rounded-lg uppercase">
                  {{ $r->status_reservasi }}
                </span>
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 {{ $pembayaranBadge['class'] }} text-[10px] font-bold rounded-lg">
                  {{ $pembayaranBadge['label'] }}
                </span>
              </td>
              <td class="p-4 text-slate-500 whitespace-nowrap">
                {{ $r->tipe_input === 'Online' ? '🌐 Online' : '🧑‍💼 Manual' }}
              </td>
              <td class="p-4 font-bold text-[#0F172A] whitespace-nowrap">
                Rp{{ number_format($r->total_harga, 0, ',', '.') }}
              </td>
              <td class="p-4 pr-6 text-right whitespace-nowrap space-x-1.5">
                @if ($r->status_reservasi === 'Pending')
                  <form method="POST" action="{{ route('admin.reservasi.confirm', $r) }}" class="inline">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 bg-[#10B981] text-white rounded-lg text-[11px] font-semibold">Konfirmasi</button>
                  </form>
                  <form method="POST" action="{{ route('admin.reservasi.cancel', $r) }}" class="inline"
                      onsubmit="return confirm('Tolak reservasi ini?')">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-[11px] font-semibold">Tolak</button>
                  </form>
                @elseif ($r->status_reservasi === 'Dikonfirmasi')
                  <button type="button" onclick="openDetailModal({{ $r->id }})"
                  class="px-3 py-1.5 bg-slate-100 text-[#0F172A] rounded-lg text-[11px] font-semibold">Detail</button>
                  <form method="POST" action="{{ route('admin.reservasi.cancel', $r) }}" class="inline"
                      onsubmit="return confirm('Batalkan reservasi ini?')">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 bg-slate-100 text-slate-500 rounded-lg text-[11px] font-semibold">Batalkan</button>
                  </form>
                @else
                  <button type="button" onclick="openDetailModal({{ $r->id }})"
                  class="px-3 py-1.5 bg-slate-100 text-[#0F172A] rounded-lg text-[11px] font-semibold">Detail</button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-10 text-center text-slate-400 text-sm">
                Tidak ada reservasi yang cocok dengan filter ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($reservasi->hasPages())
        <div class="px-6 py-4 border-t border-[#E2E8F0]">
            {{ $reservasi->links() }}
        </div>
    @endif
  </div>

  @include('admin.reservasi._modal-booking-manual')
  @include('admin.reservasi._modal-detail')

@endsection