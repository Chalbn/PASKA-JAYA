<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RolePengguna;
use App\Models\User;

/**
 * Otorisasi Manajemen Pengguna (F03) & Ringkasan Sistem (F02): hanya admin.
 * Aksi destruktif terhadap akun sendiri ditolak di sini (selain divalidasi di PenggunaService).
 */
class PenggunaPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }

    public function create(User $actor): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }

    /**
     * Edit data profil pengguna (akun sendiri boleh; role & password dijaga di service).
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }

    public function ubahRole(User $actor, User $target): bool
    {
        return $actor->hasRole(RolePengguna::Admin) && ! $actor->is($target);
    }

    public function ubahStatus(User $actor, User $target): bool
    {
        return $actor->hasRole(RolePengguna::Admin) && ! $actor->is($target);
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->hasRole(RolePengguna::Admin) && ! $actor->is($target);
    }
}
