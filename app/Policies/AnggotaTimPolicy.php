<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RolePengguna;
use App\Models\AnggotaTim;
use App\Models\User;

/**
 * Halaman Tim (F04) hanya dikelola admin.
 */
class AnggotaTimPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }

    public function create(User $actor): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }

    public function update(User $actor, AnggotaTim $anggota): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }

    public function delete(User $actor, AnggotaTim $anggota): bool
    {
        return $actor->hasRole(RolePengguna::Admin);
    }
}
