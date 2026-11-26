<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';

    protected $fillable = [
        'nip',
        'nama',
    ];

    public function jadwalMk(): HasMany
    {
        return $this->hasMany(JadwalMk::class, 'id_dosen', 'id_dosen');
    }
}
