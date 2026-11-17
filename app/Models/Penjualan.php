<?php

namespace App\Models;

use App\Enums\MetodeBayar;
use App\Enums\TipePenjualan;
use Database\Factories\PenjualanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penjualan extends Model
{
    /** @use HasFactory<PenjualanFactory> */
    use HasFactory;

    protected $table = 'penjualan';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'kode_transaksi',
        'tipe',
        'pelanggan_id',
        'nama_pelanggan',
        'metode_bayar',
        'total',
        'user_id',
        'waktu_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => TipePenjualan::class,
            'metode_bayar' => MetodeBayar::class,
            'total' => 'integer',
            'waktu_transaksi' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function bonPiutang(): HasOne
    {
        return $this->hasOne(BonPiutang::class, 'penjualan_id');
    }

    // ── Scope ──

    /**
     * Penjualan dengan tipe 'langsung'.
     */
    public function scopeLangsung(Builder $query): Builder
    {
        return $query->where('tipe', TipePenjualan::Langsung);
    }
}
