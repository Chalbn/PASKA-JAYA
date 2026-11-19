<?php

declare(strict_types=1);

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KehadiranController extends Controller
{
    public function index(Request $request, AbsensiService $service): View|JsonResponse
    {
        $search = trim((string) $request->input('search', $request->input('cari', '')));
        $filterKaryawan = (string) $request->input('filterKaryawan', $request->input('karyawan_id', ''));
        $filterTanggal = (string) $request->input('filterTanggal', $request->input('tanggal', ''));

        $daftarAbsensi = $service->getDaftarAbsensi([
            'cari' => $search,
            'karyawan_id' => $filterKaryawan,
            'tanggal' => $filterTanggal,
        ])->withQueryString();

        $daftarKaryawan = $service->getDaftarKaryawanAktif();
        $kehadiran = $daftarAbsensi->getCollection();

        if ($request->wantsJson()) {
            return response()->json([
                'kehadiran' => $kehadiran,
                'daftar_absensi' => $daftarAbsensi,
                'daftar_karyawan' => $daftarKaryawan,
            ]);
        }

        return view('kasir.kehadiran', compact(
            'daftarAbsensi',
            'daftarKaryawan',
            'kehadiran',
            'search',
            'filterKaryawan',
            'filterTanggal'
        ));
    }

    public function store(Request $request, AbsensiService $service): JsonResponse|RedirectResponse
    {
        $validated = $this->validasiKehadiran($request);

        $absensi = $service->simpan($validated, (int) Auth::id());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kehadiran berhasil ditambahkan.',
                'data' => $absensi,
            ], 201);
        }

        return redirect()->route('kasir.kehadiran')->with('success', 'Kehadiran berhasil ditambahkan.');
    }

    public function update(Request $request, int $id, AbsensiService $service): JsonResponse|RedirectResponse
    {
        $validated = $this->validasiKehadiran($request);

        $absensi = $service->update($id, $validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data kehadiran berhasil diperbarui.',
                'data' => $absensi,
            ]);
        }

        return redirect()->route('kasir.kehadiran')->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id, AbsensiService $service): JsonResponse|RedirectResponse
    {
        $service->hapus($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Catatan kehadiran berhasil dihapus.']);
        }

        return redirect()->route('kasir.kehadiran')->with('success', 'Catatan kehadiran berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validasiKehadiran(Request $request): array
    {
        // Buang baris pekerjaan yang kosong jika lebih dari 1
        $pekerjaanRaw = (array) $request->input('pekerjaan', []);
        $pekerjaanBersih = array_values(array_filter(
            array_map(fn ($v) => trim((string) $v), $pekerjaanRaw),
            fn ($v) => $v !== ''
        ));

        if (! empty($pekerjaanBersih)) {
            $request->merge(['pekerjaan' => $pekerjaanBersih]);
        }

        return $request->validate([
            'karyawan_id' => ['required', 'integer', 'exists:users,id'],
            'tanggal' => ['required', 'date'],
            'shift' => ['nullable', Rule::in(['pagi', 'siang', 'full'])],
            'pekerjaan' => ['required', 'array', 'min:1'],
            'pekerjaan.*' => ['required', 'string', 'max:150'],
        ], [
            'karyawan_id.required' => 'Nama karyawan wajib dipilih.',
            'tanggal.required' => 'Tanggal kehadiran wajib diisi.',
            'pekerjaan.required' => 'Minimal satu pekerjaan harus diisi.',
            'pekerjaan.*.required' => 'Deskripsi pekerjaan tidak boleh kosong.',
            'pekerjaan.*.max' => 'Pekerjaan maksimal 150 karakter.',
        ]);
    }
}
