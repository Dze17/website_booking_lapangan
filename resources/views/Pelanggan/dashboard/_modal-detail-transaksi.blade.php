{{-- ============================================================
     MODAL: Detail Transaksi (read-only) — pengganti blok QR Code
     ============================================================ --}}
<div id="modalDetailTransaksi" class="fixed inset-0 bg-[#0F172A]/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] overflow-hidden flex flex-col max-h-[90vh]">
        <div id="detailTransaksiContent">
            {{-- diisi otomatis via JS --}}
        </div>
    </div>
</div>

<script>
    const modalDetailTransaksi = document.getElementById('modalDetailTransaksi');
    const detailTransaksiContent = document.getElementById('detailTransaksiContent');

    function openDetailTransaksi(reservasiId) {
        detailTransaksiContent.innerHTML = `
            <div class="p-14 text-center text-slate-400 text-sm">Memuat detail transaksi...</div>`;
        modalDetailTransaksi.classList.remove('hidden');
        modalDetailTransaksi.classList.add('flex');

        fetch(`{{ url('reservasi-saya') }}/${reservasiId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((res) => {
                if (!res.ok) throw new Error('Gagal memuat');
                return res.text();
            })
            .then((html) => { detailTransaksiContent.innerHTML = html; })
            .catch(() => {
                detailTransaksiContent.innerHTML = `
                    <div class="p-14 text-center text-red-500 text-sm">
                        Gagal memuat detail transaksi. Coba lagi.
                    </div>`;
            });
    }

    function closeDetailTransaksi() {
        modalDetailTransaksi.classList.add('hidden');
        modalDetailTransaksi.classList.remove('flex');
    }

    modalDetailTransaksi.addEventListener('click', (e) => {
        if (e.target === modalDetailTransaksi) closeDetailTransaksi();
    });
</script>