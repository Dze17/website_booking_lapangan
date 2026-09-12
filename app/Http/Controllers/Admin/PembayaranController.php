<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $periode = $this->periode($request);

        $query = $this->terapkanFilter(
            Pembayaran::with(['reservasi.lapangan', 'reservasi.pelanggan', 'verifikator']),
            $request,
            $periode
        );

        $transaksi = $query->orderByDesc('tanggal_bayar')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pembayaran.index', [
            'transaksi' => $transaksi,
            'ringkasan' => $this->ringkasan($periode),
            'filter' => array_merge($periode, $request->only(['cari', 'metode', 'status', 'jenis'])),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reservasi_id' => ['required', 'exists:reservasi,id'],
            'jenis_pembayaran' => ['required', 'in:DP,Pelunasan,Full'],
            'metode' => ['required', 'in:Cash,Transfer,E-Wallet,Payment Gateway'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'status_pembayaran' => ['required', 'in:Pending,Verified'],
        ], [
            'jumlah_bayar.min' => 'Jumlah bayar harus lebih dari 0.',
        ]);
 
        Pembayaran::create([
            'reservasi_id' => $validated['reservasi_id'],
            'diverifikasi_oleh' => $validated['status_pembayaran'] === 'Verified' ? auth()->id() : null,
            'jenis_pembayaran' => $validated['jenis_pembayaran'],
            'metode' => $validated['metode'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'status_pembayaran' => $validated['status_pembayaran'],
            'tanggal_bayar' => now(),
        ]);
 
        // Sama seperti verify(): pembayaran terverifikasi otomatis mengonfirmasi
        // reservasi yang masih Pending.
        $reservasi = Reservasi::find($validated['reservasi_id']);
        if ($validated['status_pembayaran'] === 'Verified' && $reservasi->status_reservasi === 'Pending') {
            $reservasi->update(['status_reservasi' => 'Dikonfirmasi']);
        }
 
        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Mengunduh transaksi (sesuai filter aktif) sebagai CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $periode = $this->periode($request);

        $data = $this->terapkanFilter(
            Pembayaran::with(['reservasi.lapangan', 'reservasi.pelanggan', 'verifikator']),
            $request,
            $periode
        )->orderBy('tanggal_bayar')->get();

        $namaFile = 'transaksi_smsportcenter_' . $periode['dari'] . '_' . $periode['sampai'] . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$namaFile}",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID Transaksi', 'Pelanggan', 'Lapangan', 'Tanggal Main', 'Jenis',
                'Metode', 'Jumlah Bayar', 'Tanggal Bayar', 'Status', 'Diverifikasi Oleh', 'Catatan',
            ]);

            foreach ($data as $t) {
                fputcsv($file, [
                    'SP-' . str_pad((string) $t->id, 5, '0', STR_PAD_LEFT),
                    $t->reservasi->nama_pemesan,
                    $t->reservasi->lapangan->nama_lapangan,
                    $t->reservasi->tanggal_main->format('Y-m-d'),
                    $t->jenis_pembayaran,
                    $t->metode,
                    $t->jumlah_bayar,
                    $t->tanggal_bayar?->format('Y-m-d H:i'),
                    $t->status_pembayaran,
                    $t->verifikator->nama ?? '-',
                    $t->catatan_verifikasi,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Memverifikasi pembayaran (status -> Verified).
     * Jika reservasi terkait masih Pending, otomatis ikut dikonfirmasi —
     * karena secara bisnis, pembayaran terverifikasi = booking sah.
     */
    public function verify(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $pembayaran->update([
            'status_pembayaran' => 'Verified',
            'diverifikasi_oleh' => auth()->id(),
            'catatan_verifikasi' => $request->input('catatan'),
        ]);

        if ($pembayaran->reservasi->status_reservasi === 'Pending') {
            $pembayaran->reservasi->update(['status_reservasi' => 'Dikonfirmasi']);
        }

        return back()->with('success', "Pembayaran #SP-" . str_pad((string) $pembayaran->id, 5, '0', STR_PAD_LEFT) . " diverifikasi.");
    }

    /**
     * Menolak pembayaran (status -> Rejected). Wajib menyertakan catatan/alasan.
     */
    public function reject(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:255'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pembayaran->update([
            'status_pembayaran' => 'Rejected',
            'diverifikasi_oleh' => auth()->id(),
            'catatan_verifikasi' => $request->input('catatan'),
        ]);

        return back()->with('success', "Pembayaran #SP-" . str_pad((string) $pembayaran->id, 5, '0', STR_PAD_LEFT) . " ditolak.");
    }

    /**
     * Rentang tanggal aktif (default: awal bulan ini s/d hari ini),
     * dipakai bersama oleh kartu ringkasan, tabel, dan export.
     */
    protected function periode(Request $request): array
    {
        return [
            'dari' => $request->input('dari', now()->startOfMonth()->toDateString()),
            'sampai' => $request->input('sampai', now()->toDateString()),
        ];
    }

    /**
     * Menerapkan seluruh filter (rentang tanggal, pencarian, metode, status, jenis)
     * ke query — dipakai bersama oleh index() dan export() supaya hasilnya konsisten.
     */
    protected function terapkanFilter($query, Request $request, array $periode)
    {
        $query->whereBetween('tanggal_bayar', [
            $periode['dari'] . ' 00:00:00',
            $periode['sampai'] . ' 23:59:59',
        ]);

        if ($request->filled('cari')) {
            $keyword = $request->input('cari');
            $query->whereHas('reservasi', function ($q) use ($keyword) {
                $q->where('nama_tamu', 'like', "%{$keyword}%")
                    ->orWhere('no_telepon_tamu', 'like', "%{$keyword}%")
                    ->orWhereHas('pelanggan', function ($q2) use ($keyword) {
                        $q2->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('no_telepon', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('metode')) {
            $query->where('metode', $request->input('metode'));
        }

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->input('status'));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pembayaran', $request->input('jenis'));
        }

        return $query;
    }

    /**
     * Menghitung angka-angka untuk kartu ringkasan di atas tabel.
     */
    protected function ringkasan(array $periode): array
    {
        $verifiedDalamPeriode = Pembayaran::whereBetween('tanggal_bayar', [
            $periode['dari'] . ' 00:00:00',
            $periode['sampai'] . ' 23:59:59',
        ])->where('status_pembayaran', 'Verified');

        $totalPendapatan = (clone $verifiedDalamPeriode)->sum('jumlah_bayar');
        $rataRata = (clone $verifiedDalamPeriode)->avg('jumlah_bayar');

        $breakdownMetode = (clone $verifiedDalamPeriode)
            ->selectRaw('metode, SUM(jumlah_bayar) as total')
            ->groupBy('metode')
            ->pluck('total', 'metode');

        // Menunggu verifikasi TIDAK dibatasi rentang tanggal — admin perlu lihat semua yang pending
        $pendingQuery = Pembayaran::where('status_pembayaran', 'Pending');

        return [
            'total_pendapatan' => (float) $totalPendapatan,
            'rata_rata_transaksi' => (float) ($rataRata ?? 0),
            'breakdown_metode' => $breakdownMetode,
            'menunggu_jumlah' => (clone $pendingQuery)->count(),
            'menunggu_nominal' => (float) (clone $pendingQuery)->sum('jumlah_bayar'),
        ];
    }
}