<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable implements CanResetPasswordContract
{

    use Notifiable, CanResetPassword;

    protected $table = 'Users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'email',
        'no_telepon',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPelanggan(): bool
    {
        return $this->role === 'pelanggan';
    }

    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'user_id');
    }

    public function reservasiDiinput(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'dibuat_oleh');
    }
}
