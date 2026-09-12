<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LapanganController extends Controller
{
    /** Daftar fasilitas yang bisa dipilih (dipakai form tambah/edit & badge di kartu). */
    public const FASILITAS_TERSEDIA = [
        'Lampu LED Terang',
        'Indoor / Beratap',
        'Jaring Pengaman Keliling',
        'Bench Pemain',
        'Net Profesional',
        'Tribun Penonton',
    ];

    public function index(Request $request): View
    {
        $query = Lapangan::query();

        if ($request->filled('cari')) {
            $keyword = $request->input('cari');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lapangan', 'like', "%{$keyword}%")
                    ->orWhere('kode_lapangan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_lapangan', $request->input('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $lapangan = $query->orderBy('jenis_lapangan')->orderBy('nama_lapangan')->get();

        return view('admin.lapangan.index', [
            'lapangan' => $lapangan,
            'jumlahAktif' => Lapangan::where('status', 'Aktif')->count(),
            'filter' => $request->only(['cari', 'jenis', 'status']),
            'fasilitasTersedia' => self::FASILITAS_TERSEDIA,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('lapangan', 'public');
        }

        Lapangan::create($validated);

        return redirect()->route('admin.lapangan.index')
            ->with('success', 'Lapangan baru berhasil ditambahkan.');
    }

    public function update(Request $request, Lapangan $lapangan): RedirectResponse
    {
        $validated = $this->validasi($request, $lapangan);

        if ($request->hasFile('gambar')) {
            if ($lapangan->gambar) {
                Storage::disk('public')->delete($lapangan->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('lapangan', 'public');
        }

        $lapangan->update($validated);

        return redirect()->route('admin.lapangan.index')
            ->with('success', "Data {$lapangan->nama_lapangan} berhasil diperbarui.");
    }

    /** Toggle cepat Aktif <-> Maintenance lewat tombol di kartu. */
    public function toggleStatus(Lapangan $lapangan): RedirectResponse
    {
        $statusBaru = $lapangan->status === 'Maintenance' ? 'Aktif' : 'Maintenance';
        $lapangan->update(['status' => $statusBaru]);

        return back()->with('success', "Status {$lapangan->nama_lapangan} diubah menjadi {$statusBaru}.");
    }

    protected function validasi(Request $request, ?Lapangan $lapangan = null): array
    {
        return $request->validate([
            'nama_lapangan' => ['required', 'string', 'max:50'],
            'kode_lapangan' => [
                'nullable', 'string', 'max:20',
                Rule::unique('lapangan', 'kode_lapangan')->ignore($lapangan?->id),
            ],
            'jenis_lapangan' => ['required', 'in:Futsal,Badminton'],
            'tipe_lantai' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'harga_per_jam' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:Aktif,Nonaktif,Maintenance'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['string'],
            'gambar' => ['nullable', 'image', 'max:2048'],
        ], [
            'kode_lapangan.unique' => 'Kode lapangan sudah dipakai, gunakan kode lain.',
            'gambar.image' => 'File harus berupa gambar (JPG/PNG/WEBP).',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);
    }
}