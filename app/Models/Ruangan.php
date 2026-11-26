<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    protected $fillable = [
        'nama_ruangan',
    ];

    public function jadwalMk(): HasMany
    {
        return $this->hasMany(JadwalMk::class, 'id_ruangan', 'id_ruangan');
    }

    public function jadwalAgenda(): HasMany
    {
        return $this->hasMany(JadwalAgenda::class, 'id_ruangan', 'id_ruangan');
    }
}
