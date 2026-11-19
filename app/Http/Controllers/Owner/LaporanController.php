<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function barangKeluar(Request $request): View|JsonResponse
    {
        $validated = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
            'metode' => ['nullable', 'in:semua,cash,transfer'],
        ]);

        $reportConn = $this->reportConnection();

        $sets = $this->callMultiResultProcedure($reportConn, 'CALL sp_laporan_penjualan(?, ?, ?)', [
            $validated['dari'] ?? null,
            $validated['sampai'] ?? null,
            $validated['metode'] ?? 'semua',
        ]);

        $ringkasan = $sets[0][0] ?? null;
        $daftar = $sets[1] ?? [];

        if ($request->wantsJson()) {
            return response()->json(compact('ringkasan', 'daftar'));
        }

        return view('owner.laporan-barang-keluar', compact('ringkasan', 'daftar'));
    }

    public function daftarBon(Request $request): View|JsonResponse
    {
        $bon = DB::table('v_daftar_bon')
            ->when($request->filled('status_bayar'), fn ($q) => $q->where('status_bayar', $request->string('status_bayar')))
            ->orderByDesc('created_at')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['bon' => $bon]);
        }

        return view('owner.daftar-bon', compact('bon'));
    }

    public function detailBon(int $id): JsonResponse
    {
        $sets = $this->callMultiResultProcedure($this->reportConnection(), 'CALL sp_detail_bon(?)', [$id]);

        abort_if(empty($sets[0]), 404, 'Bon tidak ditemukan.');

        return response()->json([
            'bon' => $sets[0][0],
            'items' => $sets[1] ?? [],
            'log_pengiriman' => $sets[2] ?? [],
            'pembayaran' => $sets[3] ?? [],
        ]);
    }

    public function riwayatLaci(Request $request): View|JsonResponse
    {
        $riwayat = DB::connection($this->reportConnection())
            ->table('v_riwayat_saldo_laci')
            ->orderByDesc('waktu_reset')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['riwayat' => $riwayat]);
        }

        return view('owner.riwayat-laci', compact('riwayat'));
    }

    public function kehadiran(Request $request): View|JsonResponse
    {
        $kehadiran = DB::table('v_kehadiran_karyawan')
            ->when($request->filled('tanggal'), fn ($q) => $q->where('tanggal', $request->string('tanggal')))
            ->orderByDesc('tanggal')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['kehadiran' => $kehadiran]);
        }

        return view('owner.kehadiran', compact('kehadiran'));
    }
}
