<?php

namespace Database\Seeders;

use App\Models\Lapangan;
use Illuminate\Database\Seeder;

class LapanganSeeder extends Seeder
{
    
    public function run(): void
    {
        $lapangan = [
            ['kode_lapangan' => 'FLP-01', 'nama_lapangan' => 'Futsal A', 'jenis_lapangan' => 'Futsal', 'harga_per_jam' => 150000],
            ['kode_lapangan' => 'FLP-02', 'nama_lapangan' => 'Futsal B', 'jenis_lapangan' => 'Futsal', 'harga_per_jam' => 150000],
            ['kode_lapangan' => 'BDM-01', 'nama_lapangan' => 'Badminton 1', 'jenis_lapangan' => 'Badminton', 'harga_per_jam' => 60000],
            ['kode_lapangan' => 'BDM-02', 'nama_lapangan' => 'Badminton 2', 'jenis_lapangan' => 'Badminton', 'harga_per_jam' => 60000],
            ['kode_lapangan' => 'BDM-03', 'nama_lapangan' => 'Badminton 3', 'jenis_lapangan' => 'Badminton', 'harga_per_jam' => 60000],
        ];

        foreach ($lapangan as $l) {
            Lapangan::updateOrCreate(
                ['kode_lapangan' => $l['kode_lapangan']],
                array_merge($l, ['status' => 'Aktif'])
            );
        }
    }
}