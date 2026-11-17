<?php

namespace App\Models;

use App\Enums\StatusPengiriman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengirimanBarang extends Model
{
    protected $table = 'pengiriman_barang';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'bon_id',
        'karyawan_id',
        'status',
        'jadwal_kirim',
        'catatan_kasir',
        'foto_bukti',
        'dibaca_pada',
        'waktu_selesai',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPengiriman::class,
            'jadwal_kirim' => 'datetime',
            'dibaca_pada' => 'datetime',
            'waktu_selesai' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function bonPiutang(): BelongsTo
    {
        return $this->belongsTo(BonPiutang::class, 'bon_id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function pembuatPengiriman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function detailPengiriman(): HasMany
    {
        return $this->hasMany(DetailPengiriman::class, 'pengiriman_id');
    }

    public function logStatus(): HasMany
    {
        return $this->hasMany(LogStatusPengiriman::class, 'pengiriman_id');
    }
}
