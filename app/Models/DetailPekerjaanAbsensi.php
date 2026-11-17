<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPekerjaanAbsensi extends Model
{
    protected $table = 'detail_pekerjaan_absensi';

    public $timestamps = false;

    protected $fillable = [
        'absensi_id',
        'deskripsi',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    // ── Relasi ──

    public function absensi(): BelongsTo
    {
        return $this->belongsTo(AbsensiKaryawan::class, 'absensi_id');
    }
}
