<?php

namespace App\Models;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'role_id',
        'nama',
        'email',
        'no_hp',
        'password',
        'status',
        'alamat',
        'foto_profil',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPengguna::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    // ── Relasi ──

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'user_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiKaryawan::class, 'karyawan_id');
    }

    public function pengiriman(): HasMany
    {
        return $this->hasMany(PengirimanBarang::class, 'karyawan_id');
    }

    public function penggunaanBahan(): HasMany
    {
        return $this->hasMany(PenggunaanBahan::class, 'user_id');
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class, 'user_id');
    }

    // ── Helper ──

    /**
     * Cek apakah pengguna memiliki role tertentu.
     */
    public function hasRole(RolePengguna|string $role): bool
    {
        $nama = $role instanceof RolePengguna ? $role->value : $role;

        return $this->role?->nama === $nama;
    }

    /**
     * Cek apakah pengguna aktif.
     */
    public function isAktif(): bool
    {
        return $this->status === StatusPengguna::Aktif;
    }
}
