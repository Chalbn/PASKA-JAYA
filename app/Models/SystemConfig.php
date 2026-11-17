<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemConfig extends Model
{
    protected $table = 'system_configs';

    public $timestamps = false;

    protected $fillable = [
        'kunci',
        'nilai',
        'deskripsi',
        'updated_by',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Helper ──

    /**
     * Ambil nilai konfigurasi berdasarkan kunci.
     */
    public static function nilai(string $kunci, ?string $default = null): ?string
    {
        return static::where('kunci', $kunci)->value('nilai') ?? $default;
    }
}
