<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengiriman extends Model
{
    protected $table = 'detail_pengiriman';

    public $timestamps = false;

    protected $fillable = [
        'pengiriman_id',
        'barang_id',
        'nama_barang',
        'satuan',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    // ── Relasi ──

    public function pengirimanBarang(): BelongsTo
    {
        return $this->belongsTo(PengirimanBarang::class, 'pengiriman_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
