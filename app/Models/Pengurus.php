<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengurus extends Model
{
    protected $table = 'pengurus';

    protected $primaryKey = 'NIM';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'NIM',
        'ID_Jabatan',
        'No_WhatsApp',
        'Email',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'NIM', 'NIM');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'ID_Jabatan', 'ID_Jabatan');
    }
}
