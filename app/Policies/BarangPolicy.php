<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RolePengguna;
use App\Models\Barang;
use App\Models\User;

class BarangPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat daftar barang.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RolePengguna::Owner)
            || $user->hasRole(RolePengguna::Kasir)
            || $user->hasRole(RolePengguna::Admin);
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail barang.
     */
    public function view(User $user, Barang $barang): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Tentukan apakah pengguna dapat menambah barang baru.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(RolePengguna::Owner)
            || $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Tentukan apakah pengguna dapat mengedit atau merestock barang.
     */
    public function update(User $user, Barang $barang): bool
    {
        return $user->hasRole(RolePengguna::Owner)
            || $user->hasRole(RolePengguna::Kasir);
    }

    /**
     * Tentukan apakah pengguna dapat menghapus barang (soft delete).
     */
    public function delete(User $user, Barang $barang): bool
    {
        return $user->hasRole(RolePengguna::Owner)
            || $user->hasRole(RolePengguna::Kasir);
    }
}
