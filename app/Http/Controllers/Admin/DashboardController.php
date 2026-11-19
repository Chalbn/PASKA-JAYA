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

        $logTerbaru = DB::table('activity_logs as al')
            ->leftJoin('users as u', 'u.id', '=', 'al.user_id')
            ->select(['al.id', 'al.aksi', 'al.modul', 'al.deskripsi', 'al.created_at', 'u.nama as nama_user'])
            ->orderByDesc('al.id')
            ->limit(15)
            ->get();

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
            'logTerbaru'
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
