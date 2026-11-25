<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Akun modul Kas Kelas (tabel `pengguna`).
 * Terpisah dari App\Models\User yang dipakai login bawaan kelompok lain.
 */
class Pengguna extends Authenticatable
{
    public const ROLE_ADMIN     = 'admin';
    public const ROLE_BENDAHARA = 'bendahara';
    public const ROLE_ANGGOTA   = 'anggota';

    protected $table = 'pengguna';
    protected $primaryKey = 'id_user';

    protected $fillable = ['username', 'password', 'role', 'id_anggota'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBendahara(): bool
    {
        return $this->role === self::ROLE_BENDAHARA;
    }

    /** Admin dan bendahara boleh mengubah data; anggota hanya melihat. */
    public function bisaKelola(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_BENDAHARA], true);
    }

    public function getNamaTampilAttribute(): string
    {
        return $this->anggota?->nama ?? $this->username;
    }

    public function getRoleLabelAttribute(): string
    {
        return ucfirst($this->role);
    }
}
