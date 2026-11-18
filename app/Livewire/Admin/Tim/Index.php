<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Tim;

use App\Models\AnggotaTim;
use App\Models\SystemConfig;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\FileService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * Halaman Tim Toko (F04) — 5 kartu anggota tetap yang dapat diedit sendiri oleh Admin
 * dengan latar belakang transparan untuk foto bersama tim.
 */
#[Layout('components.layouts.app')]
#[Title('Tim')]
class Index extends Component
{
    use WithFileUploads;

    private const KUNCI_FOTO_BERSAMA = 'foto_tim_bersama';

    /**
     * 5 kartu anggota bawaan sesuai tampilan desain.
     *
     * @var array<int, array{nama: string, jabatan: string}>
     */
    private const KARTU_BAWAAN = [
        1 => ['nama' => 'Rudi Hartono', 'jabatan' => 'Administrator Sistem'],
        2 => ['nama' => 'Dewi Anggraini', 'jabatan' => 'Manajer Operasional'],
        3 => ['nama' => 'Bagas Pratama', 'jabatan' => 'Kepala Gudang'],
        4 => ['nama' => 'Sri Wahyuni', 'jabatan' => 'Staf Keuangan'],
        5 => ['nama' => 'Yusuf Maulana', 'jabatan' => 'Koordinator Pengantaran'],
    ];

    // ── State Modal Edit Kartu Anggota ──
    public bool $showFormModal = false;

    public ?int $editSlot = null;

    public string $nama = '';

    public string $jabatan = '';

    public ?TemporaryUploadedFile $foto = null;

    public ?string $fotoLama = null;

    public bool $hapusFotoSaatIni = false;

    // ── State Modal Foto Bersama Tim (Background Transparan) ──
    public bool $showFotoBersamaModal = false;

    public ?TemporaryUploadedFile $fotoBersama = null;

    public function mount(): void
    {
        Gate::authorize('viewAny', AnggotaTim::class);
    }

    public function updatedFoto(): void
    {
        try {
            $this->validateOnly('foto', [
                'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ], [
                'foto.image' => 'File harus berupa gambar.',
                'foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
                'foto.max' => 'Ukuran foto maksimal 4 MB.',
            ]);

            $this->hapusFotoSaatIni = false;
        } catch (ValidationException $e) {
            $this->foto = null;

            throw $e;
        }
    }

    public function updatedFotoBersama(): void
    {
        try {
            $this->validateOnly('fotoBersama', [
                'fotoBersama' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ], [
                'fotoBersama.image' => 'File harus berupa gambar.',
                'fotoBersama.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
                'fotoBersama.max' => 'Ukuran foto maksimal 4 MB.',
            ]);
        } catch (ValidationException $e) {
            $this->fotoBersama = null;

            throw $e;
        }
    }

    public function bukaModalEdit(int $slot): void
    {
        abort_unless(isset(self::KARTU_BAWAAN[$slot]), 404);

        $anggota = AnggotaTim::query()->where('urutan', $slot)->first();

        if ($anggota !== null) {
            Gate::authorize('update', $anggota);
        } else {
            Gate::authorize('create', AnggotaTim::class);
        }

        $bawaan = self::KARTU_BAWAAN[$slot];

        $this->resetValidation();
        $this->editSlot = $slot;
        $this->nama = $anggota?->nama ?? $bawaan['nama'];
        $this->jabatan = $anggota?->jabatan ?? $bawaan['jabatan'];
        $this->foto = null;
        $this->fotoLama = $anggota?->foto;
        $this->hapusFotoSaatIni = false;
        $this->showFormModal = true;
    }

