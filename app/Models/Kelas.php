<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $primaryKey = 'ID_Kelas';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ID_Kelas',
        'Nama_Kelas',
        'Program_Studi',
        'Angkatan',
    ];

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'ID_Kelas', 'ID_Kelas');
    }
}
