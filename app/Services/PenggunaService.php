<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Models\Role;
use App\Models\User;
use App\Services\Concerns\HandlesDatabaseExceptions;
use App\Support\NomorHp;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Operasi Manajemen Pengguna Admin (F03).
 *
 * Aturan yang dijaga di sini (selain Policy & trigger T7 sebagai pengaman terakhir):
 *  - admin tidak boleh mengubah role / menonaktifkan / menghapus akunnya sendiri;
 *  - admin aktif terakhir tidak boleh kehilangan statusnya;
 *  - setiap perubahan dicatat ke `activity_logs` tanpa menyimpan password.
 */
class PenggunaService
{
    use HandlesDatabaseExceptions;

    private const PESAN_ADMIN_TERAKHIR = 'Admin aktif terakhir tidak dapat diubah role-nya, dinonaktifkan, atau dihapus.';

    public function __construct(private readonly ActivityLogService $audit) {}

    /**
     * @param  array{nama: string, email: string, no_hp?: ?string, role: string, password: string}  $data
     *
     * @throws DomainException
     */
    public function buat(User $admin, array $data): User
    {
        $role = Role::query()->where('nama', $data['role'])->firstOrFail();
        $noHp = $this->normalisasiNoHp($data['no_hp'] ?? null);

        try {
            return $this->executeWithExceptionHandling(fn (): User => DB::transaction(function () use ($admin, $data, $role, $noHp): User {
                $user = User::create([
                    'role_id' => $role->id,
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'no_hp' => $noHp,
                    'password' => $data['password'],
                    'status' => StatusPengguna::Aktif,
                    // Akun dibuat langsung oleh admin sehingga tidak perlu verifikasi OTP.
                    'email_verified_at' => now(),
                ]);

                $this->audit->catat(
                    'tambah',
                    'pengguna',
                    "Menambah pengguna {$user->nama} ({$role->nama})",
                    $admin,
                    $user->id,
                    null,
                    ['nama' => $user->nama, 'email' => $user->email, 'no_hp' => $noHp, 'role' => $role->nama, 'status' => 'aktif'],
                );

                return $user;
            }));
        } catch (UniqueConstraintViolationException) {
            throw new DomainException('Email atau nomor HP sudah terdaftar.');
        }
    }

    /**
     * Perbarui data pengguna. Kunci `role` bersifat opsional; `password` kosong berarti tidak diubah.
     *
     * @param  array{nama: string, email: string, no_hp?: ?string, role?: ?string, password?: ?string}  $data
     *
     * @throws DomainException
     */
    public function perbarui(User $admin, User $target, array $data): User
    {
        $roleLama = (string) $target->role?->nama;
        $roleBaru = ! empty($data['role']) ? RolePengguna::from($data['role']) : null;
        $ubahRole = $roleBaru !== null && $roleBaru->value !== $roleLama;
        $passwordBaru = ! empty($data['password']) ? (string) $data['password'] : null;

        if ($ubahRole) {
            $this->pastikanBukanDiriSendiri($admin, $target, 'mengubah role');
            $this->pastikanBukanAdminTerakhir($target);
        }

        if ($passwordBaru !== null && $admin->is($target)) {
            throw new DomainException('Ubah password akun sendiri melalui halaman Profil.');
        }

        $noHp = $this->normalisasiNoHp($data['no_hp'] ?? null);

        $dataLama = ['nama' => $target->nama, 'email' => $target->email, 'no_hp' => $target->no_hp, 'role' => $roleLama];

        $atribut = ['nama' => $data['nama'], 'email' => $data['email'], 'no_hp' => $noHp];

        if ($ubahRole) {
            $atribut['role_id'] = Role::query()->where('nama', $roleBaru->value)->firstOrFail()->id;
        }

        if ($passwordBaru !== null) {
            $atribut['password'] = $passwordBaru;
        }

        try {
            $this->executeWithExceptionHandling(fn () => DB::transaction(function () use ($admin, $target, $atribut, $dataLama, $roleBaru, $ubahRole, $passwordBaru, $noHp): void {
                $target->update($atribut);

                $this->audit->catat(
                    'ubah',
                    'pengguna',
                    "Memperbarui pengguna {$target->nama}",
                    $admin,
                    $target->id,
                    $dataLama,
                    [
                        'nama' => $target->nama,
                        'email' => $target->email,
                        'no_hp' => $noHp,
                        'role' => $ubahRole ? $roleBaru->value : $dataLama['role'],
                        'password_diubah' => $passwordBaru !== null,
                    ],
                );
            }));
        } catch (UniqueConstraintViolationException) {
            throw new DomainException('Email atau nomor HP sudah terdaftar.');
        }

        return $target->unsetRelation('role');
    }

