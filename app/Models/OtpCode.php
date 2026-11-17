<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $table = 'otp_codes';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'kode_hash',
        'kedaluwarsa_pada',
        'digunakan_pada',
    ];

    protected function casts(): array
    {
        return [
            'kedaluwarsa_pada' => 'datetime',
            'digunakan_pada' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
