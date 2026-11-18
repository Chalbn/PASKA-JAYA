<?php

namespace App\Models;

use Database\Factories\PenggunaanBahanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggunaanBahan extends Model
{
    /** @use HasFactory<PenggunaanBahanFactory> */
    use HasFactory;

    protected $table = 'penggunaan_bahan';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'tanggal',
        'nama_bahan',
        'jumlah',
        'satuan',
        'harga',
        'keterangan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
            'harga' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
