<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\KasLaci;
use App\Models\Penjualan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BerandaController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $saldoTotal = (int) (KasLaci::query()->aktif()->sum('jumlah') ?? 0);
        $saldoCash = (int) (KasLaci::query()->aktif()->where('metode', 'cash')->sum('jumlah') ?? 0);
        $saldoTransfer = (int) (KasLaci::query()->aktif()->where('metode', 'transfer')->sum('jumlah') ?? 0);
        $transaksiTerbaru = KasLaci::query()->aktif()->latest('id')->take(5)->get();
        $transaksiHariIniCount = Penjualan::query()->langsung()->whereDate('waktu_transaksi', today())->count();
        $transaksiHariIniTotal = (int) (Penjualan::query()->langsung()->whereDate('waktu_transaksi', today())->sum('total') ?? 0);
        $stokMenipisCount = Barang::query()->stokMenipis()->count();
        $bonBelumLunasCount = BonPiutang::query()->belumLunas()->count();
        $bonBelumLunasSisa = (int) (BonPiutang::query()->belumLunas()->sum(
            DB::raw('CAST(total_tagihan AS SIGNED) - CAST(total_terbayar AS SIGNED)')
        ) ?? 0);

        if ($request->wantsJson()) {
            return response()->json(compact(
                'saldoTotal',
                'saldoCash',
                'saldoTransfer',
                'transaksiTerbaru',
                'transaksiHariIniCount',
                'transaksiHariIniTotal',
                'stokMenipisCount',
                'bonBelumLunasCount',
                'bonBelumLunasSisa'
            ));
        }

        return view('kasir.beranda', compact(
            'saldoTotal',
            'saldoCash',
            'saldoTransfer',
            'transaksiTerbaru',
            'transaksiHariIniCount',
            'transaksiHariIniTotal',
            'stokMenipisCount',
            'bonBelumLunasCount',
            'bonBelumLunasSisa'
        ));
    }

    public function resetManual(Request $request): RedirectResponse|JsonResponse
    {
        try {
            DB::select('CALL sp_reset_laci(?, ?)', ['manual', Auth::id()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Saldo laci kasir berhasil direset manual ke penutupan laci.',
                ]);
            }

            return back()->with('success', 'Saldo laci kasir berhasil direset manual ke penutupan laci.');
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Gagal mereset laci: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Gagal mereset laci: ' . $e->getMessage());
        }
    }
}
