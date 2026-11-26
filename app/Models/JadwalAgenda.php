<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalAgenda extends Model
{
    protected $table = 'jadwal_agenda';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_agenda',
        'id_ruangan',
        'tanggal',
        'tanggal_selesai',
        'tanggal_display',
        'jam_mulai',
        'jam_selesai',
        'waktu_label',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function agendaNonAkademik(): BelongsTo
    {
        return $this->belongsTo(AgendaNonAkademik::class, 'id_agenda', 'id_agenda');
    }

    public function ruanganLokasi(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function getJudulAttribute(): string
    {
        return $this->agendaNonAkademik?->nama_agenda ?? '-';
    }

    public function getKategoriAttribute(): string
    {
        return $this->agendaNonAkademik?->kategori ?? 'ORGANISASI';
    }

    public function getStatusAttribute(): string
    {
        return $this->agendaNonAkademik?->status ?? 'Mendatang';
    }

    public function getDeskripsiAttribute(): ?string
    {
        return $this->agendaNonAkademik?->deskripsi;
    }

    public function getKoordinatorAttribute(): ?string
    {
        return $this->agendaNonAkademik?->koordinator;
    }

    public function getMetaLabelAttribute(): ?string
    {
        return $this->agendaNonAkademik?->meta_label;
    }

    public function getMetaValueAttribute(): ?string
    {
        return $this->agendaNonAkademik?->meta_value;
    }

    public function getLokasiAttribute(): string
    {
        return $this->ruanganLokasi?->nama_ruangan ?? 'Daring / Online';
    }

    public function getTanggalMulaiAttribute(): ?Carbon
    {
        return $this->tanggal;
    }

    public function getTanggalDisplayAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }
        if (!$this->tanggal) {
            return '-';
        }
        $mulai = Carbon::parse($this->tanggal)->locale('id');
        if ($this->tanggal_selesai) {
            $selesai = Carbon::parse($this->tanggal_selesai)->locale('id');
            return $mulai->translatedFormat('d') . ' - ' . $selesai->translatedFormat('d M Y');
        }
        return $mulai->translatedFormat('l, d M Y');
    }

    public function getWaktuAttribute(): string
    {
        if (!empty($this->waktu_label)) {
            return $this->waktu_label;
        }
        $start = $this->jam_mulai ? substr((string) $this->jam_mulai, 0, 5) : '00:00';
        $end = $this->jam_selesai ? ' - ' . substr((string) $this->jam_selesai, 0, 5) : '';
        return "{$start}{$end} WIB";
    }
}
