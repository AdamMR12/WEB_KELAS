<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'id_mk';

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'sks',
    ];

    protected $casts = [
        'sks' => 'integer',
    ];

    public function jadwalMk(): HasMany
    {
        return $this->hasMany(JadwalMk::class, 'id_mk', 'id_mk');
    }
}
