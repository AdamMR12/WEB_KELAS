<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $table = 'periode';
    protected $primaryKey = 'id_periode';

    protected $fillable = ['nama_periode', 'nominal_iuran', 'jatuh_tempo'];

    protected function casts(): array
    {
        return [
            'nominal_iuran' => 'integer',
            'jatuh_tempo'   => 'date',
        ];
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_periode', 'id_periode');
    }

    /** Periode yang sudah melewati (atau tepat pada) tanggal jatuh tempo. */
    public function scopeJatuhTempo(Builder $query): Builder
    {
        return $query->whereDate('jatuh_tempo', '<=', now()->toDateString());
    }
}
