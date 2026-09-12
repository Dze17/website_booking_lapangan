<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    
    public function run(): void
    {
        $admins = [
            [
                'nama' => 'Dimas Aditya',
                'email' => 'admin@smsportcenter.com',
                'no_telepon' => '081200000001',
                'password' => 'password123',
            ],
            [
                'nama' => 'Rani Kasir',
                'email' => 'kasir@smsportcenter.com',
                'no_telepon' => '081200000002',
                'password' => 'password123',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'nama' => $admin['nama'],
                    'no_telepon' => $admin['no_telepon'],
                    'password' => $admin['password'], // otomatis ter-hash lewat cast di model User
                    'role' => 'admin',
                ]
            );
        }
    }
}