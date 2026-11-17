<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Menulis jejak audit ke `activity_logs` (append-only, trigger T8).
 * Jangan pernah mengirim password/token ke `$dataLama` atau `$dataBaru`.
 */
class ActivityLogService
{
    /**
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    public function catat(
        string $aksi,
        string $modul,
        string $deskripsi,
        ?User $user = null,
        ?int $targetId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null,
    ): ActivityLog {
        $user ??= auth()->user();
        $request = request();

        return ActivityLog::create([
            'user_id' => $user?->id,
            'aksi' => $aksi,
            'modul' => $modul,
            'target_id' => $targetId,
            'deskripsi' => Str::limit($deskripsi, 255, ''),
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, '') ?: null,
        ]);
    }
}
