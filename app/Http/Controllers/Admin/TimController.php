<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaTim;
use App\Models\SystemConfig;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TimController extends Controller
{
    private const KUNCI_FOTO_BERSAMA = 'foto_tim_bersama';

    /**
     * @var array<int, array{nama: string, jabatan: string}>
     */
    private const KARTU_BAWAAN = [
        1 => ['nama' => 'Rudi Hartono', 'jabatan' => 'Administrator Sistem'],
        2 => ['nama' => 'Dewi Anggraini', 'jabatan' => 'Manajer Operasional'],
        3 => ['nama' => 'Bagas Pratama', 'jabatan' => 'Kepala Gudang'],
        4 => ['nama' => 'Sri Wahyuni', 'jabatan' => 'Staf Keuangan'],
        5 => ['nama' => 'Yusuf Maulana', 'jabatan' => 'Koordinator Pengantaran'],
    ];

    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('viewAny', AnggotaTim::class);

        $tersimpan = AnggotaTim::query()
            ->whereBetween('urutan', [1, 5])
            ->orderBy('id')
            ->get()
            ->keyBy('urutan');

        $daftarKartu = [];
        foreach (self::KARTU_BAWAAN as $slot => $bawaan) {
            /** @var AnggotaTim|null $baris */
            $baris = $tersimpan->get($slot);
            $nama = $baris?->nama ?? $bawaan['nama'];

            $daftarKartu[] = [
                'slot' => $slot,
                'nama' => $nama,
                'jabatan' => $baris?->jabatan ?? $bawaan['jabatan'],
                'inisial' => $baris?->inisial ?: inisial($nama),
                'foto' => $baris?->foto,
                'foto_url' => $baris?->foto ? Storage::disk('public')->url($baris->foto) : null,
            ];
        }

        $fotoBersamaPath = trim((string) SystemConfig::nilai(self::KUNCI_FOTO_BERSAMA, ''));
        $fotoBersamaUrl = $fotoBersamaPath !== ''
            ? Storage::disk('public')->url($fotoBersamaPath)
            : null;

        $anggota = collect($daftarKartu);
        $fotoBersama = $fotoBersamaPath ?: null;

        if ($request->wantsJson()) {
            return response()->json([
                'anggota' => $anggota,
                'foto_bersama' => $fotoBersama,
                'daftar_kartu' => $daftarKartu,
                'foto_bersama_url' => $fotoBersamaUrl,
            ]);
        }

        return view('admin.tim.index', compact(
            'daftarKartu',
            'fotoBersamaUrl',
            'anggota',
            'fotoBersama'
        ));
    }

    public function store(Request $request, FileService $fileService, ActivityLogService $audit): JsonResponse|RedirectResponse
    {
        $slot = (int) $request->input('urutan', $request->input('slot', 0));
        abort_unless(isset(self::KARTU_BAWAAN[$slot]), 404);

        $anggota = AnggotaTim::query()->where('urutan', $slot)->first();

        if ($anggota !== null) {
            Gate::authorize('update', $anggota);
        } else {
            Gate::authorize('create', AnggotaTim::class);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:80'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'hapus_foto' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        /** @var User $actor */
        $actor = Auth::user();
        $hapusFoto = (bool) $request->boolean('hapus_foto');
        $fotoBaru = null;
        $fotoLamaUntukHapus = null;

        if ($request->hasFile('foto')) {
            $fotoBaru = $fileService->simpanFotoTim($request->file('foto'));
            if ($anggota?->foto) {
                $fotoLamaUntukHapus = $anggota->foto;
            }
        } elseif ($hapusFoto && $anggota?->foto) {
            $fotoLamaUntukHapus = $anggota->foto;
        }

        $namaTrim = trim($validated['nama']);
        $jabatanTrim = trim($validated['jabatan']);

        if ($anggota !== null) {
            $dataLama = [
                'nama' => $anggota->nama,
                'jabatan' => $anggota->jabatan,
            ];

            DB::transaction(function () use ($actor, $anggota, $namaTrim, $jabatanTrim, $fotoBaru, $hapusFoto, $dataLama, $audit): void {
                $anggota->nama = $namaTrim;
                $anggota->jabatan = $jabatanTrim;

                if ($fotoBaru !== null) {
                    $anggota->foto = $fotoBaru;
                } elseif ($hapusFoto) {
                    $anggota->foto = null;
                }

                $anggota->save();

                $audit->catat(
                    'ubah',
                    'tim',
                    "Memperbarui anggota tim {$anggota->nama}",
                    $actor,
                    $anggota->id,
                    $dataLama,
                    ['nama' => $anggota->nama, 'jabatan' => $anggota->jabatan],
                );
            });
        } else {
            DB::transaction(function () use ($actor, $slot, $namaTrim, $jabatanTrim, $fotoBaru, $audit): void {
                $anggotaBaru = new AnggotaTim;
                $anggotaBaru->nama = $namaTrim;
                $anggotaBaru->jabatan = $jabatanTrim;
                $anggotaBaru->foto = $fotoBaru;
                $anggotaBaru->urutan = $slot;
                $anggotaBaru->save();

                $audit->catat(
                    'ubah',
                    'tim',
                    "Memperbarui anggota tim {$anggotaBaru->nama}",
                    $actor,
                    $anggotaBaru->id,
                    self::KARTU_BAWAAN[$slot],
                    ['nama' => $anggotaBaru->nama, 'jabatan' => $anggotaBaru->jabatan],
                );
            });
        }

        if ($fotoLamaUntukHapus !== null) {
            $fileService->hapusFile($fotoLamaUntukHapus);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data anggota tim berhasil diperbarui.',
            ]);
        }

        return redirect()->route('admin.tim')->with('success', 'Data anggota tim berhasil diperbarui.');
    }

    public function updateFotoBersama(Request $request, FileService $fileService, ActivityLogService $audit): JsonResponse|RedirectResponse
    {
        Gate::authorize('create', AnggotaTim::class);

        $fileKey = $request->hasFile('fotoBersama') ? 'fotoBersama' : 'foto_bersama';

        $request->validate([
            $fileKey => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            "{$fileKey}.required" => 'Pilih foto bersama tim terlebih dahulu.',
            "{$fileKey}.image" => 'File harus berupa gambar.',
            "{$fileKey}.mimes" => 'Format foto harus JPG, PNG, atau WEBP.',
            "{$fileKey}.max" => 'Ukuran foto maksimal 4 MB.',
        ]);

        /** @var User $actor */
        $actor = Auth::user();
        $pathLama = trim((string) SystemConfig::nilai(self::KUNCI_FOTO_BERSAMA, ''));
        $pathBaru = $fileService->simpanFotoTim($request->file($fileKey));
        $waktu = now('Asia/Jakarta');

        DB::table('system_configs')->updateOrInsert(
            ['kunci' => self::KUNCI_FOTO_BERSAMA],
            [
                'nilai' => $pathBaru,
                'deskripsi' => 'Path foto bersama tim untuk latar halaman Tim',
                'updated_by' => $actor->id,
                'updated_at' => $waktu,
            ]
        );

        $audit->catat(
            'ubah',
            'tim',
            'Memperbarui foto bersama tim',
            $actor,
            null,
            $pathLama !== '' ? ['foto_bersama' => $pathLama] : null,
            ['foto_bersama' => $pathBaru],
        );

        if ($pathLama !== '') {
            $fileService->hapusFile($pathLama);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Foto bersama tim berhasil diperbarui.',
                'path' => $pathBaru,
            ]);
        }

        return redirect()->route('admin.tim')->with('success', 'Foto bersama tim berhasil diperbarui.');
    }

    public function hapusFotoBersama(Request $request, FileService $fileService, ActivityLogService $audit): JsonResponse|RedirectResponse
    {
        Gate::authorize('create', AnggotaTim::class);

        /** @var User $actor */
        $actor = Auth::user();
        $pathLama = trim((string) SystemConfig::nilai(self::KUNCI_FOTO_BERSAMA, ''));

        if ($pathLama !== '') {
            DB::table('system_configs')->updateOrInsert(
                ['kunci' => self::KUNCI_FOTO_BERSAMA],
                [
                    'nilai' => '',
                    'deskripsi' => 'Path foto bersama tim untuk latar halaman Tim',
                    'updated_by' => $actor->id,
                    'updated_at' => now('Asia/Jakarta'),
                ]
            );

            $fileService->hapusFile($pathLama);

            $audit->catat(
                'hapus',
                'tim',
                'Menghapus foto bersama tim',
                $actor,
                null,
                ['foto_bersama' => $pathLama],
                null,
            );
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Foto bersama tim berhasil dihapus.']);
        }

        return redirect()->route('admin.tim')->with('success', 'Foto bersama tim berhasil dihapus.');
    }
}
