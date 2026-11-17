<?php

namespace App\Models;

use App\Enums\MetodeBayar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasLaci extends Model
{
    protected $table = 'kas_laci';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'tanggal',
        'waktu',
        'keterangan',
        'metode',
        'jumlah',
        'sumber_tipe',
        'sumber_id',
        'penutupan_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu' => 'datetime',
            'metode' => MetodeBayar::class,
            'jumlah' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function penutupanLaci(): BelongsTo
    {
        return $this->belongsTo(PenutupanLaci::class, 'penutupan_id');
    }

    // ── Scope ──

    /**
     * Kas yang masih aktif (belum ditutup/reset).
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereNull('penutupan_id');
    }
}
