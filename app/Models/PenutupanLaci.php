<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenutupanLaci extends Model
{
    protected $table = 'penutupan_laci';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'tanggal',
        'total_cash',
        'total_transfer',
        'total',
        'waktu_reset',
        'direset_oleh',
        'tipe_reset',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_cash' => 'integer',
            'total_transfer' => 'integer',
            'total' => 'integer',
            'waktu_reset' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function userReset(): BelongsTo
    {
        return $this->belongsTo(User::class, 'direset_oleh');
    }

    public function kasLaci(): HasMany
    {
        return $this->hasMany(KasLaci::class, 'penutupan_id');
    }
}
