<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Throwable;

/**
 * Mencadangkan basis data MySQL menggunakan akun `pj_backup`
 * dan memperbarui `system_configs.backup_terakhir` (F02).
 */
class RunBackup extends Command
{
    protected $signature = 'backup:run {--path= : Lokasi file SQL keluaran (opsional)}';

    protected $description = 'Jalankan pencadangan basis data MySQL ke file SQL dan catat waktunya.';

    public function handle(): int
    {
        $koneksiNama = (string) config('database.backup.connection', config('database.default', 'mysql'));
        $koneksi = config("database.connections.{$koneksiNama}", []);

        if (($koneksi['driver'] ?? 'mysql') !== 'mysql') {
            $this->error("Driver koneksi [{$koneksiNama}] bukan MySQL.");

            return self::FAILURE;
        }

        $host = (string) ($koneksi['host'] ?? '127.0.0.1');
        $port = (string) ($koneksi['port'] ?? '3306');
        $database = (string) ($koneksi['database'] ?? '');
        $username = (string) config('database.backup.username', 'pj_backup');
        $password = (string) config('database.backup.password', '');

        if ($database === '' || $username === '') {
            $this->error('Konfigurasi database atau username backup belum lengkap.');

            return self::FAILURE;
        }

        $waktu = now('Asia/Jakarta');
        $tujuanOpsi = $this->option('path');
        $outputPath = is_string($tujuanOpsi) && trim($tujuanOpsi) !== ''
            ? trim($tujuanOpsi)
            : rtrim((string) config('database.backup.directory', storage_path('app/private/backups')), '\\/')
                .DIRECTORY_SEPARATOR.'backup_'.$waktu->format('Y-m-d_His').'.sql';

        File::ensureDirectoryExists(dirname($outputPath));

        $binary = $this->lokasiMysqldump();
        $argumen = [
            $binary,
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            '--single-transaction',
            '--no-tablespaces',
            '--events',
            '--triggers',
            '--result-file='.$outputPath,
            $database,
        ];

        $env = $password !== '' ? ['MYSQL_PWD' => $password] : [];

        try {
            $hasil = Process::timeout(300)->env($env)->run($argumen);
        } catch (Throwable $e) {
            $this->hapusFileParsial($outputPath);
            $this->error('Gagal menjalankan mysqldump: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! $hasil->successful()) {
            $this->hapusFileParsial($outputPath);
            $pesanError = trim($hasil->errorOutput()) ?: 'Exit code '.$hasil->exitCode();
            $this->error("Backup gagal: {$pesanError}");

            return self::FAILURE;
        }

        DB::table('system_configs')->updateOrInsert(
            ['kunci' => 'backup_terakhir'],
            [
                'nilai' => $waktu->format('Y-m-d H:i:s'),
                'deskripsi' => 'Timestamp backup database terakhir (diisi otomatis oleh command backup:run)',
                'updated_at' => $waktu,
            ]
        );

        $this->info("Backup berhasil disimpan ke {$outputPath} ({$waktu->format('Y-m-d H:i:s')} WIB).");

        return self::SUCCESS;
    }

    private function lokasiMysqldump(): string
    {
        $kustom = trim((string) config('database.backup.mysqldump_path', ''));
        if ($kustom !== '') {
            return $kustom;
        }

        $namaBinary = PHP_OS_FAMILY === 'Windows' ? 'mysqldump.exe' : 'mysqldump';

        foreach (explode(PATH_SEPARATOR, (string) getenv('PATH')) as $dir) {
            $dir = trim($dir);
            if ($dir === '') {
                continue;
            }

            $kandidat = rtrim($dir, '\\/').DIRECTORY_SEPARATOR.$namaBinary;
            if (is_file($kandidat)) {
                return $kandidat;
            }
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $laragon = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
            if ($laragon !== false && $laragon !== []) {
                rsort($laragon);

                return $laragon[0];
            }
        }

        return 'mysqldump';
    }

    private function hapusFileParsial(string $path): void
    {
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
