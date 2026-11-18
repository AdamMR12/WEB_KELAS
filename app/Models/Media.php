<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'id_media';

    protected $fillable = ['id_album', 'file_path', 'caption'];

    protected $casts = [
        'tgl_upload' => 'datetime',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'id_album', 'id_album');
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_media', 'id_media');
    }
}
