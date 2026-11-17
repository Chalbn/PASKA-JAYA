<?php

namespace App\Policies;

use App\Enums\RolePengguna;
use App\Models\PenggunaanBahan;
use App\Models\User;

class PenggunaanBahanPolicy
{
    /**
     * Kasir dan Owner dapat melihat daftar penggunaan bahan.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RolePengguna::Kasir) || $user->hasRole(RolePengguna::Owner);
    }

    /**
     * Kasir dan Owner dapat melihat detail penggunaan bahan.
     */
    public function view(User $user, PenggunaanBahan $bahan): bool
    {
        return $user->hasRole(RolePengguna::Kasir) || $user->hasRole(RolePengguna::Owner);
    }

    /**
     * Hanya role Kasir yang boleh menambah/mencatat bahan harian.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Hanya role Kasir yang boleh mengubah catatan bahan harian.
     */
    public function update(User $user, PenggunaanBahan $bahan): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Hanya role Kasir yang boleh menghapus catatan bahan harian.
     */
    public function delete(User $user, PenggunaanBahan $bahan): bool
    {
        return $user->hasRole(RolePengguna::Kasir);
    }
}
