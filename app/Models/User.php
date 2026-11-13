<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const LEVEL_ADMIN = 1;
    public const LEVEL_USER  = 2;

    public const LEVEL_LABELS = [
        self::LEVEL_ADMIN => 'Admin',
        self::LEVEL_USER  => 'User',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'level', 'phone', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'level'             => 'integer',
        'is_active'         => 'boolean',
    ];

    public function isAdmin(): bool { return $this->level === self::LEVEL_ADMIN; }
    public function isUser(): bool  { return $this->level === self::LEVEL_USER; }

    public function hasLevel(int|array $levels): bool
    {
        $levels = is_array($levels) ? $levels : [$levels];
        return in_array($this->level, $levels, true);
    }

    public function getLevelLabelAttribute(): string
    {
        return self::LEVEL_LABELS[$this->level] ?? 'Unknown';
    }

    public function dashboardRoute(): string
    {
        return match ($this->level) {
            self::LEVEL_ADMIN => 'dashboard.admin',
            self::LEVEL_USER  => 'dashboard.user',
            default           => 'home',
        };
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
}