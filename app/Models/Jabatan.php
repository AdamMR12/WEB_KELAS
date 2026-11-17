<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    protected $primaryKey = 'ID_Jabatan';

    protected $fillable = [
        'Nama_Jabatan',
    ];

    public function pengurus(): HasMany
    {
        return $this->hasMany(Pengurus::class, 'ID_Jabatan', 'ID_Jabatan');
    }
}
