<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogStatusPengiriman extends Model
{
    protected $table = 'log_status_pengiriman';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'bon_id',
        'pengiriman_id',
        'status',
        'user_id',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function bonPiutang(): BelongsTo
    {
        return $this->belongsTo(BonPiutang::class, 'bon_id');
    }

    public function pengirimanBarang(): BelongsTo
    {
        return $this->belongsTo(PengirimanBarang::class, 'pengiriman_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
