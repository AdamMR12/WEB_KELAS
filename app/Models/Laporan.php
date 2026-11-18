<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DITINDAK = 'ditindak';
    public const STATUS_DITOLAK  = 'ditolak';

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = ['id_media', 'id_pesan', 'alasan', 'status'];

    protected $casts = [
        'tanggal_laporan' => 'datetime',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'id_media', 'id_media');
    }

    public function pesan(): BelongsTo
    {
        return $this->belongsTo(Pesan::class, 'id_pesan', 'id_pesan');
    }
}
