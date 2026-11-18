<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesan extends Model
{
    protected $table = 'pesan';
    protected $primaryKey = 'id_pesan';

    protected $fillable = ['isi_pesan', 'anonim'];

    protected $casts = [
        'anonim'   => 'boolean',
        'tgl_buat' => 'datetime',
    ];

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_pesan', 'id_pesan');
    }
}
