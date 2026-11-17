<?php

declare(strict_types=1);

namespace App\Services\Concerns;

use DomainException;
use Illuminate\Database\QueryException;

trait HandlesDatabaseExceptions
{
    /**
     * Jalankan callback dan terjemahkan QueryException dari SIGNAL SQLSTATE 45000 menjadi DomainException.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     *
     * @throws DomainException
     */
    protected function executeWithExceptionHandling(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (QueryException $e) {
            $this->handleQueryException($e);
        }
    }

    /**
     * Terjemahkan QueryException menjadi DomainException jika berasal dari SIGNAL SQLSTATE 45000.
     *
     * @throws DomainException
     * @throws QueryException
     */
    protected function handleQueryException(QueryException $e): never
    {
        $sqlState = (string) ($e->errorInfo[0] ?? '');
        $errorCode = $e->errorInfo[1] ?? null;
        $errorMessage = $e->errorInfo[2] ?? null;

        if ($sqlState === '45000' || $errorCode === 1644 || str_contains($e->getMessage(), 'SQLSTATE[45000]')) {
            $pesan = $errorMessage ? trim($errorMessage) : $this->extractSignalMessage($e->getMessage());
            throw new DomainException($pesan, 0, $e);
        }

        throw $e;
    }

    /**
     * Ekstrak teks pesan dari pesan QueryException mentah jika errorInfo tidak lengkap.
     */
    protected function extractSignalMessage(string $raw): string
    {
        // Contoh: SQLSTATE[45000]: <<custom error>>: 1644 Pesan di sini (Connection: ...)
        if (preg_match('/(?:1644|SQLSTATE\[45000\]:[^:]*:)\s*([^(]+)/', $raw, $matches)) {
            return trim($matches[1]);
        }

        return 'Terjadi kesalahan pada aturan sistem database.';
    }
}
