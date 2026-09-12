{{-- ============================================================
     MODAL: Detail Reservasi (reusable — konten dimuat via fetch)
     ============================================================ --}}
<div id="modalDetailReservasi" class="fixed hidden inset-0 bg-[#0F172A]/60 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-[#E2E8F0] overflow-hidden flex flex-col max-h-[90vh]">
        <div id="detailContent">
            {{-- Diisi otomatis lewat JS saat tombol "Detail" diklik --}}
        </div>
    </div>
</div>

<script>
    const detailModal = document.getElementById('modalDetailReservasi');
    const detailContent = document.getElementById('detailContent');

    function openDetailModal(reservasiId) {
        detailContent.innerHTML = `
            <div class="p-14 text-center text-slate-400 text-sm">
                Memuat detail reservasi...
            </div>`;
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');

        fetch(`{{ url('admin/reservasi') }}/${reservasiId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((res) => {
                if (!res.ok) throw new Error('Gagal memuat');
                return res.text();
            })
            .then((html) => { detailContent.innerHTML = html; })
            .catch(() => {
                detailContent.innerHTML = `
                    <div class="p-14 text-center text-red-500 text-sm">
                        Gagal memuat detail reservasi. Coba lagi.
                    </div>`;
            });
    }

    function closeDetailModal() {
        detailModal.classList.add('hidden');
        detailModal.classList.remove('flex');
    }

    function openEditModal(reservasiId) {
        detailContent.innerHTML = `
            <div class="p-14 text-center text-slate-400 text-sm">
                Memuat form edit jadwal...
            </div>`;
 
        fetch(`{{ url('admin/reservasi') }}/${reservasiId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((res) => {
                if (!res.ok) throw new Error('Gagal memuat');
                return res.text();
            })
            .then((html) => { detailContent.innerHTML = html; })
            .catch(() => {
                detailContent.innerHTML = `
                    <div class="p-14 text-center text-red-500 text-sm">
                        Gagal memuat form edit jadwal. Coba lagi.
                    </div>`;
            });
    }

    // Tutup modal saat klik area gelap di luar kartu
    detailModal.addEventListener('click', (e) => {
        if (e.target === detailModal) closeDetailModal();
    });
</script>