    /**
     * Ubah role langsung (dropdown pada tabel).
     *
     * @throws DomainException
     */
    public function ubahRole(User $admin, User $target, RolePengguna $roleBaru): void
    {
        $roleLama = (string) $target->role?->nama;

        if ($roleBaru->value === $roleLama) {
            return;
        }

        $this->pastikanBukanDiriSendiri($admin, $target, 'mengubah role');
        $this->pastikanBukanAdminTerakhir($target);

        $roleId = Role::query()->where('nama', $roleBaru->value)->firstOrFail()->id;

        $this->executeWithExceptionHandling(fn () => DB::transaction(function () use ($admin, $target, $roleId, $roleLama, $roleBaru): void {
            $target->update(['role_id' => $roleId]);

            $this->audit->catat(
                'ubah',
                'pengguna',
                "Mengubah role {$target->nama} dari {$roleLama} menjadi {$roleBaru->value}",
                $admin,
                $target->id,
                ['role' => $roleLama],
                ['role' => $roleBaru->value],
            );
        }));

        $target->unsetRelation('role');
    }

    /**
     * Aktifkan / nonaktifkan akun.
     *
     * @throws DomainException
     */
    public function ubahStatus(User $admin, User $target, StatusPengguna $statusBaru): void
    {
        if ($target->status === $statusBaru) {
            return;
        }

        if ($statusBaru === StatusPengguna::Nonaktif) {
            $this->pastikanBukanDiriSendiri($admin, $target, 'menonaktifkan');
            $this->pastikanBukanAdminTerakhir($target);
        }

        $statusLama = $target->status->value;

        $this->executeWithExceptionHandling(fn () => DB::transaction(function () use ($admin, $target, $statusBaru, $statusLama): void {
            $target->update(['status' => $statusBaru]);

            $this->audit->catat(
                'ubah',
                'pengguna',
                ($statusBaru === StatusPengguna::Aktif ? 'Mengaktifkan' : 'Menonaktifkan')." pengguna {$target->nama}",
                $admin,
                $target->id,
                ['status' => $statusLama],
                ['status' => $statusBaru->value],
            );
        }));
    }

    /**
     * Hapus akun (soft delete; riwayat transaksi tetap utuh).
     *
     * @throws DomainException
     */
    public function hapus(User $admin, User $target): void
    {
        $this->pastikanBukanDiriSendiri($admin, $target, 'menghapus');
        $this->pastikanBukanAdminTerakhir($target);

        $dataLama = ['nama' => $target->nama, 'email' => $target->email, 'role' => $target->role?->nama];

        $this->executeWithExceptionHandling(fn () => DB::transaction(function () use ($admin, $target, $dataLama): void {
            $this->audit->catat('hapus', 'pengguna', "Menghapus pengguna {$target->nama}", $admin, $target->id, $dataLama, null);

            $target->delete();
        }));
    }

    /**
     * True bila target adalah satu-satunya admin aktif.
     */
    public function adalahAdminAktifTerakhir(User $target): bool
    {
        if (! $target->hasRole(RolePengguna::Admin) || ! $target->isAktif()) {
            return false;
        }

        return ! User::query()
            ->whereHas('role', fn ($q) => $q->where('nama', RolePengguna::Admin->value))
            ->where('status', StatusPengguna::Aktif->value)
            ->whereKeyNot($target->getKey())
            ->exists();
    }

    private function pastikanBukanDiriSendiri(User $admin, User $target, string $aksi): void
    {
        if ($admin->is($target)) {
            throw new DomainException("Anda tidak dapat {$aksi} akun sendiri.");
        }
    }

    private function pastikanBukanAdminTerakhir(User $target): void
    {
        if ($this->adalahAdminAktifTerakhir($target)) {
            throw new DomainException(self::PESAN_ADMIN_TERAKHIR);
        }
    }

    private function normalisasiNoHp(?string $noHp): ?string
    {
        $noHp = trim((string) $noHp);

        return $noHp !== '' ? NomorHp::normalisasi($noHp) : null;
    }
}
