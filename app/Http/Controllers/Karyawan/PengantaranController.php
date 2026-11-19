<?php

declare(strict_types=1);

namespace App\Http\Controllers\Karyawan;

use App\Enums\StatusPengiriman;
use App\Http\Controllers\Controller;
use App\Models\PengirimanBarang;
use App\Services\PengirimanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Throwable;

class PengantaranController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $userId = (int) Auth::id();
        $tab = (string) $request->input('tab', 'akan_diantar');
        if (! in_array($tab, ['akan_diantar', 'selesai'], true)) {
            $tab = 'akan_diantar';
        }

        $jumlahBaru = PengirimanBarang::where('karyawan_id', $userId)
            ->whereNull('dibaca_pada')
            ->whereNotIn('status', [
                StatusPengiriman::Selesai->value,
                StatusPengiriman::Dibatalkan->value,
            ])
            ->count();

        $jumlahBerjalan = PengirimanBarang::where('karyawan_id', $userId)
            ->whereIn('status', [
                StatusPengiriman::DikirimKeKaryawan->value,
                StatusPengiriman::SedangDisiapkan->value,
                StatusPengiriman::SiapBerangkat->value,
            ])
            ->count();

        $jumlahSelesai = PengirimanBarang::where('karyawan_id', $userId)
            ->where('status', StatusPengiriman::Selesai->value)
            ->count();

        $pengirimanAkanDiantar = PengirimanBarang::with([
            'bonPiutang.pelanggan',
            'bonPiutang.penjualan',
            'detailPengiriman',
        ])
            ->where('karyawan_id', $userId)
            ->whereIn('status', [
                StatusPengiriman::DikirimKeKaryawan->value,
                StatusPengiriman::SedangDisiapkan->value,
                StatusPengiriman::SiapBerangkat->value,
            ])
            ->orderByRaw('CASE WHEN jadwal_kirim IS NOT NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('jadwal_kirim', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $pengirimanSelesai = PengirimanBarang::with([
            'bonPiutang.pelanggan',
            'bonPiutang.penjualan',
            'detailPengiriman',
        ])
            ->where('karyawan_id', $userId)
            ->where('status', StatusPengiriman::Selesai->value)
            ->orderBy('waktu_selesai', 'desc')
            ->get();

        $denganJadwal = $pengirimanAkanDiantar->filter(fn ($p) => $p->jadwal_kirim !== null);
        $jadwalTerdekatId = $denganJadwal->isNotEmpty()
            ? $denganJadwal->sortBy('jadwal_kirim')->first()?->id
            : null;

        if ($request->wantsJson()) {
            return response()->json([
                'ringkasan' => [
                    'baru' => $jumlahBaru,
                    'berjalan' => $jumlahBerjalan,
                    'selesai' => $jumlahSelesai,
                ],
                'pengantaran_akan_diantar' => $pengirimanAkanDiantar,
                'pengantaran_selesai' => $pengirimanSelesai,
            ]);
        }

        return view('karyawan.pengantaran.index', compact(
            'tab',
            'jumlahBaru',
            'jumlahBerjalan',
            'jumlahSelesai',
            'pengirimanAkanDiantar',
            'pengirimanSelesai',
            'jadwalTerdekatId'
        ));
    }

    public function show(Request $request, int $id, PengirimanService $service): View|JsonResponse
    {
        $pengiriman = PengirimanBarang::with([
            'bonPiutang.pelanggan',
            'bonPiutang.penjualan',
            'detailPengiriman',
            'logStatus' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'karyawan',
        ])->findOrFail($id);

        Gate::authorize('view', $pengiriman);

        $service->tandaiDibaca($pengiriman);

        $statusBerikutnya = $service->statusBerikutnya($pengiriman->status);
        $labelTombol = $service->labelStatusBerikutnya($pengiriman->status);
        $perluFoto = $statusBerikutnya === StatusPengiriman::Selesai;

        $steps = [
            StatusPengiriman::SedangDisiapkan,
            StatusPengiriman::SiapBerangkat,
            StatusPengiriman::Selesai,
        ];

        $statusOrder = [
            StatusPengiriman::DikirimKeKaryawan->value => -1,
            StatusPengiriman::SedangDisiapkan->value => 0,
            StatusPengiriman::SiapBerangkat->value => 1,
            StatusPengiriman::Selesai->value => 2,
        ];

        $currentIndex = $statusOrder[$pengiriman->status->value] ?? -1;

        $stepper = collect($steps)->map(function ($step, $index) use ($currentIndex) {
            return [
                'label' => $step->label(),
                'status' => $step->value,
                'selesai' => $index <= $currentIndex,
                'aktif' => $index === $currentIndex,
            ];
        })->toArray();

        if ($request->wantsJson()) {
            return response()->json([
                'tugas' => $pengiriman,
                'items' => $pengiriman->detailPengiriman,
                'log_pengiriman' => $pengiriman->logStatus,
                'stepper' => $stepper,
            ]);
        }

        return view('karyawan.pengantaran.detail', [
            'pengiriman' => $pengiriman,
            'tugas' => $pengiriman,
            'items' => $pengiriman->detailPengiriman,
            'logPengiriman' => $pengiriman->logStatus,
            'statusBerikutnya' => $statusBerikutnya,
            'labelTombol' => $labelTombol,
            'perluFoto' => $perluFoto,
            'stepper' => $stepper,
        ]);
    }

    public function ubahStatus(Request $request, int $id, PengirimanService $service): JsonResponse|RedirectResponse
    {
        $pengiriman = PengirimanBarang::findOrFail($id);
        Gate::authorize('updateStatus', $pengiriman);

        $statusBaru = $service->statusBerikutnya($pengiriman->status);

        if (! $statusBaru) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tidak ada status berikutnya.'], 422);
            }

            return back()->with('error', 'Tidak ada status berikutnya.');
        }

        $fotoBuktiPath = null;
        $fileKey = $request->hasFile('fotoBukti') ? 'fotoBukti' : 'foto_bukti';

        if ($statusBaru === StatusPengiriman::Selesai) {
            $request->validate([
                $fileKey => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ], [
                "{$fileKey}.required" => 'Foto bukti wajib diunggah sebelum menyelesaikan pengantaran.',
                "{$fileKey}.image" => 'File harus berupa gambar.',
                "{$fileKey}.mimes" => 'Format gambar harus jpg, jpeg, png, atau webp.',
                "{$fileKey}.max" => 'Ukuran gambar maksimal 4 MB.',
            ]);

            $fotoBuktiPath = $request->file($fileKey)->store('bukti-pengiriman', 'local');
        }

        try {
            $service->ubahStatus($pengiriman, $statusBaru, Auth::user(), $fotoBuktiPath);
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }

        $pesan = 'Status berhasil diubah ke ' . $statusBaru->label();

        if ($request->wantsJson()) {
            return response()->json(['message' => $pesan]);
        }

        return redirect()->route('karyawan.pengantaran.detail', $pengiriman->id)->with('success', $pesan);
    }
}
