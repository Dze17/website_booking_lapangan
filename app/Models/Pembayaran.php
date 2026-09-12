<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'reservasi_id',
        'diverifikasi_oleh',
        'jenis_pembayaran',
        'metode',
        'jumlah_bayar',
        'bukti_pembayaran',
        'status_pembayaran',
        'catatan_verifikasi',
        'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_bayar' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function reservasi(): BelongsTo
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /** URL publik bukti transfer, atau null kalau tidak ada/cash. */
    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_pembayaran ? asset('storage/' . $this->bukti_pembayaran) : null;
    }
}