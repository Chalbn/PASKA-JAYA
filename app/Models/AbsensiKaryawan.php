<?php

namespace App\Models;

use Database\Factories\AbsensiKaryawanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbsensiKaryawan extends Model
{
    /** @use HasFactory<AbsensiKaryawanFactory> */
    use HasFactory;

    protected $table = 'absensi_karyawan';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'shift',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function detailPekerjaan(): HasMany
    {
        return $this->hasMany(DetailPekerjaanAbsensi::class, 'absensi_id');
    }
}
