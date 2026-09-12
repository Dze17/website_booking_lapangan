<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'user_id',
        'lapangan_id',
        'dibuat_oleh',
        'nama_tamu',
        'no_telepon_tamu',
        'tanggal_main',
        'jam_mulai',
        'jam_selesai',
        'total_harga',
        'tipe_input',
        'status_reservasi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_main' => 'date',
            'total_harga' => 'decimal:2',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lapangan(): BelongsTo
    {
        return $this->belongsTo(Lapangan::class, 'lapangan_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'reservasi_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'reservasi_id');
    }

    /** Nama pemesan: pakai nama akun jika ada, kalau tidak pakai nama tamu (walk-in). */
    public function getNamaPemesanAttribute(): string
    {
        return $this->pelanggan?->name ?? $this->nama_tamu ?? '-';
    }
}