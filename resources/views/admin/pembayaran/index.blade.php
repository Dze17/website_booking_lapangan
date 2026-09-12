@extends('layouts.admin')

@section('title', 'Transaksi & Keuangan - SM Sport Center')
@section('page-title', 'Transaksi & Keuangan')

@php
  $metodeIkon = ['Cash' => '💵', 'Transfer' => '🏦', 'E-Wallet' => '📱', 'Payment Gateway' => '💳'];
@endphp

@section('content')

  @if (session('success'))
    <div class="bg-[#DCFCE7] text-[#15803D] text-sm font-medium px-4 py-3 rounded-xl">
      {{ session('success') }}
    </div>
  @endif

  {{-- ============ KARTU RINGKASAN ============ --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
      <p class="text-xs font-semibold text-slate-400">Total Pendapatan (Periode)</p>
      <p class="text-2xl font-extrabold text-[#0F172A] mt-1">Rp{{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</p>
      <p class="text-[11px] font-bold text-[#10B981] mt-2">{{ $filter['dari'] }} s/d {{ $filter['sampai'] }}</p>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
      <p class="text-xs font-semibold text-slate-400 mb-2">Breakdown Metode</p>
      @forelse ($ringkasan['breakdown_metode'] as $metode => $total)
        <div class="flex justify-between items-center text-[11px] mb-1">
          <span class="text-slate-500">{{ $metodeIkon[$metode] ?? '' }} {{ $metode }}</span>
          <span class="font-bold text-[#0F172A]">Rp{{ number_format($total, 0, ',', '.') }}</span>
        </div>
      @empty
        <p class="text-[11px] text-slate-400">Belum ada transaksi terverifikasi.</p>
      @endforelse
    </div>

    <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl">
      <p class="text-xs font-semibold text-amber-700">Menunggu Verifikasi</p>
      <p class="text-2xl font-extrabold text-amber-800 mt-1">{{ $ringkasan['menunggu_jumlah'] }} Transaksi</p>
      <p class="text-[11px] font-bold text-amber-600 mt-2">Rp{{ number_format($ringkasan['menunggu_nominal'], 0, ',', '.') }} mengambang</p>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
      <p class="text-xs font-semibold text-slate-400">Rata-rata Nilai Transaksi</p>
      <p class="text-2xl font-extrabold text-[#0F172A] mt-1">Rp{{ number_format($ringkasan['rata_rata_transaksi'], 0, ',', '.') }}</p>
      <p class="text-[11px] font-bold text-slate-400 mt-2">Transaksi terverifikasi</p>
    </div>
  </div>

  {{-- ============ FILTER TOOLBAR ============ --}}
  <form method="GET" action="{{ route('admin.pembayaran.index') }}"
      class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-wrap items-center gap-3">

    <div class="relative flex-1 min-w-[200px]">
      <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
      </svg>
      <input type="text" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Cari nama pelanggan / telepon..."
        class="w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-medium placeholder:text-slate-400 focus:outline-none">
    </div>

    <input type="date" name="dari" value="{{ $filter['dari'] }}" class="px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
    <span class="text-xs text-slate-400">s/d</span>
    <input type="date" name="sampai" value="{{ $filter['sampai'] }}" class="px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">

    <select name="metode" class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
      <option value="">Semua Metode</option>
      @foreach (['Cash', 'Transfer', 'E-Wallet', 'Payment Gateway'] as $m)
        <option value="{{ $m }}" {{ ($filter['metode'] ?? null) === $m ? 'selected' : '' }}>{{ $m }}</option>
      @endforeach
    </select>

    <select name="status" class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
      <option value="">Semua Status</option>
      @foreach (['Pending', 'Verified', 'Rejected'] as $s)
        <option value="{{ $s }}" {{ ($filter['status'] ?? null) === $s ? 'selected' : '' }}>{{ $s }}</option>
      @endforeach
    </select>

    <select name="jenis" class="px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-600">
      <option value="">Semua Jenis</option>
      @foreach (['DP', 'Pelunasan', 'Full'] as $j)
        <option value="{{ $j }}" {{ ($filter['jenis'] ?? null) === $j ? 'selected' : '' }}>{{ $j }}</option>
      @endforeach
    </select>

    <button type="submit" class="px-3.5 py-2.5 bg-[#0F172A] text-white rounded-xl text-xs font-semibold">Terapkan</button>
    <a href="{{ route('admin.pembayaran.index') }}" class="px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs font-semibold text-slate-500">Reset</a>

    <a href="{{ route('admin.pembayaran.export', request()->query()) }}"
      class="ml-auto px-3.5 py-2.5 bg-[#10B981] hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5 5-5M12 15V3" />
      </svg>
      Export CSV
    </a>
  </form>

  {{-- ============ TABEL ============ --}}
  <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#F8FAFC] text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-[#E2E8F0]">
            <th class="p-4 pl-6">ID</th>
            <th class="p-4">Reservasi Terkait</th>
            <th class="p-4">Jenis</th>
            <th class="p-4">Metode</th>
            <th class="p-4">Jumlah</th>
            <th class="p-4">Tgl Bayar</th>
            <th class="p-4">Bukti</th>
            <th class="p-4">Status</th>
            <th class="p-4">Diverifikasi</th>
            <th class="p-4 pr-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#E2E8F0] text-xs font-medium">
          @forelse ($transaksi as $t)
            @php
              $r = $t->reservasi;
              $statusBadge = match ($t->status_pembayaran) {
                'Verified' => 'bg-emerald-100 text-[#10B981]',
                'Pending' => 'bg-amber-100 text-amber-600',
                'Rejected' => 'bg-red-100 text-red-600',
                default => 'bg-slate-100 text-slate-500',
              };
            @endphp
            <tr class="hover:bg-slate-50">
              <td class="p-4 pl-6 font-bold text-[#0F172A] whitespace-nowrap">
                #SP-{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}
              </td>
              <td class="p-4">
                <p class="font-bold text-[#0F172A]">{{ $r->nama_pemesan }}</p>
                <p class="text-[10px] text-slate-400">
                  {{ $r->lapangan->nama_lapangan }} &middot; {{ $r->tanggal_main->format('d M Y') }}
                </p>
              </td>
              <td class="p-4">{{ $t->jenis_pembayaran }}</td>
              <td class="p-4 whitespace-nowrap">{{ $metodeIkon[$t->metode] ?? '' }} {{ $t->metode }}</td>
              <td class="p-4 font-bold text-[#0F172A] whitespace-nowrap">Rp{{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
              <td class="p-4 whitespace-nowrap text-slate-500">
                {{ $t->tanggal_bayar?->format('d M Y') ?? '-' }}
              </td>
              <td class="p-4">
                @if ($t->bukti_url)
                  <a href="{{ $t->bukti_url }}" target="_blank">
                    <img src="{{ $t->bukti_url }}" class="w-9 h-9 rounded-lg object-cover border border-[#E2E8F0]" alt="Bukti">
                  </a>
                @else
                  <span class="text-slate-300">–</span>
                @endif
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 {{ $statusBadge }} text-[10px] font-bold rounded-lg uppercase">{{ $t->status_pembayaran }}</span>
              </td>
              <td class="p-4 text-slate-500 whitespace-nowrap">{{ $t->verifikator->name ?? '-' }}</td>
              <td class="p-4 pr-6 text-right whitespace-nowrap">
                @if ($t->status_pembayaran === 'Pending')
                  @php
                    $verData = [
                      'id' => $t->id,
                      'nama' => $r->nama_pemesan,
                      'lapangan' => $r->lapangan->nama_lapangan,
                      'jadwal' => $r->tanggal_main->format('d M Y') . ', '
                        . \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') . '-'
                        . \Carbon\Carbon::parse($r->jam_selesai)->format('H:i'),
                      'jumlah' => 'Rp' . number_format($t->jumlah_bayar, 0, ',', '.'),
                      'metode' => $t->metode,
                      'bukti_url' => $t->bukti_url,
                      'verify_url' => route('admin.pembayaran.verify', $t),
                      'reject_url' => route('admin.pembayaran.reject', $t),
                    ];
                  @endphp
                  <button type="button"
                    class="btn-verifikasi px-3 py-1.5 bg-[#10B981] text-white rounded-lg text-[11px] font-semibold"
                    data-payload="{{ json_encode($verData) }}">
                      Verifikasi
                  </button>
                @else
                  <span class="text-slate-300 text-[11px]">Selesai</span>
                @endif
              </td>
            </tr>
          @empty
              <tr>
                <td colspan="10" class="p-10 text-center text-slate-400 text-sm">Tidak ada transaksi pada periode/filter ini.</td>
              </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($transaksi->hasPages())
      <div class="px-6 py-4 border-t border-[#E2E8F0]">
        {{ $transaksi->links() }}
      </div>
    @endif
  </div>

  @include('admin.pembayaran._modal-verifikasi')

@endsection