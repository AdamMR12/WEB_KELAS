<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';

    protected $fillable = ['nim', 'nama', 'no_hp', 'status'];

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_anggota', 'id_anggota');
    }

    public function pengguna(): HasOne
    {
        return $this->hasOne(Pengguna::class, 'id_anggota', 'id_anggota');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    /** Pencarian berdasarkan nama atau NIM. */
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        $kata = trim((string) $kata);

        return $kata === ''
            ? $query
            : $query->where(fn (Builder $q) => $q
                ->where('nama', 'like', "%{$kata}%")
                ->orWhere('nim', 'like', "%{$kata}%"));
    }

    public function getInisialAttribute(): string
    {
        $kata = preg_split('/\s+/', trim($this->nama)) ?: [];

        return strtoupper(mb_substr($kata[0] ?? '?', 0, 1) . mb_substr($kata[1] ?? '', 0, 1));
    }

    /**
     * Status iuran dihitung (tidak disimpan): periode yang sudah jatuh tempo
     * dan belum dibayar anggota ini dianggap tunggakan.
     *
     * @return array{lunas: bool, tunggakan: int, periode_belum: \Illuminate\Support\Collection}
     */
    public function statusIuran(): array
    {
        $sudahBayar = $this->pembayaran()->pluck('id_periode');

        $belum = Periode::jatuhTempo()
            ->whereNotIn('id_periode', $sudahBayar)
            ->orderBy('jatuh_tempo')
            ->get();

        return [
            'lunas'         => $belum->isEmpty(),
            'tunggakan'     => (int) $belum->sum('nominal_iuran'),
            'periode_belum' => $belum,
        ];
    }
}
