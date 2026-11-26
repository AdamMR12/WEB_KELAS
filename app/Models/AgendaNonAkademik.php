<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgendaNonAkademik extends Model
{
    protected $table = 'agenda_non_akademik';
    protected $primaryKey = 'id_agenda';

    protected $fillable = [
        'nama_agenda',
        'deskripsi',
        'kategori',
        'status',
        'koordinator',
        'meta_label',
        'meta_value',
    ];

    public function jadwalAgenda(): HasMany
    {
        return $this->hasMany(JadwalAgenda::class, 'id_agenda', 'id_agenda');
    }
}
