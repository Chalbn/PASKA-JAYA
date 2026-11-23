<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $reportConn = $this->reportConnection();

        $ringkasanToko = DB::connection($reportConn)->table('v_ringkasan_toko')->first();
        $penjualanHarian = DB::connection($reportConn)
            ->table('v_penjualan_harian')
            ->orderByDesc('tanggal')
            ->limit(14)
            ->get();

        $penjualanBulanIni = (int) DB::table('penjualan')
            ->where('tipe', 'langsung')
            ->whereYear('waktu_transaksi', now('Asia/Jakarta')->year)
            ->whereMonth('waktu_transaksi', now('Asia/Jakarta')->month)
            ->sum('total');

        $stokMenipis = DB::table('v_stok_menipis')->limit(10)->get();
        $piutangAktif = DB::table('v_piutang_aktif')->orderByDesc('lewat_tempo')->limit(10)->get();

        $mingguanSets = $this->callMultiResultProcedure($reportConn, 'CALL sp_penjualan_mingguan(?)', [
            $request->input('tanggal_mulai'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'ringkasan_toko' => $ringkasanToko,
                'penjualan_bulan_ini' => $penjualanBulanIni,
                'penjualan_harian' => $penjualanHarian,
                'grafik_mingguan' => $mingguanSets[0] ?? [],
                'ringkasan_mingguan' => $mingguanSets[1][0] ?? null,
                'stok_menipis' => $stokMenipis,
                'piutang_aktif' => $piutangAktif,
            ]);
        }

        return view('owner.dashboard', [
            'ringkasanToko' => $ringkasanToko,
            'penjualanBulanIni' => $penjualanBulanIni,
            'penjualanHarian' => $penjualanHarian,
            'grafikMingguan' => $mingguanSets[0] ?? [],
            'ringkasanMingguan' => $mingguanSets[1][0] ?? null,
            'stokMenipis' => $stokMenipis,
            'piutangAktif' => $piutangAktif,
        ]);
    }
}