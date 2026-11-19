<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use App\Services\ActivityLogService;
use Throwable;

final class ActivityLogger
{
    /**
     * Mencatat jejak audit ke activity_logs secara aman (try-catch).
     *
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    public static function log(
        string $aksi,
        string $modul,
        string $deskripsi,
        ?int $targetId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        ?User $user = null,
    ): void {
        try {
            app(ActivityLogService::class)->catat(
                aksi: $aksi,
                modul: $modul,
                deskripsi: $deskripsi,
                user: $user,
                targetId: $targetId,
                dataLama: $dataLama,
                dataBaru: $dataBaru,
            );
        } catch (Throwable) {
            // Pencatatan audit log tidak boleh menggagalkan transaksi utama
        }
    }
}
