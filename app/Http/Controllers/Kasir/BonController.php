<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kasir;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\User;
use App\Services\BonService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class BonController extends Controller
{
    public function create(): View
    {
        $masterItems = Barang::query()->orderBy('nama_barang')->get();

        return view('kasir.catat-bon', compact('masterItems'));
    }

    public function index(Request $request): View|JsonResponse
    {
        $filterStatus = (string) $request->input('filterStatus', $request->input('status_bayar', 'semua'));
        $search = trim((string) $request->input('search', ''));

        $query = BonPiutang::with(['pelanggan', 'penjualan.pelanggan', 'penjualan.detailPenjualan.masterItem', 'pengiriman.karyawan']);

        if (in_array($filterStatus, ['belum_lunas', 'lunas'], true)) {
            $query->where('status_bayar', $filterStatus);
        }

        if ($search !== '') {
            $pola = '%' . $search . '%';
            $query->where(function ($sub) use ($pola): void {
                $sub->where('kode_bon', 'like', $pola)
                    ->orWhereHas('pelanggan', fn ($q) => $q->where('nama', 'like', $pola))
                    ->orWhereHas('penjualan', fn ($q) => $q->where('nama_pelanggan', 'like', $pola));
            });
        }

        $daftarBon = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $daftarKaryawan = User::query()
            ->whereHas('role', fn ($q) => $q->where('nama', RolePengguna::Karyawan->value))
            ->where('status', StatusPengguna::Aktif->value)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $piutangAktif = collect();
        try {
            $piutangAktif = DB::table('v_piutang_aktif')->get();
        } catch (Throwable) {
            // Fallback bila view belum ada
        }

        if ($request->wantsJson()) {
            return response()->json([
                'daftarBon' => $daftarBon,
                'piutangAktif' => $piutangAktif,
            ]);
        }

        return view('kasir.daftar-bon', compact(
            'daftarBon',
            'piutangAktif',
            'filterStatus',
            'search',
            'daftarKaryawan'
        ));
    }

    public function store(Request $request, BonService $bonService): JsonResponse|RedirectResponse
    {
        // Normalisasi input dari form CatatBon maupun API
        if ($request->filled('tanggal') && ! $request->filled('tanggal_bon')) {
            $request->merge(['tanggal_bon' => $request->input('tanggal')]);
        }
        if ($request->filled('kontak') && ! $request->filled('no_hp')) {
            $request->merge(['no_hp' => $request->input('kontak')]);
        }

        $validated = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:120'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'tanggal_bon' => ['required', 'date'],
            'jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_bon'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['nullable', 'integer', 'exists:barang,id'],
            'items.*.master_item_id' => ['nullable', 'integer', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $bonId = DB::transaction(function () use ($validated): int {
                $pelangganId = (int) DB::table('pelanggan')->insertGetId([
                    'nama' => trim($validated['nama_pelanggan']),
                    'no_hp' => $validated['no_hp'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                ]);

                $normalizedItems = collect($validated['items'])->map(fn ($row) => [
                    'barang_id' => (int) ($row['barang_id'] ?? $row['master_item_id'] ?? 0),
                    'jumlah' => (int) $row['jumlah'],
                ])->all();

                $barangIds = collect($normalizedItems)->pluck('barang_id')->all();
                $master = DB::table('barang')
                    ->whereIn('id', $barangIds)
                    ->whereNull('deleted_at')
                    ->get()
                    ->keyBy('id');

                $total = 0;
                $barisDetail = [];

                foreach ($normalizedItems as $row) {
                    $item = $master->get($row['barang_id']);
                    abort_if($item === null, 422, 'Barang tidak ditemukan.');

                    $jumlah = (int) $row['jumlah'];
                    $harga = (int) $item->harga_jual;
                    $subtotal = $jumlah * $harga;
                    $total += $subtotal;

                    $barisDetail[] = [
                        'barang_id' => $item->id,
                        'nama_barang' => $item->nama_barang,
                        'satuan' => $item->satuan,
                        'jumlah' => $jumlah,
                        'harga_satuan' => $harga,
                        'subtotal' => $subtotal,
                    ];
                }

                $penjualanId = (int) DB::table('penjualan')->insertGetId([
                    'kode_transaksi' => 'BON-TRX-' . now()->format('YmdHis') . '-' . random_int(10, 99),
                    'tipe' => 'bon',
                    'pelanggan_id' => $pelangganId,
                    'nama_pelanggan' => trim($validated['nama_pelanggan']),
                    'metode_bayar' => null,
                    'total' => $total,
                    'user_id' => Auth::id(),
                    'waktu_transaksi' => now('Asia/Jakarta'),
                ]);

                foreach ($barisDetail as $detail) {
                    $detail['penjualan_id'] = $penjualanId;
                    DB::table('detail_penjualan')->insert($detail);
                }

                $jumlahBon = DB::table('bon_piutang')->count() + 1;
                $kodeBonDefault = 'BON-' . str_pad((string) $jumlahBon, 4, '0', STR_PAD_LEFT);

                $bonId = (int) DB::table('bon_piutang')->insertGetId([
                    'kode_bon' => $kodeBonDefault,
                    'penjualan_id' => $penjualanId,
                    'pelanggan_id' => $pelangganId,
                    'alamat_pengantaran' => $validated['alamat'] ?? null,
                    'tanggal_bon' => $validated['tanggal_bon'],
                    'jatuh_tempo' => $validated['jatuh_tempo'],
                    'total_tagihan' => $total,
                    'total_terbayar' => 0,
                    'status_bayar' => 'belum_lunas',
                    'dibuat_oleh' => Auth::id(),
                ]);

                return $bonId;
            });
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->withInput()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Bon berhasil dicatat.',
                'bon_id' => $bonId,
            ], 201);
        }

        return redirect()->route('kasir.daftar-bon')->with('success', 'Bon berhasil dicatat! Status awal: Belum Dikirim.');
    }

    public function show(int $id): JsonResponse
    {
        $sets = $this->callMultiResultProcedure($this->reportConnection(), 'CALL sp_detail_bon(?)', [$id]);
        abort_if(empty($sets[0]), 404, 'Bon tidak ditemukan.');

        $statusDb = DB::selectOne('SELECT fn_sisa_bon(?) AS sisa, fn_status_bon(?) AS status', [$id, $id]);

        return response()->json([
            'bon' => $sets[0][0],
            'items' => $sets[1] ?? [],
            'log_pengiriman' => $sets[2] ?? [],
            'pembayaran' => $sets[3] ?? [],
            'sisa_terkini' => $statusDb?->sisa,
            'status_terkini' => $statusDb?->status,
        ]);
    }

    public function kirim(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'karyawan_id' => ['required', 'integer', 'exists:users,id'],
            'jadwal_kirim' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $hasil = DB::select('CALL sp_kirim_bon(?, ?, ?, ?, ?)', [
                $id,
                $validated['karyawan_id'],
                $validated['jadwal_kirim'] ?? null,
                $validated['catatan'] ?? null,
                Auth::id(),
            ]);
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Bon berhasil dikirim ke karyawan.',
                'pengiriman_id' => $hasil[0]->pengiriman_id ?? null,
            ]);
        }

        return back()->with('success', 'Bon berhasil dikirim ke karyawan.');
    }

    public function batalkanPengiriman(Request $request, int $pengirimanId): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'alasan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::statement('CALL sp_batalkan_pengiriman(?, ?, ?)', [
                $pengirimanId,
                Auth::id(),
                $validated['alasan'] ?? null,
            ]);
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Pengiriman dibatalkan dan stok barang telah dikembalikan.']);
        }

        return back()->with('success', 'Pengiriman dibatalkan dan stok barang telah dikembalikan.');
    }

    public function gantiKaryawan(Request $request, int $pengirimanId): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'karyawan_baru_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        try {
            DB::statement('CALL sp_ganti_karyawan(?, ?, ?)', [
                $pengirimanId,
                $validated['karyawan_baru_id'],
                Auth::id(),
            ]);
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Karyawan pengantar berhasil diganti.']);
        }

        return back()->with('success', 'Karyawan pengantar berhasil diganti.');
    }

    public function bayar(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'metode' => ['required', Rule::in(['cash', 'transfer'])],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $hasil = DB::select('CALL sp_bayar_bon(?, ?, ?, ?, ?)', [
                $id,
                $validated['jumlah'] ?? null,
                $validated['metode'],
                $validated['catatan'] ?? null,
                Auth::id(),
            ]);
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pembayaran bon berhasil dicatat.',
                'data' => $hasil[0] ?? null,
            ]);
        }

        return back()->with('success', 'Pembayaran bon berhasil dicatat.');
    }
}
