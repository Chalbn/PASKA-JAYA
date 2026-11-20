<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatusSistemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, StatusSistemService $statusSistem): View|JsonResponse
    {
        $kartu = $statusSistem->kartuRingkasan();
        $perRole = $statusSistem->penggunaPerRole();
        $status = $statusSistem->statusSistem();

        $ringkasan = null;
        $penggunaPerRole = collect();
        try {
            $ringkasan = DB::table('v_ringkasan_admin')->first();
            $penggunaPerRole = DB::table('v_pengguna_per_role')->orderBy('role_id')->get();
        } catch (\Throwable) {
            // Fallback ke StatusSistemService bila view database belum tersedia
        }

        $logRole = trim((string) $request->input('log_role', ''));
        $logModul = trim((string) $request->input('log_modul', ''));

        $logQuery = DB::table('activity_logs as al')
            ->leftJoin('users as u', 'u.id', '=', 'al.user_id')
            ->leftJoin('roles as r', 'r.id', '=', 'u.role_id')
            ->select([
                'al.id',
                'al.aksi',
                'al.modul',
                'al.deskripsi',
                'al.ip_address',
                'al.created_at',
                'u.nama as nama_user',
                'r.nama as role_nama',
            ])
            ->when($logRole !== '', fn ($q) => $q->where('r.nama', $logRole))
            ->when($logModul !== '', fn ($q) => $q->where('al.modul', $logModul))
            ->orderByDesc('al.id');

        $logAktivitas = $logQuery->paginate(10, ['*'], 'log_page')->withQueryString();
        $logTerbaru = $logAktivitas->getCollection();

        if ($request->wantsJson()) {
            return response()->json([
                'ringkasan' => $ringkasan ?? $kartu,
                'pengguna_per_role' => $penggunaPerRole->isNotEmpty() ? $penggunaPerRole : $perRole,
                'log_terbaru' => $logTerbaru,
                'kartu' => $kartu,
                'per_role' => $perRole,
                'status' => $status,
            ]);
        }

        return view('admin.dashboard', compact(
            'kartu',
            'perRole',
            'status',
            'ringkasan',
            'penggunaPerRole',
            'logTerbaru',
            'logAktivitas',
            'logRole',
            'logModul'
        ));
    }

    public function simpanKonfigurasi(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'kunci' => ['required', 'string', 'max:100'],
            'nilai' => ['required', 'string'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $lama = DB::table('system_configs')->where('kunci', $validated['kunci'])->first();

        DB::table('system_configs')->updateOrInsert(
            ['kunci' => $validated['kunci']],
            [
                'nilai' => $validated['nilai'],
                'deskripsi' => $validated['deskripsi'] ?? $lama?->deskripsi,
                'updated_by' => Auth::id(),
                'updated_at' => now('Asia/Jakarta'),
            ]
        );

        $this->catatAktivitas(
            (int) Auth::id(),
            'ubah',
            'sistem',
            null,
            "Memperbarui konfigurasi {$validated['kunci']}",
            $lama ? ['nilai' => $lama->nilai] : null,
            ['nilai' => $validated['nilai']]
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Konfigurasi sistem berhasil disimpan.']);
        }

        return back()->with('success', 'Konfigurasi sistem berhasil disimpan.');
    }
}
