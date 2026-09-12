<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $reservasiMendatang = Reservasi::where('user_id', $user->id)
            ->whereDate('tanggal_main', '>=', now()->toDateString())
            ->whereIn('status_reservasi', ['Pending', 'Dikonfirmasi'])
            ->with('lapangan')
            ->orderBy('tanggal_main')
            ->orderBy('jam_mulai')
            ->get();

        $totalJamMain = Reservasi::where('user_id', $user->id)
            ->where('status_reservasi', 'Selesai')
            ->get()
            ->sum(fn ($r) => Carbon::parse($r->jam_mulai)->diffInHours(Carbon::parse($r->jam_selesai)));

        $lapanganList = Lapangan::where('status', 'Aktif')
            ->orderBy('jenis_lapangan')
            ->get();

        return view('Pelanggan.dashboard.index', [
            'reservasiTerdekat' => $reservasiMendatang->first(),
            'jumlahMendatang' => $reservasiMendatang->count(),
            'totalJamMain' => $totalJamMain,
            'lapanganList' => $lapanganList,
        ]);
    }
}