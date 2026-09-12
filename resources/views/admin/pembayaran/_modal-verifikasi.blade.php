{{-- ============================================================
     MODAL: Verifikasi / Tolak Pembayaran
     ============================================================ --}}
<div id="modalVerifikasi" class="fixed hidden inset-0 bg-[#0F172A]/60 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-[#E2E8F0] overflow-hidden">

        <div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between">
            <h3 class="text-base font-extrabold text-[#0F172A]">Verifikasi Pembayaran</h3>
            <button type="button" onclick="closeVerifikasiModal()" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-full flex items-center justify-center">✕</button>
        </div>

        <div class="p-5 space-y-4">

            {{-- Info ringkas --}}
            <div id="verInfo" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-4 text-xs space-y-1.5">
                {{-- diisi JS --}}
            </div>

            {{-- Preview bukti transfer --}}
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bukti Transfer</p>
                <div id="verBuktiWrap" class="rounded-xl overflow-hidden border border-[#E2E8F0]">
                    {{-- diisi JS: <img> atau pesan "tanpa bukti" --}}
                </div>
            </div>

            <form id="formVerifikasi" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="_method" value="PATCH">

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Catatan (wajib diisi jika menolak)
                    </label>
                    <textarea name="catatan" id="verCatatan" rows="2" placeholder="Contoh: Bukti transfer tidak jelas / nominal tidak sesuai"
                              class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs text-[#0F172A]"></textarea>
                </div>

                <div class="flex gap-2 pt-1">
                    <button type="submit" id="btnTolak" onclick="return validasiTolak(event)"
                            class="flex-1 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-xs">
                        Tolak
                    </button>
                    <button type="submit" id="btnVerifikasi"
                            class="flex-1 py-3 bg-[#10B981] hover:bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-md">
                        ✓ Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const verModal = document.getElementById('modalVerifikasi');

    function openVerifikasiModal(data) {
        document.getElementById('verInfo').innerHTML = `
            <div class="flex justify-between"><span class="text-slate-400">Pelanggan</span><span class="font-bold text-[#0F172A]">${data.nama}</span></div>
            <div class="flex justify-between"><span class="text-slate-400">Lapangan</span><span class="font-bold text-[#0F172A]">${data.lapangan}</span></div>
            <div class="flex justify-between"><span class="text-slate-400">Jadwal</span><span class="font-bold text-[#0F172A]">${data.jadwal}</span></div>
            <div class="flex justify-between"><span class="text-slate-400">Metode</span><span class="font-bold text-[#0F172A]">${data.metode}</span></div>
            <div class="flex justify-between border-t border-[#E2E8F0] pt-1.5 mt-1.5"><span class="text-slate-400">Jumlah</span><span class="font-extrabold text-[#0F172A]">${data.jumlah}</span></div>
        `;

        const buktiWrap = document.getElementById('verBuktiWrap');
        buktiWrap.innerHTML = data.bukti_url
            ? `<a href="${data.bukti_url}" target="_blank"><img src="${data.bukti_url}" class="w-full max-h-56 object-contain bg-slate-50"></a>`
            : `<div class="p-6 text-center text-xs text-slate-400 bg-slate-50">Tidak ada bukti transfer diunggah (kemungkinan bayar cash di tempat).</div>`;

        document.getElementById('verCatatan').value = '';
        document.getElementById('btnVerifikasi').formAction = data.verify_url;
        document.getElementById('btnTolak').formAction = data.reject_url;

        verModal.classList.remove('hidden');
        verModal.classList.add('flex');
    }

    function closeVerifikasiModal() {
        verModal.classList.add('hidden');
        verModal.classList.remove('flex');
    }

    function validasiTolak(e) {
        const catatan = document.getElementById('verCatatan').value.trim();
        if (!catatan) {
            alert('Isi catatan/alasan penolakan terlebih dahulu.');
            e.preventDefault();
            return false;
        }
        return confirm('Tolak pembayaran ini?');
    }

    verModal.addEventListener('click', (e) => {
        if (e.target === verModal) closeVerifikasiModal();
    });

    // Event delegation: menangkap klik tombol "Verifikasi" di tabel (termasuk
    // setelah pagination/filter reload halaman) dan baca payload dari data-attribute.
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-verifikasi');
        if (!btn) return;

        try {
            const data = JSON.parse(btn.dataset.payload);
            openVerifikasiModal(data);
        } catch (err) {
            console.error('Payload verifikasi tidak valid', err);
        }
    });
</script>