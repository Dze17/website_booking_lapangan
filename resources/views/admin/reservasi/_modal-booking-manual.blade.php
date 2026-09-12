{{-- ====== MODAL: Booking Manual (Walk-in) ==================== --}}
<div id="modalBookingManual" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeBookingModal()"></div>

  <div class="absolute inset-0 flex items-start sm:items-center justify-center p-4 overflow-y-auto">
    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-xl my-8">

      <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0]">
        <div>
          <h3 class="text-base font-extrabold text-[#0F172A]">Booking Manual (Walk-in)</h3>
          <p class="text-[11px] text-slate-400">Untuk pelanggan yang datang langsung / pesan lewat telepon</p>
        </div>
        <button type="button" onclick="closeBookingModal()" class="text-slate-400 hover:text-slate-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form method="POST" action="{{ route('admin.reservasi.store') }}" class="px-6 py-5 space-y-4">
        @csrf

        {{-- Toggle Pelanggan Terdaftar / Tamu Baru --}}
        <div class="flex bg-[#F8FAFC] p-1 rounded-xl text-xs font-semibold">
          <label class="flex-1">
            <input type="radio" name="mode" value="terdaftar" class="peer hidden" onchange="toggleMode('terdaftar')" checked>
            <span class="block text-center py-2 rounded-lg cursor-pointer peer-checked:bg-white peer-checked:shadow-sm peer-checked:text-[#0F172A] text-slate-500"
              id="labelModeTerdaftar">Pelanggan Terdaftar</span>
          </label>
          <label class="flex-1">
            <input type="radio" name="mode" value="tamu" class="peer hidden" onchange="toggleMode('tamu')">
            <span class="block text-center py-2 rounded-lg cursor-pointer peer-checked:bg-white peer-checked:shadow-sm peer-checked:text-[#0F172A] text-slate-500"
              id="labelModeTamu">Tamu Baru</span>
          </label>
        </div>

        {{-- Field: Pelanggan terdaftar --}}
        <div id="fieldTerdaftar">
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Pelanggan</label>
          <select name="user_id" class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            <option value="">— Pilih pelanggan —</option>
            @foreach ($pelangganList as $p)
              <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->no_telepon ?? 'tanpa telepon' }}</option>
            @endforeach
          </select>
          @error('user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Field: Tamu baru --}}
        <div id="fieldTamu" class="hidden space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Tamu</label>
            <input type="text" name="nama_tamu" placeholder="Nama lengkap"
              class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('nama_tamu') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">No. Telepon Tamu</label>
            <input type="text" name="no_telepon_tamu" placeholder="08xxxxxxxxxx"
              class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('no_telepon_tamu') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Lapangan --}}
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Lapangan</label>
          <select name="lapangan_id" id="selectLapangan" onchange="hitungTotal()"
              class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            <option value="">— Pilih lapangan —</option>
            @foreach ($lapanganList as $l)
              <option value="{{ $l->id }}" data-harga="{{ $l->harga_per_jam }}">
                {{ $l->nama_lapangan }} ({{ $l->jenis_lapangan }}) — Rp{{ number_format($l->harga_per_jam, 0, ',', '.') }}/jam
              </option>
            @endforeach
          </select>
          @error('lapangan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Tanggal & Jam --}}
        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal</label>
            <input type="date" name="tanggal_main" value="{{ now()->toDateString() }}"
              class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('tanggal_main') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
            <input type="time" name="jam_mulai" id="jamMulai" onchange="hitungTotal()"
              class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('jam_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
            <input type="time" name="jam_selesai" id="jamSelesai" onchange="hitungTotal()"
              class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            @error('jam_selesai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Total & Status --}}
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Total Harga (Rp)</label>
            <input type="number" name="total_harga" id="totalHarga" min="0" step="1000"
              class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            <p class="mt-1 text-[10.5px] text-slate-400">Terisi otomatis, bisa diubah manual.</p>
            @error('total_harga') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Reservasi</label>
            <select name="status_reservasi" class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
              <option value="Dikonfirmasi">Dikonfirmasi</option>
              <option value="Pending">Pending</option>
            </select>
          </div>
        </div>

        {{-- Catat pembayaran sekaligus --}}
        <div class="border border-[#E2E8F0] rounded-xl p-4">
          <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
            <input type="checkbox" name="catat_pembayaran" value="1" id="catatPembayaran" onchange="togglePembayaran()">
            Catat pembayaran sekaligus
          </label>

          <div id="fieldPembayaran" class="hidden grid grid-cols-2 gap-3 mt-3">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1.5">Metode</label>
              <select name="metode" class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
                <option value="Cash">Cash</option>
                <option value="Transfer">Transfer</option>
                <option value="E-Wallet">E-Wallet</option>
                <option value="Payment Gateway">Payment Gateway</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah Bayar (Rp)</label>
              <input type="number" name="jumlah_bayar" min="0" step="1000"
                class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Pembayaran</label>
              <select name="status_pembayaran" class="w-full px-3 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-sm">
                <option value="Verified">Verified (Lunas)</option>
                <option value="Pending">Pending (Belum diverifikasi)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" onclick="closeBookingModal()"
            class="flex-1 py-3 rounded-xl border border-[#E2E8F0] text-slate-600 text-sm font-semibold">
            Batal
          </button>
          <button type="submit"
              class="flex-1 py-3 rounded-xl bg-[#10B981] hover:bg-emerald-600 text-white text-sm font-semibold">
            Simpan Reservasi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    function openBookingModal() {
        document.getElementById('modalBookingManual').classList.remove('hidden');
    }
    function closeBookingModal() {
        document.getElementById('modalBookingManual').classList.add('hidden');
    }
    function toggleMode(mode) {
        document.getElementById('fieldTerdaftar').classList.toggle('hidden', mode !== 'terdaftar');
        document.getElementById('fieldTamu').classList.toggle('hidden', mode !== 'tamu');
    }
    function togglePembayaran() {
        document.getElementById('fieldPembayaran').classList.toggle('hidden', !document.getElementById('catatPembayaran').checked);
    }
    function hitungTotal() {
        const lapanganSelect = document.getElementById('selectLapangan');
        const harga = parseFloat(lapanganSelect.options[lapanganSelect.selectedIndex]?.dataset.harga || 0);
        const jamMulai = document.getElementById('jamMulai').value;
        const jamSelesai = document.getElementById('jamSelesai').value;

        if (!harga || !jamMulai || !jamSelesai) return;

        const [h1, m1] = jamMulai.split(':').map(Number);
        const [h2, m2] = jamSelesai.split(':').map(Number);
        const durasiJam = (h2 * 60 + m2 - (h1 * 60 + m1)) / 60;

        if (durasiJam > 0) {
            document.getElementById('totalHarga').value = Math.round(harga * durasiJam);
        }
    }

    {{-- Buka modal otomatis kalau validasi gagal (supaya isian sebelumnya tidak hilang begitu saja) --}}
    @if ($errors->any() && old('lapangan_id'))
        document.addEventListener('DOMContentLoaded', openBookingModal);
    @endif
</script>