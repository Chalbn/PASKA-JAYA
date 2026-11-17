<?php

namespace App\Policies;

use App\Enums\RolePengguna;
use App\Models\PengirimanBarang;
use App\Models\User;

class PengirimanBarangPolicy
{
    /**
     * Karyawan hanya bisa melihat daftar pengiriman miliknya.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RolePengguna::Karyawan);
    }

    /**
     * Karyawan hanya bisa melihat detail pengiriman miliknya.
     * Owner dan Kasir juga boleh melihat (untuk sinkronisasi panel).
     */
    public function view(User $user, PengirimanBarang $pengiriman): bool
    {
        if ($user->hasRole(RolePengguna::Owner) || $user->hasRole(RolePengguna::Kasir)) {
            return true;
        }

        return $user->hasRole(RolePengguna::Karyawan)
            && (int) $pengiriman->karyawan_id === (int) $user->id;
    }

    /**
     * Karyawan pemilik bisa mengubah status pengiriman.
     */
    public function updateStatus(User $user, PengirimanBarang $pengiriman): bool
    {
        return $user->hasRole(RolePengguna::Karyawan)
            && (int) $pengiriman->karyawan_id === (int) $user->id;
    }

    /**
     * Akses foto bukti: owner, kasir, atau karyawan pemilik.
     */
    public function lihatBukti(User $user, PengirimanBarang $pengiriman): bool
    {
        if ($user->hasRole(RolePengguna::Owner) || $user->hasRole(RolePengguna::Kasir)) {
            return true;
        }

        return $user->hasRole(RolePengguna::Karyawan)
            && (int) $pengiriman->karyawan_id === (int) $user->id;
    }
}
