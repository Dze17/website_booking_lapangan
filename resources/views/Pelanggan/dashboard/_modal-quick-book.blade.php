<div id="modalQuickBook" class="fixed inset-0 bg-[#0F172A]/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-[#E2E8F0] overflow-hidden flex flex-col">

        <div class="relative h-32 bg-slate-900 p-5 flex flex-col justify-between overflow-hidden">
            <img id="qbImg" src="" class="absolute inset-0 w-full h-full object-cover opacity-50 hidden" alt="Lapangan">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/40 to-transparent"></div>

            <div class="relative z-10 flex justify-between items-center">
                <span class="px-2.5 py-0.5 bg-[#10B981] text-white text-[10px] font-extrabold rounded-md uppercase tracking-wider">Pesan Cepat</span>
                <button onclick="closeModal('modalQuickBook')" class="w-8 h-8 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center transition backdrop-blur-md">✕</button>
            </div>

            <div class="relative z-10">
                <h3 id="qbTitle" class="text-base font-bold text-white leading-snug">-</h3>
                <p id="qbPrice" class="text-xs text-slate-300">Rp0 / Jam</p>
            </div>
        </div>

        <form id="formQuickBook" method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            <input type="hidden" name="sumber" value="quick">
            <input type="hidden" name="lapangan_id" id="qbLapanganId">
            <input type="hidden" name="tanggal_main" id="qbTanggalInput">
            <input type="hidden" name="jam_list[]" id="qbJamInput">
            <input type="hidden" name="jenis_pembayaran" value="Full">
            <input type="hidden" name="jumlah_bayar" id="qbJumlahBayarInput">

            <div class="p-5 space-y-5">

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl p-3 space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2">Pilih Hari</label>
                    <div id="qbDatePills" class="grid grid-cols-3 gap-2"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2">Slot Jam Tersedia</label>
                    <div id="qbSlotGrid" class="grid grid-cols-3 gap-2 text-xs">
                        <p class="col-span-full text-slate-400 text-xs py-3 text-center">Pilih tanggal dahulu...</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2">Metode Bayar</label>
                    <select name="metode" id="qbMetode" onchange="qbToggleBukti()"
                            class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A]">
                        <option value="Transfer">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Cash">Bayar di Tempat (Cash)</option>
                    </select>
                </div>

                <div id="qbBuktiWrap">
                    <label class="block text-xs font-bold text-[#0F172A] uppercase tracking-wider mb-2">
                        Bukti Transfer <span class="font-normal normal-case text-slate-400">(opsional)</span>
                    </label>
                    <input type="file" name="bukti_pembayaran" accept="image/*"
                           class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-600 file:text-xs">
                </div>

                <div class="pt-3 border-t border-[#E2E8F0] flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase">Total Pembayaran</p>
                        <p id="qbTotal" class="text-lg font-extrabold text-[#0F172A]">Rp0</p>
                    </div>
                    <button type="submit" class="py-3 px-5 bg-[#0F172A] hover:bg-slate-800 text-white font-bold rounded-xl text-xs shadow-lg shadow-[#0F172A]/20 transition active:scale-[0.99]">
                        Buat Reservasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let qbHarga = 0;

    function openQuickBookModal(lapanganId, nama, harga, gambarUrl) {
        document.getElementById('qbLapanganId').value = lapanganId;
        document.getElementById('qbTitle').textContent = nama;
        document.getElementById('qbPrice').textContent = `Rp${Number(harga).toLocaleString('id-ID')} / Jam`;
        qbHarga = Number(harga);

        const img = document.getElementById('qbImg');
        if (gambarUrl) { img.src = gambarUrl; img.classList.remove('hidden'); } else { img.classList.add('hidden'); }

        qbBuatDatePills();
        qbToggleBukti();
        openModal('modalQuickBook');
    }

    function qbBuatDatePills() {
        const wrap = document.getElementById('qbDatePills');
        wrap.innerHTML = '';
        const labelHari = ['Hari Ini', 'Besok', 'Lusa'];

        for (let i = 0; i < 3; i++) {
            const tgl = new Date();
            tgl.setDate(tgl.getDate() + i);
            const iso = tgl.toISOString().split('T')[0];
            const tampil = tgl.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.tanggal = iso;
            btn.className = i === 0
                ? 'py-2 px-3 bg-[#0F172A] text-white rounded-xl text-xs font-bold text-center shadow-md qb-date-pill'
                : 'py-2 px-3 bg-[#F8FAFC] border border-[#E2E8F0] hover:border-slate-300 text-[#0F172A] rounded-xl text-xs font-bold text-center qb-date-pill';
            btn.innerHTML = `${labelHari[i]}<span class="block text-[10px] font-normal opacity-80">${tampil}</span>`;
            btn.onclick = () => qbPilihTanggal(iso, btn);
            wrap.appendChild(btn);

            if (i === 0) qbPilihTanggal(iso, btn);
        }
    }

    function qbPilihTanggal(iso, btnAktif) {
        document.querySelectorAll('.qb-date-pill').forEach(b => {
            b.className = 'py-2 px-3 bg-[#F8FAFC] border border-[#E2E8F0] hover:border-slate-300 text-[#0F172A] rounded-xl text-xs font-bold text-center qb-date-pill';
        });
        btnAktif.className = 'py-2 px-3 bg-[#0F172A] text-white rounded-xl text-xs font-bold text-center shadow-md qb-date-pill';

        document.getElementById('qbTanggalInput').value = iso;
        document.getElementById('qbJamInput').value = '';
        document.getElementById('qbTotal').textContent = 'Rp0';

        const grid = document.getElementById('qbSlotGrid');
        grid.innerHTML = '<p class="col-span-full text-slate-400 text-xs py-3 text-center">Memuat slot...</p>';

        fetch(`{{ route('booking.slot-tersedia') }}?lapangan_id=${document.getElementById('qbLapanganId').value}&tanggal=${iso}`)
            .then(res => res.json())
            .then(data => {
                grid.innerHTML = '';
                data.slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = slot.label;

                    if (!slot.tersedia) {
                        btn.disabled = true;
                        btn.className = 'py-2 bg-slate-100 text-slate-300 font-semibold rounded-xl border border-slate-200 cursor-not-allowed line-through';
                    } else {
                        btn.className = 'py-2 bg-white border border-[#E2E8F0] hover:border-[#10B981] text-[#0F172A] font-semibold rounded-xl transition';
                        btn.onclick = () => qbPilihJam(slot.jam, btn);
                    }
                    grid.appendChild(btn);
                });
            });
    }

    function qbToggleBukti() {
        const metode = document.getElementById('qbMetode').value;
        document.getElementById('qbBuktiWrap').classList.toggle('hidden', metode === 'Cash');
    }

    function qbPilihJam(jam, btnAktif) {
        document.querySelectorAll('#qbSlotGrid button:not(:disabled)').forEach(b => {
            b.className = 'py-2 bg-white border border-[#E2E8F0] hover:border-[#10B981] text-[#0F172A] font-semibold rounded-xl transition';
        });
        btnAktif.className = 'py-2 bg-[#10B981] text-white font-bold rounded-xl shadow-md shadow-[#10B981]/20';

        document.getElementById('qbJamInput').value = jam;
        document.getElementById('qbJumlahBayarInput').value = qbHarga;
        document.getElementById('qbTotal').textContent = `Rp${qbHarga.toLocaleString('id-ID')}`;
    }

    document.getElementById('formQuickBook').addEventListener('submit', function (e) {
        if (!document.getElementById('qbJamInput').value) {
            e.preventDefault();
            alert('Pilih slot jam terlebih dahulu.');
        }
    });

    @if ($errors->any() && old('lapangan_id') && request()->routeIs('booking.store'))
        document.addEventListener('DOMContentLoaded', () => openModal('modalQuickBook'));
    @endif
</script>