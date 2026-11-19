<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Services\FileService;
use App\Services\PenjualanService;
use App\Services\RoleNotificationService;
use App\Support\ActivityLogger;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class MasterItemController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $kategori = trim((string) $request->input('kategori', ''));
        $view = (string) $request->input('view', 'cards');
        if (! in_array($view, ['cards', 'table'], true)) {
            $view = 'cards';
        }

        $stats = [
            'total_barang' => Barang::count(),
            'jumlah_kategori' => KategoriBarang::whereHas('barang')->count(),
            'nilai_stok' => (int) (Barang::selectRaw('COALESCE(SUM(stok * harga_beli), 0) as total')->value('total') ?? 0),
            'stok_menipis' => Barang::stokMenipis()->count(),
            'stok_aman' => Barang::whereColumn('stok', '>', 'stok_minimum')->count(),
        ];

        $kategoriList = KategoriBarang::whereHas('barang')->orderBy('nama')->get();
        $allKategori = KategoriBarang::orderBy('nama')->pluck('nama')->all();

        $barangPaginated = Barang::with('kategori')
            ->when($search !== '', function ($q) use ($search): void {
                $term = '%' . $search . '%';
                $q->where(fn ($sub) => $sub->where('nama_barang', 'like', $term)->orWhere('kode_barang', 'like', $term));
            })
            ->when($kategori !== '', function ($q) use ($kategori): void {
                $q->whereHas('kategori', fn ($sub) => $sub->where('slug', $kategori)->orWhere('nama', $kategori));
            })
            ->when($request->filled('kategori_id'), fn ($q) => $q->where('kategori_id', $request->integer('kategori_id')))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        try {
            $next = DB::selectOne('SELECT fn_kode_barang_berikutnya() AS kode');
            $kodeBerikutnya = $next?->kode ?? ('BRG-' . str_pad((string) (Barang::count() + 1), 3, '0', STR_PAD_LEFT));
        } catch (Throwable) {
            $kodeBerikutnya = 'BRG-' . str_pad((string) (Barang::count() + 1), 3, '0', STR_PAD_LEFT);
        }

        $items = $barangPaginated->getCollection();
        $ringkasan = (object) $stats;
        $stokMenipis = Barang::stokMenipis()->get();

        if ($request->wantsJson()) {
            return response()->json(compact(
                'items',
                'ringkasan',
                'stokMenipis',
                'kategoriList',
                'kodeBerikutnya',
                'stats'
            ));
        }

        return view('shared.master-item.index', [
            'barangPaginated' => $barangPaginated,
            'kategoriList' => $kategoriList,
            'stats' => $stats,
            'allKategori' => $allKategori,
            'search' => $search,
            'kategori' => $kategori,
            'view' => $view,
            'kodeBerikutnya' => $kodeBerikutnya,
            'items' => $items,
            'ringkasan' => $ringkasan,
            'stokMenipis' => $stokMenipis,
        ]);
    }

    public function store(Request $request, PenjualanService $penjualanService, FileService $fileService): JsonResponse|RedirectResponse
    {
        if ($request->filled('kategori') && ! $request->filled('kategori_nama')) {
            $request->merge(['kategori_nama' => $request->input('kategori')]);
        }

        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:20', 'unique:barang,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_nama' => ['required', 'string', 'max:80'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'stok_awal' => ['nullable', 'integer', 'min:0'],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_nama.required' => 'Kategori barang wajib dipilih atau diketik.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
        ]);

        try {
            $fotoPath = $request->hasFile('foto')
                ? $fileService->simpanFotoBarang($request->file('foto'))
                : null;

            $hasil = $penjualanService->tambahBarang(
                kodeBarang: trim($validated['kode_barang']),
                kategori: trim($validated['kategori_nama']),
                namaBarang: trim($validated['nama_barang']),
                satuan: trim($validated['satuan']),
                hargaBeli: (int) $validated['harga_beli'],
                hargaJual: (int) $validated['harga_jual'],
                stokAwal: (int) ($validated['stok_awal'] ?? 0),
                stokMinimum: (int) ($validated['stok_minimum'] ?? 10),
                foto: $fotoPath,
                userId: (int) Auth::id()
            );
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat menyimpan barang: ' . $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan barang: ' . $e->getMessage());
        }

        $pesan = 'Barang ' . trim($validated['nama_barang']) . ' berhasil ditambahkan.';

        try {
            $notif = app(RoleNotificationService::class);
            $notif->syncForRole('owner');
            $notif->syncForRole('kasir');
        } catch (Throwable) {
            // Sinkronisasi notifikasi aman
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $pesan,
                'data' => $hasil,
            ], 201);
        }

        return back()->with('success', $pesan);
    }

    public function restock(Request $request, int $id, PenjualanService $penjualanService, FileService $fileService): JsonResponse|RedirectResponse
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'kode_barang' => ['nullable', 'string', 'max:20', 'unique:barang,kode_barang,' . $barang->id],
            'nama_barang' => ['nullable', 'string', 'max:150'],
            'kategori_nama' => ['nullable', 'string', 'max:80'],
            'satuan' => ['nullable', 'string', 'max:30'],
            'harga_beli' => ['nullable', 'integer', 'min:0'],
            'harga_jual' => ['nullable', 'integer', 'min:0'],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
            'tambah_stok' => ['nullable', 'integer', 'min:0'],
            'jumlah_tambah' => ['nullable', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        try {
            if ($request->hasFile('foto')) {
                $barang->foto = $fileService->simpanFotoBarang($request->file('foto'));
            }

            if (! empty($validated['kategori_nama'])) {
                $kategori = KategoriBarang::firstOrCreate(
                    ['nama' => trim($validated['kategori_nama'])],
                    ['slug' => Str::slug($validated['kategori_nama'])]
                );
                $barang->kategori_id = $kategori->id;
            }

            $jumlahTambah = (int) ($validated['tambah_stok'] ?? $validated['jumlah_tambah'] ?? 0);
            $hargaBeli = isset($validated['harga_beli']) ? (int) $validated['harga_beli'] : (int) $barang->harga_beli;
            $hargaJual = isset($validated['harga_jual']) ? (int) $validated['harga_jual'] : (int) $barang->harga_jual;
            $stokMin = isset($validated['stok_minimum']) ? (int) $validated['stok_minimum'] : (int) $barang->stok_minimum;

            if ($jumlahTambah > 0) {
                $penjualanService->restockBarang(
                    barangId: $barang->id,
                    jumlahTambah: $jumlahTambah,
                    hargaBeli: $hargaBeli,
                    hargaJual: $hargaJual,
                    stokMinimum: $stokMin,
                    userId: (int) Auth::id()
                );
                $barang->refresh();
            }

            if (! empty($validated['kode_barang'])) {
                $barang->kode_barang = trim($validated['kode_barang']);
            }
            if (! empty($validated['nama_barang'])) {
                $barang->nama_barang = trim($validated['nama_barang']);
            }
            if (! empty($validated['satuan'])) {
                $barang->satuan = trim($validated['satuan']);
            }
            $barang->harga_beli = $hargaBeli;
            $barang->harga_jual = $hargaJual;
            $barang->stok_minimum = $stokMin;
            $barang->save();

            if ($jumlahTambah <= 0) {
                ActivityLogger::log(
                    'ubah',
                    'barang',
                    "Memperbarui informasi/harga barang {$barang->nama_barang}",
                    $barang->id,
                    null,
                    ['harga_beli' => $hargaBeli, 'harga_jual' => $hargaJual, 'stok_minimum' => $stokMin]
                );
            }
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat memperbarui barang: ' . $e->getMessage()], 422);
            }

            return back()->with('error', 'Terjadi kesalahan saat memperbarui barang: ' . $e->getMessage());
        }

        $pesan = 'Perubahan barang ' . $barang->nama_barang . ' berhasil disimpan.';

        try {
            $notif = app(RoleNotificationService::class);
            $notif->syncForRole('owner');
            $notif->syncForRole('kasir');
        } catch (Throwable) {
            // Sinkronisasi notifikasi aman
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $pesan,
                'data' => $barang,
            ]);
        }

        return back()->with('success', $pesan);
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $barang = Barang::findOrFail($id);
        $nama = $barang->nama_barang;
        $barang->delete();

        ActivityLogger::log('hapus', 'barang', "Menghapus barang '{$nama}' dari master item", $id);

        try {
            $notif = app(RoleNotificationService::class);
            $notif->syncForRole('owner');
            $notif->syncForRole('kasir');
        } catch (Throwable) {
        }

        $pesan = "Barang '{$nama}' berhasil dihapus dari master item.";

        if ($request->wantsJson()) {
            return response()->json(['message' => $pesan]);
        }

        return back()->with('success', $pesan);
    }
}
