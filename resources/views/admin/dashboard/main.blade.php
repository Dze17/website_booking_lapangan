@extends('layouts.admin')

@section('title', 'Dashboard Admin - SM Sport Center')
@section('page-title', 'Ringkasan Operasional')

@section('header-action')
    
@endsection

@section('content')

    {{-- ============ 4 KPI STATS ============ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Pendapatan --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <p class="text-xs font-semibold text-slate-400">Pendapatan Hari Ini</p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-1">
                Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
            </p>
            @if ($deltaPendapatan === null)
                <p class="text-[11px] font-bold text-slate-400 mt-2">Belum ada data kemarin</p>
            @else
                <p class="text-[11px] font-bold {{ $deltaPendapatan >= 0 ? 'text-[#10B981]' : 'text-red-500' }} mt-2 flex items-center gap-1">
                    <span>{{ $deltaPendapatan >= 0 ? '↑' : '↓' }} {{ abs($deltaPendapatan) }}%</span>
                    <span class="font-normal text-slate-400">vs kemarin</span>
                </p>
            @endif
        </div>

        {{-- Total Pemesanan --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <p class="text-xs font-semibold text-slate-400">Total Pemesanan</p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-1">{{ $reservasiHariIni }} Slot</p>
            <p class="text-[11px] font-bold text-[#10B981] mt-2 flex items-center gap-1">
                <span>{{ $futsalHariIni }} Futsal</span> • <span class="text-blue-600">{{ $badmintonHariIni }} Badminton</span>
            </p>
        </div>

        {{-- Okupansi --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <p class="text-xs font-semibold text-slate-400">Tingkat Keterisian (Occupancy)</p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-1">{{ $okupansi }}%</p>
            <p class="text-[11px] font-bold text-amber-500 mt-2">
                @if ($jamRamai !== null)
                    Jam Ramai: {{ sprintf('%02d', $jamRamai) }}:00 - {{ sprintf('%02d', $jamRamai + 1) }}:00
                @else
                    Belum ada reservasi hari ini
                @endif
            </p>
        </div>

        {{-- Pengguna Baru --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <p class="text-xs font-semibold text-slate-400">Pengguna Baru</p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-1">+{{ $penggunaBaruHariIni }} User</p>
            <p class="text-[11px] font-bold text-[#10B981] mt-2">Hari Ini</p>
        </div>
    </div>

    {{-- ============ STATUS LAPANGAN REALTIME ============ --}}
    <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
            <div>
                <h2 class="text-base font-bold text-[#0F172A]">Status Lapangan Realtime — {{ $today->translatedFormat('d F Y') }}</h2>
                <p class="text-xs text-slate-500">Pantau jadwal terisi dan kosong secara cepat</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-[#10B981] rounded-md"></span> Terisi</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-slate-100 border border-slate-300 rounded-md"></span> Kosong</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-100 border border-red-300 rounded-md"></span> Maintenance</span>
            </div>
        </div>

        @if ($jadwal->isEmpty())
            <p class="text-sm text-slate-400 py-6 text-center">Belum ada data lapangan aktif.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 pt-2">
                @foreach ($jadwal as $slot)
                    @php
                        $lapangan = $slot['lapangan'];
                        $reservasi = $slot['reservasi'];
                        $isMaintenance = $lapangan->status === 'Maintenance';
                    @endphp

                    @if ($isMaintenance)
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs space-y-1">
                            <p class="font-bold">{{ sprintf('%02d:00', $slot['jam']) }} - {{ sprintf('%02d:00', $slot['jam'] + 1) }}</p>
                            <p class="text-[10px] text-red-500">{{ $lapangan->nama_lapangan }}</p>
                            <span class="inline-block px-1.5 py-0.5 bg-red-100 text-red-600 text-[9px] font-extrabold rounded">PERBAIKAN</span>
                        </div>
                    @elseif ($reservasi)
                        <div class="p-3 bg-[#10B981] text-white rounded-xl text-xs space-y-1">
                            <p class="font-bold">{{ sprintf('%02d:00', $slot['jam']) }} - {{ sprintf('%02d:00', $slot['jam'] + 1) }}</p>
                            <p class="text-[10px] opacity-90 truncate">{{ $lapangan->nama_lapangan }} • {{ $reservasi->nama_pemesan }}</p>
                            <span class="inline-block px-1.5 py-0.5 bg-white/20 text-[9px] font-extrabold rounded">TERISI</span>
                        </div>
                    @else
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                            <p class="font-bold text-[#0F172A]">{{ sprintf('%02d:00', $slot['jam']) }} - {{ sprintf('%02d:00', $slot['jam'] + 1) }}</p>
                            <p class="text-[10px] text-slate-400">{{ $lapangan->nama_lapangan }}</p>
                            <span class="inline-block px-1.5 py-0.5 bg-emerald-100 text-[#10B981] text-[9px] font-bold rounded">KOSONG</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ TRANSAKSI TERBARU ============ --}}
    <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-[#0F172A]">Transaksi Terbaru</h2>
                <p class="text-xs text-slate-500">Daftar pembayaran masuk dari pelanggan</p>
            </div>
            <a href="{{ Route::has('admin.pembayaran.index') ? route('admin.pembayaran.index') : '#' }}"
               class="text-xs font-semibold text-[#10B981] hover:underline">Lihat Semua Transaksi</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-[#E2E8F0]">
                        <th class="p-4 pl-6">ID Transaksi</th>
                        <th class="p-4">Pemesan</th>
                        <th class="p-4">Lapangan</th>
                        <th class="p-4">Jadwal Main</th>
                        <th class="p-4">Total Bayar</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] text-xs font-medium">
                    @forelse ($transaksiTerbaru as $t)
                        @php
                            $reservasi = $t->reservasi;
                            $telepon = $reservasi->pelanggan->no_telepon ?? $reservasi->no_telepon_tamu ?? '-';

                            $statusBadge = match ($t->status_pembayaran) {
                                'Verified' => ['label' => 'LUNAS', 'class' => 'bg-emerald-100 text-[#10B981]'],
                                'Pending' => ['label' => 'PENDING', 'class' => 'bg-amber-100 text-amber-600'],
                                'Rejected' => ['label' => 'DITOLAK', 'class' => 'bg-red-100 text-red-600'],
                                default => ['label' => $t->status_pembayaran, 'class' => 'bg-slate-100 text-slate-600'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 pl-6 font-bold text-[#0F172A]">#SP-{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-4">
                                <p class="font-bold text-[#0F172A]">{{ $reservasi->nama_pemesan }}</p>
                                <p class="text-[10px] text-slate-400">{{ $telepon }}</p>
                            </td>
                            <td class="p-4">{{ $reservasi->lapangan->nama_lapangan }}</td>
                            <td class="p-4">
                                {{ $reservasi->tanggal_main->translatedFormat('d M Y') }}
                                ({{ \Carbon\Carbon::parse($reservasi->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasi->jam_selesai)->format('H:i') }})
                            </td>
                            <td class="p-4 font-bold text-[#0F172A]">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 {{ $statusBadge['class'] }} text-[10px] font-bold rounded-lg">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </td>
                            <td class="p-4 pr-6 text-right">
                                @if ($t->status_pembayaran === 'Pending')
                                    <button class="px-3 py-1 bg-[#10B981] text-white rounded-lg text-xs font-semibold transition">
                                        Konfirmasi
                                    </button>
                                @else
                                    <button class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-[#0F172A] rounded-lg text-xs font-semibold transition">
                                        Detail
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-sm">Belum ada transaksi masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection