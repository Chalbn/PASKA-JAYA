<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PenggunaanBahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BahanHarianController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('viewAny', PenggunaanBahan::class);

        $daftarBahan = PenggunaanBahan::with('user')
            ->when($request->filled('tanggal'), fn ($q) => $q->whereDate('tanggal', (string) $request->input('tanggal')))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $totalPengeluaran = (int) PenggunaanBahan::sum('harga');
        $bahan = $daftarBahan;

        $editItem = null;
        if ($request->filled('edit')) {
            $editItem = PenggunaanBahan::find((int) $request->input('edit'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'bahan' => $bahan,
                'total_pengeluaran' => $totalPengeluaran,
            ]);
        }

        return view('kasir.bahan-harian', compact(
            'daftarBahan',
            'totalPengeluaran',
            'bahan',
            'editItem'
        ));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        Gate::authorize('create', PenggunaanBahan::class);

        $validated = $this->validasiBahan($request);

        $bahan = PenggunaanBahan::create([
            'tanggal' => $validated['tanggal'],
            'nama_bahan' => trim($validated['nama_bahan']),
            'jumlah' => $validated['jumlah'],
            'satuan' => trim($validated['satuan']),
            'harga' => $validated['harga'],
            'keterangan' => $validated['keterangan'] ?? null,
            'user_id' => Auth::id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Bahan harian berhasil ditambahkan.',
                'data' => $bahan,
            ], 201);
        }

        return redirect()->route('kasir.bahan-harian')->with('success', 'Bahan harian berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $bahan = PenggunaanBahan::findOrFail($id);
        Gate::authorize('update', $bahan);

        $validated = $this->validasiBahan($request);

        $bahan->update([
            'tanggal' => $validated['tanggal'],
            'nama_bahan' => trim($validated['nama_bahan']),
            'jumlah' => $validated['jumlah'],
            'satuan' => trim($validated['satuan']),
            'harga' => $validated['harga'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Bahan harian berhasil diperbarui.',
                'data' => $bahan,
            ]);
        }

        return redirect()->route('kasir.bahan-harian')->with('success', 'Bahan harian berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $bahan = PenggunaanBahan::findOrFail($id);
        Gate::authorize('delete', $bahan);
        $bahan->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Bahan harian berhasil dihapus.']);
        }

        return redirect()->route('kasir.bahan-harian')->with('success', 'Bahan harian berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validasiBahan(Request $request): array
    {
        $pesanWajib = 'Nama bahan, jumlah, dan harga wajib diisi.';

        $validator = Validator::make($request->all(), [
            'nama_bahan' => ['required', 'string', 'max:150'],
            'jumlah' => ['required', 'numeric', 'gt:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga' => ['required', 'integer', 'gte:0'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_bahan.required' => $pesanWajib,
            'jumlah.required' => $pesanWajib,
            'harga.required' => $pesanWajib,
            'jumlah.gt' => 'Jumlah bahan harus lebih dari 0.',
            'harga.gte' => 'Harga tidak boleh negatif.',
        ]);

        return $validator->validate();
    }
}
