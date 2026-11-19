<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kasir;

use App\Exports\RiwayatSaldoLaciExport;
use App\Http\Controllers\Controller;
use App\Models\KasLaci;
use App\Services\LaciService;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LaciController extends Controller
{
    public function index(Request $request, LaciService $laciService): View|JsonResponse
    {
        $saldo = $laciService->getSaldoAktif();
        $transaksiTerbaru = $laciService->getTransaksiTerbaru(10);
        $riwayat7Hari = $laciService->getRiwayat7Hari(7);

        $penjualanTunai = (int) KasLaci::query()
            ->aktif()
            ->where('metode', 'cash')
            ->where('sumber_tipe', 'penjualan')
            ->sum('jumlah');

        $pelunasanBonTunai = (int) KasLaci::query()
            ->aktif()
            ->where('metode', 'cash')
            ->where('sumber_tipe', 'pembayaran_bon')
            ->sum('jumlah');

        if ($request->wantsJson()) {
            return response()->json([
                'saldo' => $saldo,
                'transaksi_aktif' => $transaksiTerbaru,
                'riwayat_7_hari' => $riwayat7Hari,
                'penjualan_tunai' => $penjualanTunai,
                'pelunasan_bon_tunai' => $pelunasanBonTunai,
            ]);
        }

        return view('kasir.lihat-saldo', [
            'saldo' => $saldo,
            'transaksiTerbaru' => $transaksiTerbaru,
            'transaksiAktif' => $transaksiTerbaru,
            'riwayat7Hari' => $riwayat7Hari,
            'riwayat' => $riwayat7Hari,
            'penjualanTunai' => $penjualanTunai,
            'pelunasanBonTunai' => $pelunasanBonTunai,
        ]);
    }

    public function grafikMingguan(Request $request, LaciService $laciService): View|JsonResponse
    {
        $mode = (string) $request->input('mode', 'minggu_ini');
        if (! in_array($mode, ['minggu_ini', 'minggu_lalu', 'pilih_tanggal'], true)) {
            $mode = 'minggu_ini';
        }

        $customTanggal = $request->input('customTanggal', $request->input('tanggal', now()->toDateString()));
        $data = $laciService->getGrafikMingguan($mode, is_string($customTanggal) ? $customTanggal : null);

        if ($request->wantsJson()) {
            return response()->json([
                'mode' => $mode,
                'customTanggal' => $customTanggal,
                'data' => $data,
            ]);
        }

        return view('kasir.grafik-mingguan', [
            'mode' => $mode,
            'customTanggal' => $customTanggal,
            'data' => $data,
        ]);
    }

    public function riwayatSaldo(Request $request, LaciService $laciService): View|JsonResponse
    {
        $tanggalAwal = $request->filled('tanggalAwal') ? (string) $request->input('tanggalAwal') : null;
        $tanggalAkhir = $request->filled('tanggalAkhir') ? (string) $request->input('tanggalAkhir') : null;

        $riwayat = $laciService->getRiwayatSaldoLaci($tanggalAwal, $tanggalAkhir, 10)->withQueryString();

        $detailData = null;
        if ($request->filled('detail_id')) {
            $detailData = $laciService->getRincianPenutupan((int) $request->input('detail_id'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'riwayat' => $riwayat,
                'detail_data' => $detailData,
            ]);
        }

        return view('kasir.riwayat-saldo-laci', compact(
            'riwayat',
            'tanggalAwal',
            'tanggalAkhir',
            'detailData'
        ));
    }

    public function detailPenutupan(int $penutupanId, LaciService $laciService): JsonResponse
    {
        $detail = $laciService->getRincianPenutupan($penutupanId);
        abort_if($detail === null, 404, 'Rincian penutupan laci tidak ditemukan.');

        return response()->json($detail);
    }

    public function resetManual(Request $request, LaciService $laciService): JsonResponse|RedirectResponse
    {
        try {
            $hasil = $laciService->resetManual((int) Auth::id());
            $pesan = 'Saldo laci berhasil direset manual sebesar ' . rupiah($hasil['total']) . '.';

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $pesan,
                    'data' => $hasil,
                ]);
            }

            return back()->with('success', $pesan);
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal mereset laci: ' . $e->getMessage()], 422);
            }

            return back()->with('error', 'Gagal mereset laci: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $tanggalAwal = $request->filled('tanggalAwal') ? (string) $request->input('tanggalAwal') : null;
        $tanggalAkhir = $request->filled('tanggalAkhir') ? (string) $request->input('tanggalAkhir') : null;
        $namaFile = 'riwayat-saldo-laci-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new RiwayatSaldoLaciExport($tanggalAwal, $tanggalAkhir),
            $namaFile
        );
    }

    public function exportPdf(Request $request): Response
    {
        $tanggalAwal = $request->filled('tanggalAwal') ? (string) $request->input('tanggalAwal') : null;
        $tanggalAkhir = $request->filled('tanggalAkhir') ? (string) $request->input('tanggalAkhir') : null;

        $query = DB::table('v_riwayat_saldo_laci');

        if (! empty($tanggalAwal)) {
            $query->whereDate('tanggal', '>=', $tanggalAwal);
        }

        if (! empty($tanggalAkhir)) {
            $query->whereDate('tanggal', '<=', $tanggalAkhir);
        }

        $riwayat = $query->orderByDesc('penutupan_id')->get();

        $pdf = Pdf::loadView('exports.riwayat-saldo-laci-pdf', [
            'riwayat' => $riwayat,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ])->setPaper('a4', 'portrait');

        $namaFile = 'riwayat-saldo-laci-' . now()->format('Ymd-His') . '.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $namaFile,
            ['Content-Type' => 'application/pdf']
        );
    }
}
