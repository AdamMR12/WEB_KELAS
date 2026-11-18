<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $table = 'album';
    protected $primaryKey = 'id_album';

    protected $fillable = ['nama_album', 'tgl_acara'];

    protected $casts = [
        'tgl_acara' => 'date',
    ];

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'id_album', 'id_album');
    }
}
