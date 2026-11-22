<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use App\Services\RoleNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class BarangKeluarController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $masterItems = Barang::query()->orderBy('nama_barang')->get();
        $riwayatPenjualan = Penjualan::query()
            ->langsung()
            ->with('detailPenjualan')
            ->latest('waktu_transaksi')
            ->latest('id')
            ->take(10)
            ->get();
        $totalRiwayat = Penjualan::query()->langsung()->count();

        $transaksi = collect();
        $barang = $masterItems->where('stok', '>', 0)->values();
        $kodeBerikutnya = null;

        try {
            $transaksi = DB::table('v_laporan_barang_keluar')
                ->orderByDesc('waktu_transaksi')
                ->limit(50)
                ->get();
            $kodeBerikutnya = DB::selectOne('SELECT fn_kode_transaksi_berikutnya() AS kode')?->kode;
        } catch (Throwable) {
            // Fallback bila view atau function MySQL belum tersedia
        }

        if ($request->wantsJson()) {
            return response()->json(compact(
                'transaksi',
                'barang',
                'kodeBerikutnya',
                'masterItems',
                'riwayatPenjualan',
                'totalRiwayat'
            ));
        }

        return view('kasir.barang-keluar', compact(
            'transaksi',
            'barang',
            'kodeBerikutnya',
            'masterItems',
            'riwayatPenjualan',
            'totalRiwayat'
        ));
    }

    public function store(Request $request, PenjualanService $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_pelanggan' => ['nullable', 'string', 'max:120'],
            'metode_bayar' => ['required', Rule::in(['cash', 'transfer'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'integer', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Keranjang masih kosong. Pilih barang terlebih dahulu.',
            'items.min' => 'Keranjang masih kosong. Pilih barang terlebih dahulu.',
        ]);

        try {
            $penjualan = $service->simpanLangsung([
                'items' => $validated['items'],
                'metode_bayar' => $validated['metode_bayar'],
                'nama_pelanggan' => trim((string) ($validated['nama_pelanggan'] ?? '')) ?: 'Umum',
            ], Auth::user());
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }

        try {
            $notif = app(RoleNotificationService::class);
            $notif->syncForRole('owner');
            $notif->syncForRole('kasir');
        } catch (Throwable) {
            // Sinkronisasi notifikasi tidak boleh mengganggu transaksi utama
        }

        $pesan = "Transaksi {$penjualan->kode_transaksi} berhasil disimpan.";

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $pesan,
                'data' => $penjualan,
            ], 201);
        }

        return redirect()->route('kasir.barang-keluar')->with('success', $pesan);
    }

    public function cetakNota(int $id): Response
    {
        $penjualan = Penjualan::query()
            ->with(['detailPenjualan', 'user', 'pelanggan'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('exports.nota-penjualan-pdf', [
            'penjualan' => $penjualan,
        ])->setPaper('a5', 'portrait');

        $namaFile = 'nota-' . ($penjualan->kode_transaksi ?: $penjualan->id) . '.pdf';

        return $pdf->stream($namaFile);
    }
}
