<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{

    public function index(Request $request): View
    {
        $user = auth()->user();
 
        $query = Reservasi::with('lapangan')->where('user_id', $user->id);
 
        if ($request->filled('status') && $request->input('status') !== 'semua') {
            if ($request->input('status') === 'akan_datang') {
                $query->whereIn('status_reservasi', ['Pending', 'Dikonfirmasi'])
                    ->whereDate('tanggal_main', '>=', now()->toDateString());
            } else {
                $query->where('status_reservasi', $request->input('status'));
            }
        }
 
        $reservasi = $query->orderByDesc('tanggal_main')
            ->orderByDesc('jam_mulai')
            ->paginate(6)
            ->withQueryString();
 
        $ringkasan = [
            'akan_datang' => Reservasi::where('user_id', $user->id)
                ->whereIn('status_reservasi', ['Pending', 'Dikonfirmasi'])
                ->whereDate('tanggal_main', '>=', now()->toDateString())
                ->count(),
            'selesai' => Reservasi::where('user_id', $user->id)->where('status_reservasi', 'Selesai')->count(),
            'dibatalkan' => Reservasi::where('user_id', $user->id)->where('status_reservasi', 'Dibatalkan')->count(),
        ];
 
        return view('Pelanggan.reservasi.reservasi-saya', [
            'reservasi' => $reservasi,
            'ringkasan' => $ringkasan,
            'filterStatus' => $request->input('status', 'semua'),
        ]);
    }

    /**
     * Mengembalikan daftar jam operasional lapangan + status tersedia/terisi
     * pada tanggal tertentu. Dipanggil via fetch dari modal booking.
     */
    public function slotTersedia(Request $request): JsonResponse
    {
        $request->validate([
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal' => ['required', 'date'],
        ]);

        $lapangan = Lapangan::findOrFail($request->input('lapangan_id'));
        $jamBuka = (int) Carbon::parse($lapangan->jam_buka)->format('H');
        $jamTutup = (int) Carbon::parse($lapangan->jam_tutup)->format('H');

        $reservasiHariItu = Reservasi::where('lapangan_id', $lapangan->id)
            ->where('tanggal_main', $request->input('tanggal'))
            ->where('status_reservasi', '!=', 'Dibatalkan')
            ->get();

        $slots = [];
        for ($jam = $jamBuka; $jam < $jamTutup; $jam++) {
            $mulai = sprintf('%02d:00:00', $jam);
            $selesai = sprintf('%02d:00:00', $jam + 1);

            // Terisi jika ada reservasi yang overlap dengan jam ini
            $terisi = $reservasiHariItu->contains(function ($r) use ($mulai, $selesai) {
                return $r->jam_mulai < $selesai && $r->jam_selesai > $mulai;
            });

            $slots[] = [
                'jam' => $jam,
                'label' => sprintf('%02d:00 - %02d:00', $jam, $jam + 1),
                'tersedia' => ! $terisi,
            ];
        }

        return response()->json([
            'harga_per_jam' => (float) $lapangan->harga_per_jam,
            'slots' => $slots,
        ]);
    }

    /**
     * Menyimpan reservasi baru dari pelanggan (booking online).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lapangan_id' => ['required', 'exists:lapangan,id'],
            'tanggal_main' => ['required', 'date', 'after_or_equal:today'],
            'jam_list' => ['required', 'array', 'min:1'],
            'jam_list.*' => ['integer', 'min:0', 'max:23'],
            'jenis_pembayaran' => ['required', 'in:DP,Full'],
            'metode' => ['required', 'in:Cash,Transfer,E-Wallet'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'bukti_pembayaran' => ['nullable', 'image', 'max:2048'],
        ], [
            'jam_list.required' => 'Pilih minimal 1 slot jam.',
            'jumlah_bayar.required' => 'Isi jumlah pembayaran terlebih dahulu.',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa gambar (JPG/PNG/WEBP).',
            'bukti_pembayaran.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
        ]);
 
        $jamList = collect($validated['jam_list'])->map(fn ($j) => (int) $j)->sort()->values();
 
        // Pastikan slot yang dipilih berurutan (tidak boleh ada lompatan jam)
        for ($i = 1; $i < $jamList->count(); $i++) {
            if ($jamList[$i] !== $jamList[$i - 1] + 1) {
                return back()->withInput()
                    ->withErrors(['jam_list' => 'Slot jam yang dipilih harus berurutan, tidak boleh loncat.']);
            }
        }
 
        $jamMulai = sprintf('%02d:00:00', $jamList->first());
        $jamSelesai = sprintf('%02d:00:00', $jamList->last() + 1);
 
        $lapangan = Lapangan::findOrFail($validated['lapangan_id']);
        $totalHarga = $lapangan->harga_per_jam * $jamList->count();
 
        // Path file diproses SEBELUM transaction (upload file tidak perlu ikut di-rollback).
        $pathBukti = $request->hasFile('bukti_pembayaran')
            ? $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public')
            : null;
 
        try {
            DB::transaction(function () use ($validated, $lapangan, $jamMulai, $jamSelesai, $totalHarga, $pathBukti) {
                // Cek overlap penuh (bukan cuma exact jam_mulai) — supaya booking
                // multi-jam tidak bisa tabrakan sebagian dengan reservasi lain.
                // lockForUpdate() dipakai di dalam transaction untuk memperkecil race condition.
                $bentrok = Reservasi::where('lapangan_id', $lapangan->id)
                    ->where('tanggal_main', $validated['tanggal_main'])
                    ->where('status_reservasi', '!=', 'Dibatalkan')
                    ->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai)
                    ->lockForUpdate()
                    ->exists();
 
                if ($bentrok) {
                    throw ValidationException::withMessages([
                        'jam_list' => 'Salah satu slot yang kamu pilih baru saja dipesan orang lain. Silakan pilih ulang.',
                    ]);
                }
 
                $reservasi = Reservasi::create([
                    'user_id' => auth()->id(),
                    'nama_tamu' => auth()->user()->nama,
                    'no_telepon_tamu' => auth()->user()->no_telepon,
                    'lapangan_id' => $lapangan->id,
                    'dibuat_oleh' => auth()->id(),
                    'tanggal_main' => $validated['tanggal_main'],
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'total_harga' => $totalHarga,
                    'tipe_input' => 'Online',
                    'status_reservasi' => 'Pending',
                ]);
 
                // Pembayaran yang dilaporkan pelanggan sendiri SELALU berstatus
                // Pending — baru berubah jadi Verified setelah admin mengecek
                // buktinya di menu Transaksi & Keuangan / Detail Reservasi.
                Pembayaran::create([
                    'reservasi_id' => $reservasi->id,
                    'jenis_pembayaran' => $validated['jenis_pembayaran'],
                    'metode' => $validated['metode'],
                    'jumlah_bayar' => $validated['jumlah_bayar'],
                    'bukti_pembayaran' => $pathBukti,
                    'status_pembayaran' => 'Pending',
                    'tanggal_bayar' => now(),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (QueryException $e) {
            return back()->withInput()
                ->withErrors(['jam_list' => 'Slot baru saja dipesan orang lain. Silakan pilih ulang.']);
        }
 
        return redirect()->route('dashboard')
            ->with('success', 'Reservasi & pembayaran berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * Fragment detail transaksi (dipanggil via fetch dari kartu jadwal terdekat).
     * Hanya bisa diakses oleh pemilik reservasi.
     */
    public function show(Reservasi $reservasi): View
    {
        abort_unless($reservasi->user_id === auth()->id(), 403);
 
        $reservasi->load(['lapangan', 'pembayaran' => function ($q) {
            $q->orderByDesc('tanggal_bayar');
        }]);
 
        return view('Pelanggan.dashboard._detail-transaksi-content', compact('reservasi'));
    }

    /**
     * Pelanggan membatalkan reservasi miliknya sendiri (selama masih Pending/Dikonfirmasi).
     */
    public function cancel(Reservasi $reservasi): RedirectResponse
    {
        abort_unless($reservasi->user_id === auth()->id(), 403);

        if (! in_array($reservasi->status_reservasi, ['Pending', 'Dikonfirmasi'])) {
            return back()->withErrors(['reservasi' => 'Reservasi ini sudah tidak bisa dibatalkan.']);
        }

        $reservasi->update([
            'status_reservasi' => 'Dibatalkan',
            'alasan_pembatalan' => 'Dibatalkan oleh pelanggan.',
        ]);

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}