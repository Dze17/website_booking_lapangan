{{-- Header --}}
<div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between bg-white sticky top-0 z-10">
    <div>
        <span class="font-extrabold text-lg text-[#0F172A]">Edit Jadwal — #SP-{{ str_pad($reservasi->id, 5, '0', STR_PAD_LEFT) }}</span>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $reservasi->nama_pemesan }}</p>
    </div>
    <button type="button" onclick="closeDetailModal()"
            class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-full flex items-center justify-center shrink-0">✕</button>
</div>

<form method="POST" action="{{ route('admin.reservasi.update', $reservasi) }}" class="p-6 space-y-4 overflow-y-auto">
    @csrf
    @method('PUT')

    <div class="bg-amber-50 border border-amber-200 text-amber-700 text-[11px] rounded-xl px-3.5 py-2.5">
        ⚠️ Mengubah lapangan/jam akan dicek ulang supaya tidak bentrok dengan reservasi lain.
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Lapangan</label>
        <select name="lapangan_id" id="editSelectLapangan" onchange="hitungTotalEdit()"
                class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @foreach ($lapanganList as $l)
                <option value="{{ $l->id }}" data-harga="{{ $l->harga_per_jam }}"
                    {{ old('lapangan_id', $reservasi->lapangan_id) == $l->id ? 'selected' : '' }}>
                    {{ $l->nama_lapangan }} ({{ $l->jenis_lapangan }}) — Rp{{ number_format($l->harga_per_jam, 0, ',', '.') }}/jam
                </option>
            @endforeach
        </select>
        @error('lapangan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal</label>
            <input type="date" name="tanggal_main" value="{{ old('tanggal_main', $reservasi->tanggal_main->toDateString()) }}"
                   class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('tanggal_main') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
            <input type="time" name="jam_mulai" id="editJamMulai" onchange="hitungTotalEdit()"
                   value="{{ old('jam_mulai', \Carbon\Carbon::parse($reservasi->jam_mulai)->format('H:i')) }}"
                   class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('jam_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
            <input type="time" name="jam_selesai" id="editJamSelesai" onchange="hitungTotalEdit()"
                   value="{{ old('jam_selesai', \Carbon\Carbon::parse($reservasi->jam_selesai)->format('H:i')) }}"
                   class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('jam_selesai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Total Harga (Rp)</label>
        <input type="number" name="total_harga" id="editTotalHarga" min="0" step="1000"
               value="{{ old('total_harga', $reservasi->total_harga) }}"
               class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
        <p class="mt-1 text-[10.5px] text-slate-400">Otomatis dihitung ulang jika lapangan/jam diubah, tapi tetap bisa diedit manual.</p>
        @error('total_harga') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-2 text-xs font-medium text-slate-600 cursor-pointer">
        <input type="checkbox" name="beri_tahu_pelanggan" value="1" checked>
        Beri tahu pelanggan lewat notifikasi jika jadwal berubah
    </label>

    <div class="flex gap-3 pt-2">
        <button type="button" onclick="openDetailModal({{ $reservasi->id }})"
                class="flex-1 py-3 rounded-xl border border-[#E2E8F0] text-slate-600 text-sm font-semibold">
            ← Kembali ke Detail
        </button>
        <button type="submit"
                class="flex-1 py-3 rounded-xl bg-[#10B981] hover:bg-emerald-600 text-white text-sm font-semibold">
            Simpan Perubahan
        </button>
    </div>
</form>

<script>
    function hitungTotalEdit() {
        const sel = document.getElementById('editSelectLapangan');
        const harga = parseFloat(sel.options[sel.selectedIndex]?.dataset.harga || 0);
        const jamMulai = document.getElementById('editJamMulai').value;
        const jamSelesai = document.getElementById('editJamSelesai').value;

        if (!harga || !jamMulai || !jamSelesai) return;

        const [h1, m1] = jamMulai.split(':').map(Number);
        const [h2, m2] = jamSelesai.split(':').map(Number);
        const durasiJam = (h2 * 60 + m2 - (h1 * 60 + m1)) / 60;

        if (durasiJam > 0) {
            document.getElementById('editTotalHarga').value = Math.round(harga * durasiJam);
        }
    }
</script>