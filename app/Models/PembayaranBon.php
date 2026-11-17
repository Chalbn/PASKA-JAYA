<?php

namespace App\Models;

use App\Enums\MetodeBayar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranBon extends Model
{
    protected $table = 'pembayaran_bon';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'bon_id',
        'jumlah',
        'metode',
        'waktu_bayar',
        'user_id',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'metode' => MetodeBayar::class,
            'waktu_bayar' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function bonPiutang(): BelongsTo
    {
        return $this->belongsTo(BonPiutang::class, 'bon_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
