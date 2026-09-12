<div id="modalBookingBaru" class="fixed inset-0 bg-[#0F172A]/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-2xl w-full my-8 shadow-2xl border border-[#E2E8F0] overflow-hidden flex flex-col max-h-[90vh]">

        <div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between sticky top-0 bg-white z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#10B981]/10 text-[#10B981] rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-[#0F172A]">Pesan Lapangan Baru</h3>
                    <p class="text-xs text-slate-500">Pilih jadwal & kustomisasi pesanan Anda</p>
                </div>
            </div>
            <button onclick="closeModal('modalBookingBaru')" class="w-9 h-9 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl flex items-center justify-center transition">✕</button>
        </div>

        <form id="formBookingBaru" method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data" class="flex flex-col overflow-hidden">
            @csrf
            <div id="bbInputSlots"></div>

            <div class="p-6 space-y-6 overflow-y-auto">

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl p-3 space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- 1. Jenis Olahraga --}}
                <div>
                    <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2.5">1. Pilih Jenis Olahraga</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="bb-jenis-option relative flex items-center justify-between p-4 bg-[#F8FAFC] border-2 border-[#10B981] rounded-2xl cursor-pointer transition">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">⚽</span>
                                <div>
                                    <p class="text-sm font-bold text-[#0F172A]">Futsal</p>
                                    <p class="text-[11px] text-slate-500">Sintetis & Interlock</p>
                                </div>
                            </div>
                            <input type="radio" name="jenis_ui" value="Futsal" checked class="w-4 h-4 text-[#10B981] focus:ring-[#10B981]" onchange="bbFilterLapangan('Futsal')">
                        </label>

                        <label class="bb-jenis-option relative flex items-center justify-between p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl cursor-pointer hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">🏸</span>
                                <div>
                                    <p class="text-sm font-bold text-[#0F172A]">Badminton</p>
                                    <p class="text-[11px] text-slate-500">Karpet Vinyl BWF</p>
                                </div>
                            </div>
                            <input type="radio" name="jenis_ui" value="Badminton" class="w-4 h-4 text-[#10B981] focus:ring-[#10B981]" onchange="bbFilterLapangan('Badminton')">
                        </label>
                    </div>
                </div>

                {{-- 2 & 3. Lapangan & Tanggal --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2">2. Pilih Lapangan</label>
                        <select id="bbLapangan" onchange="bbMuatSlot()"
                                class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#10B981]">
                            @foreach ($lapanganList as $l)
                                <option value="{{ $l->id }}" data-jenis="{{ $l->jenis_lapangan }}" data-harga="{{ $l->harga_per_jam }}">
                                    {{ $l->nama_lapangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2">3. Tanggal Main</label>
                        <input type="date" id="bbTanggal" onchange="bbMuatSlot()" min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}"
                               class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#10B981]">
                    </div>
                </div>

                {{-- 4. Slot Picker --}}
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider">4. Pilih Slot Waktu (harus berurutan)</label>
                        <div class="flex items-center gap-3 text-[10px] font-semibold">
                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-white border border-slate-300 rounded"></span> Tersedia</span>
                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-[#0F172A] rounded"></span> Dipilih</span>
                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-slate-200 rounded"></span> Terisi</span>
                        </div>
                    </div>
                    <div id="bbSlotGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-2.5 text-xs">
                        <p class="col-span-full text-slate-400 text-xs py-4 text-center">Memuat slot...</p>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-[#E2E8F0] space-y-2">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span id="bbRingkasSewa">Sewa Lapangan</span>
                        <span id="bbRingkasHarga" class="font-bold text-[#0F172A]">Rp0</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-[#0F172A]">Total Pembayaran</span>
                        <span id="bbTotalHarga" class="text-base font-extrabold text-[#0F172A]">Rp0</span>
                    </div>
                </div>

                {{-- 5. Metode Pembayaran --}}
                <div>
                    <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2.5">5. Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenis Bayar</label>
                            <select name="jenis_pembayaran" id="bbJenisBayar" onchange="bbUpdateJumlahBayar()"
                                    class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A]">
                                <option value="Full">Bayar Penuh</option>
                                <option value="DP">DP (50%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Metode</label>
                            <select name="metode" id="bbMetode" onchange="bbToggleBukti()"
                                    class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A]">
                                <option value="Transfer">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Cash">Bayar di Tempat (Cash)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Jumlah yang Dibayarkan (Rp)</label>
                        <input type="number" name="jumlah_bayar" id="bbJumlahBayar" min="0" step="1"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A]">
                    </div>

                    <div id="bbBuktiWrap">
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                            Bukti Transfer <span class="font-normal text-slate-400">(opsional, bisa diunggah menyusul)</span>
                        </label>
                        <input type="file" name="bukti_pembayaran" accept="image/*"
                               class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-600 file:text-xs">
                    </div>

                    <p class="text-[10.5px] text-slate-400 mt-2">
                        Pembayaran akan diverifikasi admin sebelum reservasi dianggap final.
                    </p>
                </div>
            </div>

            <div class="p-6 border-t border-[#E2E8F0] flex items-center justify-end gap-3 bg-white">
                <button type="button" onclick="closeModal('modalBookingBaru')" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-3 bg-[#10B981] hover:bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-lg shadow-[#10B981]/30 transition active:scale-[0.99]">
                    Buat Reservasi →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let bbSlotDipilih = new Set();
    let bbTotalSaatIni = 0;

    function bbToggleBukti() {
        const metode = document.getElementById('bbMetode').value;
        document.getElementById('bbBuktiWrap').classList.toggle('hidden', metode === 'Cash');
    }

    function bbUpdateJumlahBayar() {
        const jenis = document.getElementById('bbJenisBayar').value;
        const jumlah = jenis === 'DP' ? Math.round(bbTotalSaatIni / 2) : bbTotalSaatIni;
        document.getElementById('bbJumlahBayar').value = jumlah;
    }

    function bbFilterLapangan(jenis) {
        const select = document.getElementById('bbLapangan');
        let firstMatch = null;
        [...select.options].forEach(opt => {
            const cocok = opt.dataset.jenis === jenis;
            opt.hidden = !cocok;
            if (cocok && !firstMatch) firstMatch = opt.value;
        });
        if (firstMatch) select.value = firstMatch;
        bbMuatSlot();
    }

    function bbMuatSlot() {
        bbSlotDipilih.clear();
        const lapanganId = document.getElementById('bbLapangan').value;
        const tanggal = document.getElementById('bbTanggal').value;
        const grid = document.getElementById('bbSlotGrid');
        grid.innerHTML = '<p class="col-span-full text-slate-400 text-xs py-4 text-center">Memuat slot...</p>';

        fetch(`{{ route('booking.slot-tersedia') }}?lapangan_id=${lapanganId}&tanggal=${tanggal}`)
            .then(res => res.json())
            .then(data => {
                grid.innerHTML = '';
                grid.dataset.harga = data.harga_per_jam;

                data.slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = slot.label;
                    btn.dataset.jam = slot.jam;

                    if (!slot.tersedia) {
                        btn.disabled = true;
                        btn.className = 'py-2.5 px-2 bg-slate-100 text-slate-400 font-semibold rounded-xl border border-slate-200 cursor-not-allowed line-through';
                    } else {
                        btn.className = 'py-2.5 px-2 bg-white hover:border-[#10B981] text-[#0F172A] font-semibold rounded-xl border border-[#E2E8F0] transition';
                        btn.onclick = () => bbToggleSlot(btn, slot.jam);
                    }
                    grid.appendChild(btn);
                });
                bbUpdateRingkasan();
            })
            .catch(() => {
                grid.innerHTML = '<p class="col-span-full text-red-500 text-xs py-4 text-center">Gagal memuat slot. Coba lagi.</p>';
            });
    }

    function bbToggleSlot(btn, jam) {
        if (bbSlotDipilih.has(jam)) {
            bbSlotDipilih.delete(jam);
            btn.className = 'py-2.5 px-2 bg-white hover:border-[#10B981] text-[#0F172A] font-semibold rounded-xl border border-[#E2E8F0] transition';
        } else {
            bbSlotDipilih.add(jam);
            btn.className = 'py-2.5 px-2 bg-[#0F172A] text-white font-bold rounded-xl border border-[#0F172A] shadow-md';
        }
        bbUpdateRingkasan();
    }

    function bbUpdateRingkasan() {
        const harga = parseFloat(document.getElementById('bbSlotGrid').dataset.harga || 0);
        const jumlahJam = bbSlotDipilih.size;
        const total = harga * jumlahJam;
        bbTotalSaatIni = total;

        document.getElementById('bbRingkasSewa').textContent = `Sewa Lapangan (${jumlahJam} Jam x Rp${harga.toLocaleString('id-ID')})`;
        document.getElementById('bbRingkasHarga').textContent = `Rp${total.toLocaleString('id-ID')}`;
        document.getElementById('bbTotalHarga').textContent = `Rp${total.toLocaleString('id-ID')}`;
        bbUpdateJumlahBayar();
    }

    document.getElementById('formBookingBaru').addEventListener('submit', function (e) {
        if (bbSlotDipilih.size === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 slot jam terlebih dahulu.');
            return;
        }

        const wrap = document.getElementById('bbInputSlots');
        wrap.innerHTML = '';

        const inputLapangan = document.createElement('input');
        inputLapangan.type = 'hidden';
        inputLapangan.name = 'lapangan_id';
        inputLapangan.value = document.getElementById('bbLapangan').value;
        wrap.appendChild(inputLapangan);

        const inputTanggal = document.createElement('input');
        inputTanggal.type = 'hidden';
        inputTanggal.name = 'tanggal_main';
        inputTanggal.value = document.getElementById('bbTanggal').value;
        wrap.appendChild(inputTanggal);

        [...bbSlotDipilih].forEach(jam => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jam_list[]';
            input.value = jam;
            wrap.appendChild(input);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        bbFilterLapangan('Futsal');
        bbToggleBukti();
        @if ($errors->any())
            openModal('modalBookingBaru');
        @endif
    });
</script>