@php
    $statusBadge = match ($reservasi->status_reservasi) {
        'Pending' => 'bg-amber-100 text-amber-700',
        'Dikonfirmasi' => 'bg-emerald-100 text-[#10B981]',
        'Dibatalkan' => 'bg-red-100 text-red-600',
        'Selesai' => 'bg-slate-100 text-slate-500',
        default => 'bg-slate-100 text-slate-500',
    };

    $jenisSingkat = ['DP' => 'DP', 'Pelunasan' => 'PL', 'Full' => 'FL'];
@endphp

{{-- Header --}}
<div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between bg-white sticky top-0 z-10">
    <div class="flex items-center gap-3">
        <span class="font-extrabold text-lg text-[#0F172A]">Detail Transaksi</span>
        <span class="px-2.5 py-1 {{ $statusBadge }} text-[10px] font-bold rounded-lg uppercase">{{ $reservasi->status_reservasi }}</span>
    </div>
    <button type="button" onclick="closeDetailTransaksi()"
            class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-full flex items-center justify-center shrink-0">✕</button>
</div>

<div class="p-6 space-y-6 overflow-y-auto">

    {{-- Info ringkas --}}
    <div class="grid grid-cols-2 gap-4 p-4 bg-[#F8FAFC] rounded-2xl border border-[#E2E8F0] text-xs">
        <div>
            <p class="text-slate-400 font-medium">ID Booking</p>
            <p class="font-bold text-[#0F172A] mt-0.5">#SP-{{ str_pad($reservasi->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
            <p class="text-slate-400 font-medium">Lapangan</p>
            <p class="font-bold text-[#0F172A] mt-0.5">{{ $reservasi->lapangan->nama_lapangan }}</p>
        </div>
        <div>
            <p class="text-slate-400 font-medium">Jadwal</p>
            <p class="font-bold text-[#10B981] mt-0.5">
                {{ $reservasi->tanggal_main->translatedFormat('d M Y') }},
                {{ \Carbon\Carbon::parse($reservasi->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasi->jam_selesai)->format('H:i') }}
            </p>
        </div>
        <div>
            <p class="text-slate-400 font-medium">Total Tagihan</p>
            <p class="font-bold text-[#0F172A] mt-0.5">Rp{{ number_format($reservasi->total_harga, 0, ',', '.') }}</p>
            @if ($reservasi->sisa_tagihan > 0)
                <p class="text-[10px] text-amber-600 font-bold">Sisa: Rp{{ number_format($reservasi->sisa_tagihan, 0, ',', '.') }}</p>
            @else
                <p class="text-[10px] text-[#10B981] font-bold">Lunas</p>
            @endif
        </div>
    </div>

    {{-- Riwayat Pembayaran --}}
    <div>
        <h4 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2.5">Riwayat Pembayaran</h4>

        @if ($reservasi->pembayaran->isEmpty())
            <div class="border border-dashed border-[#E2E8F0] rounded-2xl p-6 text-center text-xs text-slate-400 space-y-2">
                <p>Belum ada pembayaran tercatat untuk reservasi ini.</p>
                <p class="text-[11px]">Silakan lakukan pembayaran di kasir SM Sport Center atau hubungi admin untuk instruksi transfer.</p>
            </div>
        @else
            <div class="border border-[#E2E8F0] rounded-2xl overflow-hidden divide-y divide-[#E2E8F0]">
                @foreach ($reservasi->pembayaran as $p)
                    @php
                        $pStatusBadge = match ($p->status_pembayaran) {
                            'Verified' => 'bg-emerald-50 text-[#10B981]',
                            'Pending' => 'bg-amber-50 text-amber-600',
                            'Rejected' => 'bg-red-50 text-red-600',
                            default => 'bg-slate-50 text-slate-500',
                        };
                    @endphp
                    <div class="p-3.5 bg-white flex items-center justify-between text-xs flex-wrap gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-50 text-[#10B981] rounded-lg flex items-center justify-center font-bold text-xs shrink-0">
                                {{ $jenisSingkat[$p->jenis_pembayaran] ?? '—' }}
                            </div>
                            <div>
                                <p class="font-bold text-[#0F172A]">{{ $p->metode }} ({{ $p->jenis_pembayaran }})</p>
                                <p class="text-[10px] text-slate-400">
                                    {{ $p->tanggal_bayar ? $p->tanggal_bayar->translatedFormat('d M Y, H:i') . ' WIB' : 'Menunggu pembayaran' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 {{ $pStatusBadge }} text-[9.5px] font-bold rounded-md uppercase">{{ $p->status_pembayaran }}</span>
                            <span class="font-bold text-[#0F172A]">Rp{{ number_format($p->jumlah_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($reservasi->status_reservasi === 'Dibatalkan' && $reservasi->alasan_pembatalan)
        <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 text-xs text-red-700">
            <span class="font-bold">Alasan pembatalan:</span> {{ $reservasi->alasan_pembatalan }}
        </div>
    @endif
</div>

{{-- Aksi: pelanggan hanya bisa membatalkan, tidak ada aksi admin di sini --}}
@if (in_array($reservasi->status_reservasi, ['Pending', 'Dikonfirmasi']))
    <div class="p-4 border-t border-[#E2E8F0] bg-[#F8FAFC] flex justify-end">
        <form method="POST" action="{{ route('reservasi-saya.cancel', $reservasi) }}"
              onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-xs">
                Batalkan Reservasi
            </button>
        </form>
    </div>
@endif