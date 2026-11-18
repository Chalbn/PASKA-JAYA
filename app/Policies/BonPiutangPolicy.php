<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RolePengguna;
use App\Models\BonPiutang;
use App\Models\User;

class BonPiutangPolicy
{
    /**
     * Owner (read-only) dan Kasir dapat melihat daftar bon.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RolePengguna::Owner)
            || $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Owner (read-only) dan Kasir dapat melihat detail bon.
     */
    public function view(User $user, BonPiutang $bon): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Hanya Kasir yang dapat membuat bon baru.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Hanya Kasir yang dapat mengelola pengiriman / pembayaran bon.
     */
    public function update(User $user, BonPiutang $bon): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Hanya Kasir yang dapat menghapus bon yang belum dikirim dan belum dibayar.
     */
    public function delete(User $user, BonPiutang $bon): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Hanya Kasir yang dapat mengirim bon ke karyawan.
     */
    public function kirim(User $user, BonPiutang $bon): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Hanya Kasir yang dapat mencatat pembayaran bon.
     */
    public function bayar(User $user, BonPiutang $bon): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }
}
