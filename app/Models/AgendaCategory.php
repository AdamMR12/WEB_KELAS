<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'warna_bg',
    ];

    public function getBadgeClassAttribute(): string
    {
        return match($this->warna_bg) {
            'amber' => 'bg-[#ffd8a8] text-[#5c3100]',
            'lime' => 'bg-[#d1f771] text-[#1f3300]',
            'cyan' => 'bg-[#c5f6fa] text-[#0c5460]',
            'rose' => 'bg-[#ffc9c9] text-[#5c0f0f]',
            'purple' => 'bg-[#e2d9f3] text-[#3b1f66]',
            'emerald' => 'bg-[#bbf7d0] text-[#064e3b]',
            'navy' => 'bg-[#1e1f37] text-white',
            default => 'bg-slate-200 text-slate-800',
        };
    }
}
