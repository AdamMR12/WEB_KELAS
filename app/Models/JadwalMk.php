<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalMk extends Model
{
    protected $table = 'jadwal_mk';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_mk',
        'id_dosen',
        'id_ruangan',
        'hari',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'tipe_sks',
        'jenis',
        'warna_bg',
        'is_pengganti',
        'catatan_pekan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_pengganti' => 'boolean',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk', 'id_mk');
    }

    public function dosenPengampu(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    public function ruanganLokasi(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function getJamMulaiAttribute(?string $value): string
    {
        return $value ? substr($value, 0, 5) : '';
    }

    public function getJamSelesaiAttribute(?string $value): string
    {
        return $value ? substr($value, 0, 5) : '';
    }

    public function getNamaMatakuliahAttribute(): string
    {
        return $this->mataKuliah?->nama_mk ?? '-';
    }

    public function getSksAttribute(): int
    {
        return $this->mataKuliah?->sks ?? 0;
    }

    public function getDosenAttribute(): string
    {
        return $this->dosenPengampu?->nama ?? '-';
    }

    public function getRuangAttribute(): string
    {
        return $this->ruanganLokasi?->nama_ruangan ?? '-';
    }

    public function getMetodeAttribute(): string
    {
        return $this->tipe_sks ?? 'Praktikum Lab';
    }
}
