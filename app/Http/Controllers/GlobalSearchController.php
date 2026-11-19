<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatusBayar;
use App\Models\Barang;
use App\Models\BonPiutang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        $user = Auth::user();
        $user?->loadMissing('role');
        $role = strtolower((string) ($user?->role?->nama ?? ''));

        // 1. Cek Harga & Stok Barang Instan (Semua role)
        $barangQuery = Barang::query()->orderBy('nama_barang');
        if ($q !== '') {
            $pola = '%' . $q . '%';
            $barangQuery->where(function ($sub) use ($pola): void {
                $sub->where('nama_barang', 'like', $pola)
                    ->orWhere('kode_barang', 'like', $pola);
            });
        }

        $masterRouteName = match ($role) {
            'owner' => 'owner.master-item',
            'kasir' => 'kasir.master-item',
            default => null,
        };

        $barangResults = $barangQuery->take($q === '' ? 5 : 8)->get()->map(static function (Barang $b) use ($masterRouteName): array {
            $stokStatus = 'aman';
            if ((int) $b->stok <= 0) {
                $stokStatus = 'habis';
            } elseif ((int) $b->stok <= (int) $b->stok_minimum) {
                $stokStatus = 'menipis';
            }

            $searchParam = $b->kode_barang ?: $b->nama_barang;
            $url = ($masterRouteName && Route::has($masterRouteName))
                ? route($masterRouteName, ['search' => $searchParam])
                : null;

            return [
                'id' => $b->id,
                'kode' => $b->kode_barang ?: ('BRG-' . $b->id),
                'nama' => $b->nama_barang,
                'satuan' => $b->satuan,
                'harga_jual' => (int) $b->harga_jual,
                'harga_formatted' => rupiah((int) $b->harga_jual),
                'stok' => (int) $b->stok,
                'stok_minimum' => (int) $b->stok_minimum,
                'stok_status' => $stokStatus,
                'url' => $url,
            ];
        })->values();

        // 2. Pencarian Pelanggan & Status Bon (Owner, Kasir, Admin)
        $bonResults = collect();
        if (in_array($role, ['owner', 'kasir', 'admin'], true) && $q !== '') {
            $pola = '%' . $q . '%';
            $bonQuery = BonPiutang::query()
                ->with(['pelanggan', 'penjualan'])
                ->where(function ($sub) use ($pola): void {
                    $sub->where('kode_bon', 'like', $pola)
                        ->orWhereHas('pelanggan', fn ($pq) => $pq->where('nama', 'like', $pola))
                        ->orWhereHas('penjualan', fn ($sq) => $sq->where('nama_pelanggan', 'like', $pola));
                })
                ->orderByRaw("CASE WHEN status_bayar = 'belum_lunas' THEN 0 ELSE 1 END ASC")
                ->latest('id')
                ->take(6)
                ->get();

            $bonResults = $bonQuery->map(static function (BonPiutang $bon) use ($role): array {
                $kode = $bon->kode_bon ?: ('BON-' . $bon->id);
                $namaPelanggan = $bon->pelanggan?->nama ?? $bon->penjualan?->nama_pelanggan ?? 'Pelanggan';
                $sisa = max(0, (int) $bon->total_tagihan - (int) $bon->total_terbayar);
                $lunas = $bon->status_bayar === StatusBayar::Lunas;

                $url = match ($role) {
                    'owner' => Route::has('owner.bon.detail') ? route('owner.bon.detail', $bon->id) : '/owner/daftar-bon',
                    'kasir' => Route::has('kasir.daftar-bon') ? route('kasir.daftar-bon', ['search' => $kode]) : '/kasir/daftar-bon',
                    default => null,
                };

                return [
                    'id' => $bon->id,
                    'kode_bon' => $kode,
                    'nama_pelanggan' => $namaPelanggan,
                    'total_tagihan_formatted' => rupiah((int) $bon->total_tagihan),
                    'sisa_formatted' => rupiah($sisa),
                    'is_lunas' => $lunas,
                    'status_label' => $lunas ? 'Lunas' : 'Belum Lunas',
                    'jatuh_tempo' => $bon->jatuh_tempo?->format('d M Y') ?? '-',
                    'url' => $url,
                ];
            })->values();
        }

        return response()->json([
            'barang' => $barangResults,
            'bon' => $bonResults,
            'can_view_bon' => in_array($role, ['owner', 'kasir', 'admin'], true),
        ]);
    }
}