    public function simpan(FileService $fileService, ActivityLogService $audit): void
    {
        $slot = $this->editSlot;
        abort_unless($slot !== null && isset(self::KARTU_BAWAAN[$slot]), 404);

        $this->nama = trim($this->nama);
        $this->jabatan = trim($this->jabatan);

        $anggota = AnggotaTim::query()->where('urutan', $slot)->first();

        if ($anggota !== null) {
            Gate::authorize('update', $anggota);
        } else {
            Gate::authorize('create', AnggotaTim::class);
        }

        $this->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:80'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        $actor = $this->penggunaAktif();
        $fotoBaru = null;
        $fotoLamaUntukHapus = null;

        if ($this->foto !== null) {
            $fotoBaru = $fileService->simpanFotoTim($this->foto);
            if ($anggota?->foto) {
                $fotoLamaUntukHapus = $anggota->foto;
            }
        } elseif ($this->hapusFotoSaatIni && $anggota?->foto) {
            $fotoLamaUntukHapus = $anggota->foto;
        }

        if ($anggota !== null) {
            $dataLama = [
                'nama' => $anggota->nama,
                'jabatan' => $anggota->jabatan,
            ];

            DB::transaction(function () use ($actor, $anggota, $fotoBaru, $dataLama, $audit): void {
                $anggota->nama = $this->nama;
                $anggota->jabatan = $this->jabatan;

                if ($fotoBaru !== null) {
                    $anggota->foto = $fotoBaru;
                } elseif ($this->hapusFotoSaatIni) {
                    $anggota->foto = null;
                }

                // Trigger `trg_t14_anggota_tim_bu` memperbarui inisial bila nama berubah.
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
            DB::transaction(function () use ($actor, $slot, $fotoBaru, $audit): void {
                $anggotaBaru = new AnggotaTim;
                $anggotaBaru->nama = $this->nama;
                $anggotaBaru->jabatan = $this->jabatan;
                $anggotaBaru->foto = $fotoBaru;
                $anggotaBaru->urutan = $slot;
                // Trigger `trg_t14_anggota_tim_bi` mengisi kolom `inisial` otomatis.
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

        $this->showFormModal = false;
        $this->reset(['editSlot', 'nama', 'jabatan', 'foto', 'fotoLama', 'hapusFotoSaatIni']);
        $this->dispatch('toast', message: 'Data anggota tim berhasil diperbarui.', type: 'success');
    }

    public function bukaModalFotoBersama(): void
    {
        Gate::authorize('create', AnggotaTim::class);

        $this->resetValidation();
        $this->fotoBersama = null;
        $this->showFotoBersamaModal = true;
    }

    public function simpanFotoBersama(FileService $fileService, ActivityLogService $audit): void
    {
        Gate::authorize('create', AnggotaTim::class);

        $this->validate([
            'fotoBersama' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'fotoBersama.required' => 'Pilih foto bersama tim terlebih dahulu.',
            'fotoBersama.image' => 'File harus berupa gambar.',
            'fotoBersama.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'fotoBersama.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        $actor = $this->penggunaAktif();
        $pathLama = trim((string) SystemConfig::nilai(self::KUNCI_FOTO_BERSAMA, ''));
        $pathBaru = $fileService->simpanFotoTim($this->fotoBersama);
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

        $this->showFotoBersamaModal = false;
        $this->fotoBersama = null;
        $this->dispatch('toast', message: 'Foto bersama tim berhasil diperbarui.', type: 'success');
    }

    public function hapusFotoBersama(FileService $fileService, ActivityLogService $audit): void
    {
        Gate::authorize('create', AnggotaTim::class);

        $actor = $this->penggunaAktif();
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

        $this->showFotoBersamaModal = false;
        $this->fotoBersama = null;
        $this->dispatch('toast', message: 'Foto bersama tim berhasil dihapus.', type: 'success');
    }

    public function render(): View
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
                'foto_url' => $baris?->foto ? Storage::disk('public')->url($baris->foto) : null,
            ];
        }

        $fotoBersamaPath = trim((string) SystemConfig::nilai(self::KUNCI_FOTO_BERSAMA, ''));
        $fotoBersamaUrl = $fotoBersamaPath !== ''
            ? Storage::disk('public')->url($fotoBersamaPath)
            : null;

        return view('livewire.admin.tim.index', [
            'daftarKartu' => $daftarKartu,
            'fotoBersamaUrl' => $fotoBersamaUrl,
        ]);
    }

    private function penggunaAktif(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
