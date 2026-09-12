<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lapangan extends Model
{
    protected $table = 'lapangan';

    protected $fillable = [
        'kode_lapangan',
        'nama_lapangan',
        'jenis_lapangan',
        'tipe_lantai',
        'lokasi',
        'fasilitas',
        'gambar',
        'harga_per_jam',
        'jam_buka',
        'jam_tutup',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_jam' => 'decimal:2',
            'fasilitas' => 'array',
        ];
    }

    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'lapangan_id');
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}