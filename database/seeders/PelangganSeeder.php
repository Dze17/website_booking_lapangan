<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    
    public function run(): void
    {
        $pelanggan = [
            [
                'nama' => 'Andika Pratama',
                'email' => 'andika.pratama@gmail.com',
                'no_telepon' => '081234567891',
                'alamat' => 'Jl. Melati No. 12, Soreang, Kab. Bandung',
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@gmail.com',
                'no_telepon' => '081234567892',
                'alamat' => 'Jl. Kopo Permai No. 5, Kab. Bandung',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'no_telepon' => '081234567893',
                'alamat' => 'Jl. Cibaduyut Raya No. 20, Bandung',
            ],
            [
                'nama' => 'Rina Wulandari',
                'email' => 'rina.wulandari@gmail.com',
                'no_telepon' => '081234567894',
                'alamat' => 'Jl. Terusan Kopo No. 88, Bandung',
            ],
            [
                'nama' => 'Fajar Ramadhan',
                'email' => 'fajar.ramadhan@gmail.com',
                'no_telepon' => '081234567895',
                'alamat' => 'Jl. Sukamenak No. 3, Kab. Bandung',
            ],
        ];

        foreach ($pelanggan as $p) {
            User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'nama' => $p['nama'],
                    'no_telepon' => $p['no_telepon'],
                    'alamat' => $p['alamat'],
                    'password' => 'password123', // sama untuk semua akun dummy, biar gampang dites
                    'role' => 'pelanggan',
                ]
            );
        }
    }
}