@php
    $telepon = $reservasi->pelanggan->no_telepon ?? $reservasi->no_telepon_tamu ?? '-';

    $statusBadge = match ($reservasi->status_reservasi) {
        'Pending' => 'bg-amber-100 text-amber-700',
        'Dikonfirmasi' => 'bg-emerald-100 text-[#10B981]',
        'Dibatalkan' => 'bg-red-100 text-red-600',
        'Selesai' => 'bg-slate-100 text-slate-500',
        default => 'bg-slate-100 text-slate-500',
    };

    $dibuatOlehLabel = $reservasi->tipe_input === 'Online'
        ? 'Sistem Online (Web)'
        : ($reservasi->dibuatOleh->name ?? 'Admin');
    $dibuatOlehSub = $reservasi->tipe_input === 'Manual' ? 'Input Manual · ' : '';

    $jenisSingkat = ['DP' => 'DP', 'Pelunasan' => 'PL', 'Full' => 'FL'];
@endphp

{{-- Header --}}
<div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between bg-white sticky top-0 z-10">
    <div class="flex items-center gap-3">
        <span class="font-extrabold text-lg text-[#0F172A]">Detail Reservasi #SP-{{ str_pad($reservasi->id, 5, '0', STR_PAD_LEFT) }}</span>
        <span class="px-2.5 py-1 {{ $statusBadge }} text-[10px] font-bold rounded-lg uppercase">{{ $reservasi->status_reservasi }}</span>
    </div>
    <button type="button" onclick="closeDetailModal()"
            class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-full flex items-center justify-center shrink-0">✕</button>
</div>

