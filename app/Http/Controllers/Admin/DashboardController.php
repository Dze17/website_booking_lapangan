<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Jam yang ditampilkan di papan jadwal (jam sibuk sore-malam). */
    protected array $jamOperasional = [18, 19, 20, 21, 22, 23];

    public function index(): View
    {
        $today = Carbon::today();
        $kemarin = $today->copy()->subDay();

        // --- KPI 1: Pendapatan hari ini + delta vs kemarin ---
        $pendapatanHariIni = $this->totalPendapatan($today);
        $pendapatanKemarin = $this->totalPendapatan($kemarin);
        $deltaPendapatan = $this->hitungDelta($pendapatanHariIni, $pendapatanKemarin);

        // --- KPI 2: Total pemesanan hari ini, breakdown Futsal/Badminton ---
        $reservasiAktifHariIni = Reservasi::whereDate('tanggal_main', $today)
            ->where('status_reservasi', '!=', 'Dibatalkan');

        $reservasiHariIni = (clone $reservasiAktifHariIni)->count();
        $futsalHariIni = (clone $reservasiAktifHariIni)
            ->whereHas('lapangan', fn ($q) => $q->where('jenis_lapangan', 'Futsal'))->count();
        $badmintonHariIni = (clone $reservasiAktifHariIni)
            ->whereHas('lapangan', fn ($q) => $q->where('jenis_lapangan', 'Badminton'))->count();

        // --- KPI 3: Okupansi + jam ramai ---
        $lapanganAktif = Lapangan::whereIn('status', ['Aktif', 'Maintenance'])
            ->orderBy('jenis_lapangan')->get();

        $totalSlotHariIni = Lapangan::where('status', 'Aktif')->count() * count($this->jamOperasional);
        $okupansi = $totalSlotHariIni > 0
            ? (int) round(($reservasiHariIni / $totalSlotHariIni) * 100)
            : 0;

        $jamRamai = $this->cariJamRamai($today);

        // --- KPI 4: Pengguna baru hari ini ---
        $penggunaBaruHariIni = User::where('role', 'pelanggan')
            ->whereDate('created_at', $today)
            ->count();

        // --- Papan status lapangan realtime ---
        $jadwal = $this->buildJadwalHariIni($lapanganAktif, $today);

        // --- Transaksi/pembayaran terbaru ---
        $transaksiTerbaru = Pembayaran::with(['reservasi.lapangan', 'reservasi.pelanggan'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard.main', [
            'today' => $today,
            'pendapatanHariIni' => $pendapatanHariIni,
            'deltaPendapatan' => $deltaPendapatan,
            'reservasiHariIni' => $reservasiHariIni,
            'futsalHariIni' => $futsalHariIni,
            'badmintonHariIni' => $badmintonHariIni,
            'okupansi' => $okupansi,
            'jamRamai' => $jamRamai,
            'penggunaBaruHariIni' => $penggunaBaruHariIni,
            'jamOperasional' => $this->jamOperasional,
            'jadwal' => $jadwal,
            'transaksiTerbaru' => $transaksiTerbaru,
        ]);
    }

    protected function totalPendapatan(Carbon $tanggal): float
    {
        return (float) Pembayaran::whereHas('reservasi', function ($q) use ($tanggal) {
                $q->whereDate('tanggal_main', $tanggal);
            })
            ->where('status_pembayaran', 'Verified')
            ->sum('jumlah_bayar');
    }

    protected function hitungDelta(float $sekarang, float $sebelumnya): ?int
    {
        if ($sebelumnya <= 0) {
            return $sekarang > 0 ? 100 : null;
        }

        return (int) round((($sekarang - $sebelumnya) / $sebelumnya) * 100);
    }

    protected function cariJamRamai(Carbon $today): ?int
    {
        $terbanyak = Reservasi::whereDate('tanggal_main', $today)
            ->where('status_reservasi', '!=', 'Dibatalkan')
            ->get()
            ->groupBy(fn ($r) => (int) Carbon::parse($r->jam_mulai)->format('H'))
            ->sortByDesc(fn ($grup) => $grup->count())
            ->keys()
            ->first();

        return $terbanyak !== null ? (int) $terbanyak : null;
    }

    /**
     * Menyusun kartu status per lapangan x jam untuk papan realtime.
     * Jika status lapangan = "Maintenance", seluruh slotnya ditandai maintenance
     * (bukan per-jam, karena skema kita menyimpan status di level lapangan).
     */
    protected function buildJadwalHariIni($lapanganAktif, Carbon $today)
    {
        $reservasiHariIni = Reservasi::with('lapangan')
            ->whereDate('tanggal_main', $today)
            ->where('status_reservasi', '!=', 'Dibatalkan')
            ->get()
            ->keyBy(fn ($r) => $r->lapangan_id . '-' . (int) Carbon::parse($r->jam_mulai)->format('H'));

        $slots = collect();

        foreach ($lapanganAktif as $lapangan) {
            foreach ($this->jamOperasional as $jam) {
                $slots->push([
                    'lapangan' => $lapangan,
                    'jam' => $jam,
                    'reservasi' => $reservasiHariIni->get($lapangan->id . '-' . $jam),
                ]);
            }
        }

        return $slots->sortBy('jam')->values();
    }
}