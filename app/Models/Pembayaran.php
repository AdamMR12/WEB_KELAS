<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = ['id_anggota', 'id_periode', 'id_user', 'tanggal_bayar', 'nominal'];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
            'nominal'       => 'integer',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
    }

    /** Bendahara/admin yang mencatat pembayaran ini. */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_user', 'id_user');
    }
}
