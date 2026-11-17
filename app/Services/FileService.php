<?php

declare(strict_types=1);

namespace App\Services;

use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    protected const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    protected const MAX_FILE_SIZE_KB = 4096; // 4 MB

    /**
     * Simpan foto bukti pengiriman ke disk privat.
     * Sesuai ADR-011: storage/app/private/bukti-pengiriman
     *
     * @throws DomainException
     */
    public function simpanBuktiPengiriman(UploadedFile $file): string
    {
        $this->validasiFile($file);

        $namaFile = Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('bukti-pengiriman', $namaFile, 'local');

        if (! $path) {
            throw new DomainException('Gagal menyimpan foto bukti pengiriman.');
        }

        return $path;
    }

    /**
     * Simpan foto barang ke disk public.
     * Sesuai ADR-011: storage/app/public/foto-barang
     *
     * @throws DomainException
     */
    public function simpanFotoBarang(UploadedFile $file): string
    {
        $this->validasiFile($file);

        $namaFile = Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('foto-barang', $namaFile, 'public');

        if (! $path) {
            throw new DomainException('Gagal menyimpan foto barang.');
        }

        return $path;
    }

    /**
     * Simpan foto profil pengguna ke disk public.
     * Sesuai ADR-011: storage/app/public/foto-profil
     *
     * @throws DomainException
     */
    public function simpanFotoProfil(UploadedFile $file): string
    {
        $this->validasiFile($file);

        $namaFile = Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('foto-profil', $namaFile, 'public');

        if (! $path) {
            throw new DomainException('Gagal menyimpan foto profil.');
        }

        return $path;
    }

    /**
     * Simpan foto anggota tim ke disk public.
     * Sesuai ADR-011: storage/app/public/foto-tim
     *
     * @throws DomainException
     */
    public function simpanFotoTim(UploadedFile $file): string
    {
        $this->validasiFile($file);

        $namaFile = Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('foto-tim', $namaFile, 'public');

        if (! $path) {
            throw new DomainException('Gagal menyimpan foto anggota tim.');
        }

        return $path;
    }

    /**
     * Hapus berkas dari disk tertentu.
     */
    public function hapusFile(?string $path, string $disk = 'public'): bool
    {
        if (! $path) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Dapatkan path absolut file privat untuk otorisasi download.
     */
    public function pathPrivat(string $path): string
    {
        return Storage::disk('local')->path($path);
    }

    /**
     * Validasi format dan ukuran file.
     *
     * @throws DomainException
     */
    protected function validasiFile(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw new DomainException('Format file harus berupa JPG, PNG, atau WebP.');
        }

        $sizeKb = $file->getSize() / 1024;
        if ($sizeKb > self::MAX_FILE_SIZE_KB) {
            throw new DomainException('Ukuran file maksimal adalah 4 MB.');
        }
    }
}
