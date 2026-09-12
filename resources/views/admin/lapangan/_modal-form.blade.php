{{-- ============================================================
     MODAL: Tambah / Edit Lapangan (satu modal, dua mode)
     ============================================================ --}}
<div id="modalFormLapangan" class="fixed hidden inset-0 bg-[#0F172A]/60 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-[#E2E8F0] overflow-hidden flex flex-col max-h-[92vh]">

        {{-- Header --}}
        <div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div id="modalLapanganIcon" class="w-9 h-9 bg-[#10B981] text-white rounded-xl flex items-center justify-center font-bold">+</div>
                <div>
                    <h3 id="modalLapanganTitle" class="text-base font-bold text-[#0F172A]">Tambah Lapangan Baru</h3>
                    <p class="text-xs text-slate-500">Input spesifikasi, tarif, dan fasilitas lapangan</p>
                </div>
            </div>
            <button type="button" onclick="closeModalLapangan()" class="w-8 h-8 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center">✕</button>
        </div>

        {{-- Form --}}
        <form id="formLapangan" method="POST" action="{{ route('admin.lapangan.store') }}"
              enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto text-xs">
            @csrf
            <input type="hidden" name="_method" id="formLapanganMethod" value="">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl p-3 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Nama & Kode --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Nama Lapangan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lapangan" id="fNamaLapangan" placeholder="Contoh: Lapangan Futsal Synthetic C"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A] focus:ring-2 focus:ring-[#10B981]">
                </div>
                <div>
                    <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Kode Unik</label>
                    <input type="text" name="kode_lapangan" id="fKodeLapangan" placeholder="FLP-03"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-semibold text-[#0F172A] focus:ring-2 focus:ring-[#10B981]">
                </div>
            </div>

            {{-- Kategori & Tipe Lantai --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Kategori Olahraga</label>
                    <select name="jenis_lapangan" id="fJenisLapangan" class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-bold text-[#0F172A]">
                        <option value="Futsal">Futsal</option>
                        <option value="Badminton">Badminton</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Tipe Lantai/Permukaan</label>
                    <input type="text" name="tipe_lantai" id="fTipeLantai" placeholder="mis. Rumput Sintetis"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-bold text-[#0F172A]">
                </div>
            </div>

            {{-- Lokasi & Status --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Lokasi</label>
                    <input type="text" name="lokasi" id="fLokasi" placeholder="mis. Gedung Utama (Indoor)"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-bold text-[#0F172A]">
                </div>
                <div>
                    <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Status</label>
                    <select name="status" id="fStatus" class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs font-bold text-[#0F172A]">
                        <option value="Aktif">Aktif Beroperasi</option>
                        <option value="Maintenance">Perbaikan / Maintenance</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- Tarif --}}
            <div class="p-4 bg-[#F8FAFC] rounded-2xl border border-[#E2E8F0] space-y-3">
                <label class="block font-bold text-[#0F172A] uppercase tracking-wider">Tarif Sewa (Per Jam)</label>
                <input type="number" name="harga_per_jam" id="fHargaPerJam" placeholder="150000" min="0" step="1000"
                       class="w-full px-3 py-2 bg-white border border-[#E2E8F0] rounded-xl font-bold text-[#0F172A]">
            </div>

            {{-- Fasilitas --}}
            <div>
                <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-2">Fasilitas Pendukung</label>
                <div class="grid grid-cols-2 gap-2 text-slate-600 font-semibold">
                    @foreach ($fasilitasTersedia as $f)
                        <label class="flex items-center gap-2 p-2 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0]">
                            <input type="checkbox" name="fasilitas[]" value="{{ $f }}" class="fasilitas-checkbox w-4 h-4 text-[#10B981] border-slate-300 rounded focus:ring-[#10B981]">
                            {{ $f }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Upload Foto --}}
            <div>
                <label class="block font-bold text-[#0F172A] uppercase tracking-wider mb-1">Foto Lapangan</label>
                <div id="previewFotoWrap" class="hidden mb-2">
                    <img id="previewFoto" src="" alt="Foto saat ini" class="w-full h-32 object-cover rounded-xl border border-[#E2E8F0]">
                    <p class="text-[10px] text-slate-400 mt-1">Foto saat ini. Unggah file baru untuk menggantinya.</p>
                </div>
                <div class="border-2 border-dashed border-[#E2E8F0] hover:border-[#10B981] p-4 rounded-2xl text-center bg-[#F8FAFC] transition cursor-pointer"
                     onclick="document.getElementById('fGambar').click()">
                    <p class="text-slate-500 font-semibold">Tarik &amp; Lepas foto di sini, atau <span class="text-[#10B981] font-bold">Pilih File</span></p>
                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB)</p>
                </div>
                <input type="file" name="gambar" id="fGambar" accept="image/*" class="hidden">
                <p id="fileNameLabel" class="text-[10px] text-slate-500 mt-1"></p>
            </div>

            <button type="submit" id="submitLapanganBtn"
                    class="w-full py-3.5 bg-[#10B981] hover:bg-emerald-600 text-white font-bold rounded-xl text-xs shadow-lg shadow-[#10B981]/25 transition mt-2">
                Simpan Lapangan Baru
            </button>
        </form>
    </div>
</div>

<script>
    const modalLapangan = document.getElementById('modalFormLapangan');
    const formLapangan = document.getElementById('formLapangan');

    function closeModalLapangan() {
        modalLapangan.classList.add('hidden');
        modalLapangan.classList.remove('flex');
    }

    function resetFormLapangan() {
        formLapangan.reset();
        document.querySelectorAll('.fasilitas-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('previewFotoWrap').classList.add('hidden');
        document.getElementById('fileNameLabel').textContent = '';
    }

    function openTambahLapangan() {
        resetFormLapangan();
        document.getElementById('modalLapanganTitle').textContent = 'Tambah Lapangan Baru';
        document.getElementById('modalLapanganIcon').textContent = '+';
        document.getElementById('submitLapanganBtn').textContent = 'Simpan Lapangan Baru';
        formLapangan.action = "{{ route('admin.lapangan.store') }}";
        document.getElementById('formLapanganMethod').value = '';

        modalLapangan.classList.remove('hidden');
        modalLapangan.classList.add('flex');
    }

    function openEditLapangan(data) {
        resetFormLapangan();
        document.getElementById('modalLapanganTitle').textContent = 'Edit ' + data.nama_lapangan;
        document.getElementById('modalLapanganIcon').textContent = '✏️';
        document.getElementById('submitLapanganBtn').textContent = 'Simpan Perubahan';

        formLapangan.action = `{{ url('admin/lapangan') }}/${data.id}`;
        document.getElementById('formLapanganMethod').value = 'PUT';

        document.getElementById('fNamaLapangan').value = data.nama_lapangan ?? '';
        document.getElementById('fKodeLapangan').value = data.kode_lapangan ?? '';
        document.getElementById('fJenisLapangan').value = data.jenis_lapangan ?? 'Futsal';
        document.getElementById('fTipeLantai').value = data.tipe_lantai ?? '';
        document.getElementById('fLokasi').value = data.lokasi ?? '';
        document.getElementById('fStatus').value = data.status ?? 'Aktif';
        document.getElementById('fHargaPerJam').value = data.harga_per_jam ?? '';

        const fasilitasAktif = data.fasilitas ?? [];
        document.querySelectorAll('.fasilitas-checkbox').forEach(cb => {
            cb.checked = fasilitasAktif.includes(cb.value);
        });

        if (data.gambar_url) {
            document.getElementById('previewFoto').src = data.gambar_url;
            document.getElementById('previewFotoWrap').classList.remove('hidden');
        }

        modalLapangan.classList.remove('hidden');
        modalLapangan.classList.add('flex');
    }

    document.getElementById('fGambar').addEventListener('change', function () {
        document.getElementById('fileNameLabel').textContent = this.files[0]?.name ?? '';
    });

    modalLapangan.addEventListener('click', (e) => {
        if (e.target === modalLapangan) closeModalLapangan();
    });

    {{-- Buka modal otomatis (mode Tambah) kalau validasi gagal --}}
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            modalLapangan.classList.remove('hidden');
            modalLapangan.classList.add('flex');
        });
    @endif
</script>