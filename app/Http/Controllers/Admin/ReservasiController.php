<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservasiController extends Controller
{
    /**
     * Menampilkan daftar reservasi dengan filter & pencarian.
     */
    public function index(Request $request): View
    {
        $query = Reservasi::with([
            'pelanggan', 
            'lapangan',
            'dibuatOleh', 
            'pembayaran' => function ($q) {
            $q->orderByDesc('tanggal_bayar');
        }]);

        // --- Filter tanggal ---
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_main', $request->input('tanggal'));
        }

        // --- Filter lapangan ---
        if ($request->filled('lapangan_id')) {
            $query->where('lapangan_id', $request->input('lapangan_id'));
        }

        // --- Filter status reservasi ---
        if ($request->filled('status')) {
            $query->where('status_reservasi', $request->input('status'));
        }

        // --- Filter tipe input ---
        if ($request->filled('tipe')) {
            $query->where('tipe_input', $request->input('tipe'));
        }

        // --- Pencarian nama/telepon (pelanggan terdaftar maupun tamu) ---
        if ($request->filled('cari')) {
            $keyword = $request->input('cari');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_tamu', 'like', "%{$keyword}%")
                    ->orWhere('no_telepon_tamu', 'like', "%{$keyword}%")
                    ->orWhereHas('pelanggan', function ($q2) use ($keyword) {
                        $q2->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('no_telepon', 'like', "%{$keyword}%");
                    });
            });
        }

        $reservasi = $query->orderByDesc('tanggal_main')
            ->orderByDesc('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        $ringkasan = [
            'total_bulan_ini' => Reservasi::whereMonth('tanggal_main', now()->month)
                ->whereYear('tanggal_main', now()->year)
                ->count(),
            'menunggu_konfirmasi' => Reservasi::where('status_reservasi', 'Pending')->count(),
        ];

        return view('admin.reservasi.index', [
            'reservasi' => $reservasi,
            'ringkasan' => $ringkasan,
            'lapanganList' => Lapangan::orderBy('nama_lapangan')->get(),
            'pelangganList' => User::where('role', 'pelanggan')->orderBy('nama')->get(),
            'filter' => $request->only(['tanggal', 'lapangan_id', 'status', 'tipe', 'cari']),
        ]);
    }

    public function show(Reservasi $reservasi): View
    {
        $reservasi->load([
            'pelanggan',
            'lapangan',
            'dibuatOleh',
            'pembayaran' => function ($q) {
                $q->orderByDesc('tanggal_bayar');
            },
        ]);

        return view('admin.reservasi._detail-content', compact('reservasi'));
    }

    public function edit(Reservasi $reservasi): View
    {
        $reservasi->load(['pelanggan', 'lapangan']);
 
        return view('admin.reservasi._edit-content', [
            'reservasi' => $reservasi,
            'lapanganList' => Lapangan::orderBy('nama_lapangan')->get(),
        ]);
    }

     protected function cekBentrokJadwal(
        int $lapanganId,
        string $tanggalMain,
        string $jamMulai,
        string $jamSelesai,
        ?int $kecualikanId = null
    ): bool {
        return Reservasi::where('lapangan_id', $lapanganId)
            ->where('tanggal_main', $tanggalMain)
            ->where('status_reservasi', '!=', 'Dibatalkan')
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai)
            ->when($kecualikanId, fn ($q) => $q->where('id', '!=', $kecualikanId))
            ->lockForUpdate()
            ->exists();
    }

    public function update(Request $request, Reservasi $reservasi): RedirectResponse
    {
        $validated = $request->validate([
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal_main' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'total_harga' => ['required', 'numeric', 'min:0'],
            'beri_tahu_pelanggan' => ['nullable', 'boolean'],
        ], [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'tanggal_main.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
        ]);

        $jadwalBerubah = $reservasi->lapangan_id != $validated['lapangan_id']
            || $reservasi->tanggal_main->toDateString() !== $validated['tanggal_main']
            || \Carbon\Carbon::parse($reservasi->jam_mulai)->format('H:i') !== $validated['jam_mulai'];
 
        try {
            DB::transaction(function () use ($validated, $reservasi) {
                // Cegah bentrok dengan reservasi LAIN (kecualikan dirinya sendiri).
                // Dicek+disimpan dalam satu transaction supaya atomik.
                if ($this->cekBentrokJadwal(
                    $validated['lapangan_id'],
                    $validated['tanggal_main'],
                    $validated['jam_mulai'],
                    $validated['jam_selesai'],
                    $reservasi->id
                )) {
                    throw ValidationException::withMessages([
                        'jam_mulai' => 'Slot ini bentrok dengan reservasi lain pada rentang jam tersebut. Silakan pilih jam atau lapangan lain.',
                    ]);
                }
 
                $reservasi->update([
                    'lapangan_id' => $validated['lapangan_id'],
                    'tanggal_main' => $validated['tanggal_main'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'total_harga' => $validated['total_harga'],
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (QueryException $e) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Slot ini baru saja dipesan pengguna lain. Silakan pilih jam lain.']);
        }
 
        if ($jadwalBerubah && $request->boolean('beri_tahu_pelanggan')) {
            $this->catatNotifikasi(
                $reservasi,
                'Konfirmasi',
                "Jadwal reservasi Anda diubah menjadi {$reservasi->lapangan->nama_lapangan}, "
                    . "{$reservasi->tanggal_main->format('d M Y')} {$reservasi->jam_mulai}-{$reservasi->jam_selesai}."
            );
        }
 
        return redirect()->route('admin.reservasi.index')
            ->with('success', "Jadwal reservasi #{$reservasi->id} berhasil diperbarui.");
    }


    /**
     * Menyimpan reservasi manual (walk-in) yang diinput admin lewat modal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:terdaftar,tamu'],
            'user_id' => ['required_if:mode,terdaftar', 'nullable', 'exists:users,id'],
            'nama_tamu' => ['required_if:mode,tamu', 'nullable', 'string', 'max:100'],
            'no_telepon_tamu' => ['required_if:mode,tamu', 'nullable', 'string', 'max:20'],
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal_main' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'total_harga' => ['required', 'numeric', 'min:0'],
            'status_reservasi' => ['required', 'in:Pending,Dikonfirmasi'],
            'catat_pembayaran' => ['nullable', 'boolean'],
            'metode' => ['required_if:catat_pembayaran,1', 'nullable', 'in:Cash,Transfer,E-Wallet,Payment Gateway'],
            'jumlah_bayar' => ['required_if:catat_pembayaran,1', 'nullable', 'numeric', 'min:0'],
            'status_pembayaran' => ['required_if:catat_pembayaran,1', 'nullable', 'in:Pending,Verified'],
        ], [
            'user_id.required_if' => 'Pilih pelanggan terdaftar.',
            'nama_tamu.required_if' => 'Nama tamu wajib diisi.',
            'no_telepon_tamu.required_if' => 'Nomor telepon tamu wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'tanggal_main.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Dicek DI DALAM transaction (bukan sebelum-nya) supaya cek + insert atomik.
                if ($this->cekBentrokJadwal(
                    $validated['lapangan_id'],
                    $validated['tanggal_main'],
                    $validated['jam_mulai'],
                    $validated['jam_selesai']
                )) {
                    throw ValidationException::withMessages([
                        'jam_mulai' => 'Slot ini bentrok dengan reservasi lain pada rentang jam tersebut. Silakan pilih jam atau lapangan lain.',
                    ]);
                }
 
                $reservasi = Reservasi::create([
                    'user_id' => $validated['mode'] === 'terdaftar' ? $validated['user_id'] : null,
                    'lapangan_id' => $validated['lapangan_id'],
                    'dibuat_oleh' => auth()->id(),
                    'nama_tamu' => $validated['mode'] === 'tamu' ? $validated['nama_tamu'] : null,
                    'no_telepon_tamu' => $validated['mode'] === 'tamu' ? $validated['no_telepon_tamu'] : null,
                    'tanggal_main' => $validated['tanggal_main'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'total_harga' => $validated['total_harga'],
                    'tipe_input' => 'Manual',
                    'status_reservasi' => $validated['status_reservasi'],
                ]);
 
                if ($request->boolean('catat_pembayaran')) {
                    Pembayaran::create([
                        'reservasi_id' => $reservasi->id,
                        'diverifikasi_oleh' => $validated['status_pembayaran'] === 'Verified' ? auth()->id() : null,
                        'jenis_pembayaran' => 'Full',
                        'metode' => $validated['metode'],
                        'jumlah_bayar' => $validated['jumlah_bayar'],
                        'status_pembayaran' => $validated['status_pembayaran'],
                        'tanggal_bayar' => now(),
                    ]);
                }
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (QueryException $e) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Slot ini baru saja dipesan pengguna lain. Silakan pilih jam lain.']);
        }

        return redirect()->route('admin.reservasi.index')
            ->with('success', 'Reservasi manual berhasil ditambahkan.');
    }

    /**
     * Mengkonfirmasi reservasi berstatus Pending.
     */
    public function confirm(Reservasi $reservasi): RedirectResponse
    {
        $reservasi->update([
            'status_reservasi' => 'Dikonfirmasi',
            'keterangan' => null,
            ]);
        
        $this->catatNotifikasi($reservasi, 'konfirmasi', 'Reservasi anda telah dikonfirmasi');

        return back()->with('success', "Reservasi #{$reservasi->id} dikonfirmasi.");
    }

    /**
     * Membatalkan reservasi.
     */
    public function cancel(Request $request, Reservasi $reservasi): RedirectResponse
    {
        $request->validate([
            'alasan' => ['nullable', 'string', 'max:255'],
        ]);

        $reservasi->update([
            'status_reservasi' => 'Dibatalkan',
            'keterangan' => $request->input('alasan'),
        ]);

        $pesan = $request->filled('alasan') 
            ? "Reservasi anda telah dibatalkan. Alasan: {$request->input('alasan')}" 
            : "Reservasi anda telah dibatalkan.";
        $this->catatNotifikasi($reservasi, 'pembatalan', $pesan);

        return back()->with('success', "Reservasi #{$reservasi->id} dibatalkan.");
    }

     /**
     * Mengirim ulang notifikasi (simulasi WA/email) untuk satu reservasi.
     */
    public function resendNotifikasi(Reservasi $reservasi): RedirectResponse
    {
        $tipe = match ($reservasi->status_reservasi) {
            'Dikonfirmasi' => 'Konfirmasi',
            'Dibatalkan' => 'Pembatalan',
            default => 'Pengingat',
        };
 
        $this->catatNotifikasi(
            $reservasi,
            $tipe,
            "Pengingat reservasi {$reservasi->lapangan->nama_lapangan} pada {$reservasi->tanggal_main->format('d M Y')}."
        );
 
        return back()->with('success', 'Notifikasi berhasil dikirim ulang ke pelanggan.');
    }

    /**
     * Mencatat notifikasi ke tabel notifikasi.
     * NOTE: ini baru mencatat record-nya saja (status "Sent" disimulasikan).
     * Integrasi pengiriman WhatsApp/email sungguhan bisa ditambahkan di sini nanti
     * (mis. panggil WhatsApp Business API), lalu update status_kirim sesuai hasilnya.
     */
    protected function catatNotifikasi(Reservasi $reservasi, string $tipe, string $pesan): void
    {
        Notifikasi::create([
            'reservasi_id' => $reservasi->id,
            'tipe' => $tipe,
            'pesan' => $pesan,
            'status_kirim' => 'Sent',
            'dikirim_pada' => now(),
        ]);
    }
}