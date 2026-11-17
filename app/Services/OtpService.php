<?php

namespace App\Services;

use App\Mail\KodeOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * OTP 6 digit untuk verifikasi akun (F01). Kode disimpan sebagai hash di `otp_codes`.
 *
 * Waktu pembuatan kode diturunkan dari `kedaluwarsa_pada` (diisi aplikasi, zona Asia/Jakarta)
 * sehingga perhitungan cooldown tidak bergantung pada zona waktu server MySQL.
 */
class OtpService
{
    public const OK = 'ok';

    public const SALAH = 'salah';

    public const KEDALUWARSA = 'kedaluwarsa';

    public const TIDAK_ADA = 'tidak_ada';

    /**
     * Buat kode baru (menghanguskan kode lama yang belum dipakai) lalu kirim lewat email.
     * Mengembalikan false bila email gagal terkirim; kode yang gagal dikirim tidak disimpan.
     */
    public function kirim(User $user): bool
    {
        $kode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $menit = $this->masaBerlakuMenit();

        $user->otpCodes()->whereNull('digunakan_pada')->delete();

        $otp = $user->otpCodes()->create([
            'kode_hash' => Hash::make($kode),
            'kedaluwarsa_pada' => now()->addMinutes($menit),
        ]);

        try {
            Mail::to($user->email)->send(new KodeOtpMail($kode, $menit));
        } catch (Throwable $e) {
            report($e);
            $otp->delete();

            return false;
        }

        return true;
    }

    /**
     * Sisa detik sebelum kode boleh dikirim ulang (0 = boleh sekarang).
     */
    public function detikSisaCooldown(User $user): int
    {
        $terakhir = $user->otpCodes()->latest('id')->first();

        if (! $terakhir) {
            return 0;
        }

        $bolehKirimLagi = $terakhir->kedaluwarsa_pada
            ->copy()
            ->subMinutes($this->masaBerlakuMenit())
            ->addSeconds((int) config('auth.otp_resend_seconds', 60));

        return max(0, (int) ceil(now()->diffInSeconds($bolehKirimLagi, false)));
    }

    /**
     * Periksa kode terbaru yang belum dipakai. Kode benar langsung ditandai terpakai (sekali pakai).
     *
     * @return self::OK|self::SALAH|self::KEDALUWARSA|self::TIDAK_ADA
     */
    public function verifikasi(User $user, string $kode): string
    {
        $otp = $user->otpCodes()
            ->whereNull('digunakan_pada')
            ->latest('id')
            ->first();

        if (! $otp) {
            return self::TIDAK_ADA;
        }

        if ($otp->kedaluwarsa_pada->isPast()) {
            return self::KEDALUWARSA;
        }

        if (! Hash::check($kode, $otp->kode_hash)) {
            return self::SALAH;
        }

        $otp->update(['digunakan_pada' => now()]);

        return self::OK;
    }

    private function masaBerlakuMenit(): int
    {
        return (int) config('auth.otp_expire_minutes', 10);
    }
}