<div class="p-6 space-y-6 overflow-y-auto">

    {{-- Info Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-[#F8FAFC] rounded-2xl border border-[#E2E8F0] text-xs">
        <div>
            <p class="text-slate-400 font-medium">Pelanggan</p>
            <p class="font-bold text-[#0F172A] mt-0.5">{{ $reservasi->nama_pemesan }}</p>
            <p class="text-[10px] text-slate-500">{{ $telepon }}</p>
        </div>
        <div>
            <p class="text-slate-400 font-medium">Lapangan & Waktu</p>
            <p class="font-bold text-[#0F172A] mt-0.5">{{ $reservasi->lapangan->nama_lapangan }}</p>
            <p class="text-[10px] text-[#10B981] font-bold">
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
        <div>
            <p class="text-slate-400 font-medium">Dibuat Oleh</p>
            <p class="font-bold text-[#0F172A] mt-0.5">{{ $dibuatOlehLabel }}</p>
            <p class="text-[10px] text-slate-400">{{ $dibuatOlehSub }}{{ $reservasi->created_at->translatedFormat('d M Y H:i') }}</p>
        </div>
    </div>

    {{-- Riwayat Pembayaran --}}
    <div>
        <h4 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2.5">Riwayat Pembayaran & Bukti Transfer</h4>

        @if ($reservasi->pembayaran->isEmpty())
            <div class="border border-dashed border-[#E2E8F0] rounded-2xl p-6 text-center text-xs text-slate-400">
                Belum ada pembayaran tercatat.
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
                                    {{ $p->tanggal_bayar ? $p->tanggal_bayar->translatedFormat('d M Y, H:i') . ' WIB' : 'Belum dibayar' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 {{ $pStatusBadge }} text-[9.5px] font-bold rounded-md uppercase">{{ $p->status_pembayaran }}</span>
                            <span class="font-bold text-[#0F172A]">Rp{{ number_format($p->jumlah_bayar, 0, ',', '.') }}</span>
                            @if ($p->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank"
                                   class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-md">Lihat Bukti 📄</a>
                            @else
                                <span class="text-[10px] text-slate-300">Tanpa bukti</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Riwayat notifikasi singkat (opsional, membantu admin lihat sudah dikirim apa saja) --}}
    @if ($reservasi->notifikasi->isNotEmpty())
        <div>
            <h4 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2.5">Riwayat Notifikasi</h4>
            <ul class="space-y-1.5 text-[11px] text-slate-500">
                @foreach ($reservasi->notifikasi->sortByDesc('created_at') as $n)
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                        {{ $n->tipe }} — {{ $n->created_at->translatedFormat('d M, H:i') }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Catat Pembayaran Baru — hanya muncul kalau masih ada sisa tagihan.
         Ini yang menutup celah alur pelanggan (booking online tanpa pembayaran
         di awal), sekaligus dipakai untuk pembayaran bertahap (DP -> Pelunasan). --}}
    @if ($reservasi->sisa_tagihan > 0 && $reservasi->status_reservasi !== 'Dibatalkan')
        <div class="border border-[#E2E8F0] rounded-2xl p-4">
            <h4 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-3">
                Catat Pembayaran Baru
                <span class="font-normal normal-case text-slate-400">
                    (sisa Rp{{ number_format($reservasi->sisa_tagihan, 0, ',', '.') }})
                </span>
            </h4>
            <form method="POST" action="{{ route('admin.pembayaran.store') }}" class="grid grid-cols-2 gap-3">
                @csrf
                <input type="hidden" name="reservasi_id" value="{{ $reservasi->id }}">

                <div>
                    <label class="block text-[10.5px] font-semibold text-slate-500 mb-1">Jenis</label>
                    <select name="jenis_pembayaran" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-xs">
                        <option value="DP">DP</option>
                        <option value="Pelunasan">Pelunasan</option>
                        <option value="Full">Full</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10.5px] font-semibold text-slate-500 mb-1">Metode</label>
                    <select name="metode" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-xs">
                        <option value="Cash">Cash</option>
                        <option value="Transfer">Transfer</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Payment Gateway">Payment Gateway</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10.5px] font-semibold text-slate-500 mb-1">Jumlah (Rp)</label>
                    <input type="number" name="jumlah_bayar" min="1" step="1000"
                           value="{{ $reservasi->sisa_tagihan }}"
                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-xs">
                </div>
                <div>
                    <label class="block text-[10.5px] font-semibold text-slate-500 mb-1">Status</label>
                    <select name="status_pembayaran" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-xs">
                        <option value="Verified">Verified (Lunas)</option>
                        <option value="Pending">Pending (Belum diverifikasi)</option>
                    </select>
                </div>

                <button type="submit" class="col-span-2 py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded-lg text-xs font-bold">
                    Simpan Pembayaran
                </button>
            </form>
        </div>
    @endif

    {{-- Alasan pembatalan --}}
    @if ($reservasi->status_reservasi === 'Dibatalkan' && $reservasi->alasan_pembatalan)
        <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 text-xs text-red-700">
            <span class="font-bold">Alasan pembatalan:</span> {{ $reservasi->alasan_pembatalan }}
        </div>
    @elseif (in_array($reservasi->status_reservasi, ['Pending', 'Dikonfirmasi']))
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                Alasan Pembatalan (jika ditolak/dibatalkan)
            </label>
            <input type="text" form="formBatalkan{{ $reservasi->id }}" name="alasan"
                   placeholder="Contoh: Bukti transfer tidak valid / Lapangan dipakai latihan privat"
                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs text-[#0F172A]">
        </div>
    @endif
</div>

{{-- Action Buttons --}}
<div class="p-4 border-t border-[#E2E8F0] bg-[#F8FAFC] flex flex-wrap items-center justify-between gap-2">
    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('admin.reservasi.resend', $reservasi) }}">
            @csrf
            <button class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-xs">
                📱 Kirim Ulang Notifikasi
            </button>
        </form>
        <button type="button" onclick="openEditModal({{ $reservasi->id }})"
                class="px-3 py-2 border border-slate-300 hover:bg-white text-slate-700 font-semibold rounded-xl text-xs">
            ✏️ Edit Jadwal
        </button>
    </div>

    @if (in_array($reservasi->status_reservasi, ['Pending', 'Dikonfirmasi']))
        <div class="flex items-center gap-2">
            <form id="formBatalkan{{ $reservasi->id }}" method="POST" action="{{ route('admin.reservasi.cancel', $reservasi) }}"
                  onsubmit="return confirm('Batalkan reservasi ini?')">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-xs">
                    Batalkan Reservasi
                </button>
            </form>

            @if ($reservasi->status_reservasi === 'Pending')
                <form method="POST" action="{{ route('admin.reservasi.confirm', $reservasi) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-5 py-2 bg-[#10B981] hover:bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-md">
                        ✓ Konfirmasi Reservasi
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>