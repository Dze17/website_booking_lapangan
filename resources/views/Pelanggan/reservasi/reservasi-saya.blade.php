@extends('layouts.pelanggan')

@section('title', 'Pesanan Saya - SM Sport Center')

@section('content')

    <div>
        <h1 class="text-xl font-bold text-[#0F172A]">Pesanan Saya</h1>
        <p class="text-xs text-slate-500 mt-0.5">Riwayat dan status seluruh reservasi lapanganmu</p>
    </div>

    {{-- ============ FILTER CHIPS ============ --}}
    @php
        $chips = [
            'semua' => 'Semua',
            'akan_datang' => 'Akan Datang (' . $ringkasan['akan_datang'] . ')',
            'Selesai' => 'Selesai (' . $ringkasan['selesai'] . ')',
            'Dibatalkan' => 'Dibatalkan (' . $ringkasan['dibatalkan'] . ')',
        ];
    @endphp
    <div class="flex flex-wrap gap-2">
        @foreach ($chips as $value => $label)
            <a href="{{ route('reservasi-saya.index', $value === 'semua' ? [] : ['status' => $value]) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition
                      {{ $filterStatus === $value ? 'bg-[#0F172A] text-white' : 'bg-white border border-[#E2E8F0] text-slate-600 hover:border-slate-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ============ DAFTAR PESANAN (TICKET CARDS) ============ --}}
    <div class="space-y-4">
        @forelse ($reservasi as $r)
            @php
                $statusMap = [
                    'Pending' => ['label' => 'Menunggu Konfirmasi', 'badge' => 'bg-amber-100 text-amber-700', 'bar' => 'bg-amber-400'],
                    'Dikonfirmasi' => ['label' => 'Dikonfirmasi', 'badge' => 'bg-emerald-100 text-[#10B981]', 'bar' => 'bg-[#10B981]'],
                    'Selesai' => ['label' => 'Selesai', 'badge' => 'bg-slate-100 text-slate-500', 'bar' => 'bg-slate-300'],
                    'Dibatalkan' => ['label' => 'Dibatalkan', 'badge' => 'bg-red-100 text-red-600', 'bar' => 'bg-red-400'],
                ];
                $s = $statusMap[$r->status_reservasi] ?? $statusMap['Pending'];
            @endphp
            <div class="bg-white rounded-2xl border border-[#E2E8F0] p-5 sm:p-6 shadow-sm relative overflow-hidden flex flex-col sm:flex-row justify-between gap-4 sm:gap-6">
                <div class="absolute left-0 top-0 bottom-0 w-2 {{ $s['bar'] }}"></div>

                <div class="space-y-3 flex-1 pl-2">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="px-2.5 py-1 {{ $s['badge'] }} text-[11px] font-bold rounded-lg uppercase">{{ $s['label'] }}</span>
                        <span class="text-xs font-semibold text-slate-400">#SP-{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-[#0F172A]">{{ $r->lapangan->nama_lapangan }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">SM Sport Center &middot; {{ $r->lapangan->lokasi ?? 'Indoor' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-2.5 border-y border-dashed border-slate-200 text-xs">
                        <div>
                            <p class="text-slate-400 font-medium">Tanggal</p>
                            <p class="font-bold text-[#0F172A] mt-0.5">{{ $r->tanggal_main->translatedFormat('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Waktu</p>
                            <p class="font-bold text-[#10B981] mt-0.5">
                                {{ \Carbon\Carbon::parse($r->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($r->jam_selesai)->format('H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#F8FAFC] border border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center shrink-0 w-full sm:w-44 text-center gap-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                    <p class="text-sm font-extrabold text-[#0F172A]">Rp{{ number_format($r->total_harga, 0, ',', '.') }}</p>
                    <button type="button" onclick="openDetailTransaksi({{ $r->id }})"
                            class="w-full py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-lg text-[11px] font-bold transition">
                        Lihat Detail Transaksi
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-[#E2E8F0] p-10 text-center">
                <p class="text-sm text-slate-500">Belum ada pesanan pada kategori ini.</p>
                <a href="{{ route('dashboard') }}" class="mt-3 inline-block text-xs font-bold text-[#10B981] hover:underline">
                    ← Kembali ke Dashboard
                </a>
            </div>
        @endforelse
    </div>

    @if ($reservasi->hasPages())
        <div>{{ $reservasi->links() }}</div>
    @endif

@endsection

@section('modals')
    @include('Pelanggan.dashboard._modal-detail-transaksi')
@endsection