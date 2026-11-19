<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PDO;

abstract class Controller
{
    /**
     * Koneksi read-only khusus pelaporan (pj_report) jika dikonfigurasi di config/database.php,
     * fallback ke koneksi default (pj_app) bila belum didefinisikan atau saat testing.
     */
    protected function reportConnection(): string
    {
        if (app()->environment('testing')) {
            return (string) config('database.default', 'mysql');
        }

        $connections = config('database.connections', []);

        return array_key_exists('mysql_report', $connections)
            ? 'mysql_report'
            : (string) config('database.default', 'mysql');
    }

    /**
     * Memanggil stored procedure yang mengembalikan lebih dari satu result set
     * (sp_laporan_penjualan, sp_detail_bon, sp_penjualan_mingguan) menggunakan PDO
     * agar tidak memicu error 2014 (unbuffered queries active).
     *
     * @param  array<int, mixed>  $bindings
     * @return array<int, array<int, object>>
     */
    protected function callMultiResultProcedure(string $connection, string $sql, array $bindings = []): array
    {
        $pdo = DB::connection($connection)->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);

        $sets = [];
        do {
            if ($stmt->columnCount() > 0) {
                $sets[] = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
        } while ($stmt->nextRowset());

        $stmt->closeCursor();

        return $sets;
    }

    /**
     * Mencatat aktivitas ke activity_logs melalui stored procedure sp_catat_aktivitas
     * (activity_logs bersifat append-only sesuai trigger trg_t8_activity_logs_bu/bd).
     */
    protected function catatAktivitas(
        ?int $userId,
        string $aksi,
        string $modul,
        ?int $targetId,
        string $deskripsi,
        ?array $dataLama = null,
        ?array $dataBaru = null,
    ): void {
        DB::statement('CALL sp_catat_aktivitas(?, ?, ?, ?, ?, ?, ?)', [
            $userId,
            $aksi,
            $modul,
            $targetId,
            $deskripsi,
            $dataLama !== null ? json_encode($dataLama, JSON_UNESCAPED_UNICODE) : null,
            $dataBaru !== null ? json_encode($dataBaru, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    /**
     * Mengambil pesan error yang bersih dari SIGNAL SQLSTATE '45000' trigger/procedure MySQL.
     */
    protected function dbErrorMessage(QueryException $e, string $fallback = 'Operasi database gagal diproses.'): string
    {
        $info = $e->errorInfo ?? [];
        if (isset($info[1]) && (int) $info[1] === 1644 && ! empty($info[2])) {
            return (string) $info[2];
        }

        return $fallback;
    }
}