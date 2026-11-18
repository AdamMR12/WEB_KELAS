<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $primaryKey = 'NIM';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'NIM',
        'Nama_Mahasiswa',
        'Tanggal_Lahir',
        'Foto',
        'ID_Kelas',
    ];

    protected $casts = [
    'Tanggal_Lahir' => 'date',
];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'ID_Kelas', 'ID_Kelas');
    }

    public function pengurus(): HasOne
    {
        return $this->hasOne(Pengurus::class, 'NIM', 'NIM');
    }
}
