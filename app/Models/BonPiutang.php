<?php

namespace App\Models;

use App\Enums\StatusBayar;
use Database\Factories\BonPiutangFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonPiutang extends Model
{
    /** @use HasFactory<BonPiutangFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'bon_piutang';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'kode_bon',
        'penjualan_id',
        'pelanggan_id',
        'alamat_pengantaran',
        'tanggal_bon',
        'jatuh_tempo',
        'total_tagihan',
        'total_terbayar',
        'status_bayar',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bon' => 'date',
            'jatuh_tempo' => 'date',
            'total_tagihan' => 'integer',
            'total_terbayar' => 'integer',
            'status_bayar' => StatusBayar::class,
            'created_at' => 'datetime',
        ];
    }

    // ── Accessor ──

    /**
     * Sisa tagihan = total_tagihan − total_terbayar.
     */
    protected function sisa(): Attribute
    {
        return Attribute::get(
            fn () => $this->total_tagihan - $this->total_terbayar
        );
    }

    // ── Relasi ──

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function pembuatBon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranBon::class, 'bon_id');
    }

    public function pengiriman(): HasMany
    {
        return $this->hasMany(PengirimanBarang::class, 'bon_id');
    }

    public function logStatusPengiriman(): HasMany
    {
        return $this->hasMany(LogStatusPengiriman::class, 'bon_id');
    }

    // ── Scope ──

    /**
     * Bon yang belum lunas.
     */
    public function scopeBelumLunas(Builder $query): Builder
    {
        return $query->where('status_bayar', StatusBayar::BelumLunas);
    }

    /**
     * Bon yang sudah lewat jatuh tempo (belum lunas & jatuh_tempo < hari ini).
     */
    public function scopeLewatTempo(Builder $query): Builder
    {
        return $query->belumLunas()->where('jatuh_tempo', '<', now()->toDateString());
    }
}
