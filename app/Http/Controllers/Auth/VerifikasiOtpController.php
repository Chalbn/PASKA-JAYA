<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VerifikasiOtpController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public function show(OtpService $otp): View|RedirectResponse
    {
        $user = $this->pengguna();

        if (! config('auth.otp_enabled') || ! $user) {
            return redirect()->route('login');
        }

        return view('auth.verifikasi-otp', [
            'emailTersamar' => $this->samarkanEmail($user->email),
            'sisaCooldown' => $otp->detikSisaCooldown($user),
        ]);
    }

    public function verify(Request $request, OtpService $otp, ActivityLogService $audit): RedirectResponse|JsonResponse
    {
        $user = $this->pengguna();

        if (! $user) {
            return redirect()->route('login');
        }

        $kodeBersih = preg_replace('/\D/', '', (string) $request->input('kode', '')) ?? '';
        $request->merge(['kode' => $kodeBersih]);

        $validated = $request->validate([
            'kode' => ['required', 'digits:6'],
        ]);

        $kunci = 'otp|' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'kode' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.",
            ]);
        }

        $hasil = $otp->verifikasi($user, $validated['kode']);

        if ($hasil !== OtpService::OK) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);

            $pesan = match ($hasil) {
                OtpService::KEDALUWARSA => 'Kode sudah kedaluwarsa. Silakan kirim ulang kode.',
                OtpService::TIDAK_ADA => 'Kode tidak ditemukan. Silakan kirim ulang kode.',
                default => 'Kode tidak sesuai. Periksa kembali kode Anda.',
            };

            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan], 422);
            }

            return back()->withErrors(['kode' => $pesan]);
        }

        RateLimiter::clear($kunci);

        $user->update(['email_verified_at' => now()]);

        session()->forget('otp.user_id');
        Auth::login($user);
        $request->session()->regenerate();

        $audit->catat('login', 'auth', 'Login setelah verifikasi OTP', $user, $user->id);

        if ($request->wantsJson()) {
            return response()->json(['redirect' => $user->urlBeranda()]);
        }

        return redirect()->to($user->urlBeranda());
    }

    public function resend(Request $request, OtpService $otp): RedirectResponse|JsonResponse
    {
        $user = $this->pengguna();

        if (! $user) {
            return redirect()->route('login');
        }

        $sisa = $otp->detikSisaCooldown($user);

        if ($sisa > 0) {
            $pesan = "Tunggu {$sisa} detik sebelum meminta kode baru.";

            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan], 429);
            }

            return back()->withErrors(['kode' => $pesan]);
        }

        if ($otp->kirim($user)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Kode baru telah dikirim ke email Anda.']);
            }

            return back()->with('success', 'Kode baru telah dikirim ke email Anda.');
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kode gagal dikirim. Coba lagi beberapa saat lagi.'], 500);
        }

        return back()->with('error', 'Kode gagal dikirim. Coba lagi beberapa saat lagi.');
    }

    private function pengguna(): ?User
    {
        $id = session('otp.user_id');

        if (! $id) {
            return null;
        }

        $user = User::query()->find($id);

        if (! $user || ! $user->isAktif()) {
            session()->forget('otp.user_id');

            return null;
        }

        return $user;
    }

    private function samarkanEmail(string $email): string
    {
        [$lokal, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $awal = mb_substr($lokal, 0, min(2, mb_strlen($lokal)));
        $bintang = str_repeat('*', max(mb_strlen($lokal) - mb_strlen($awal), 1));

        return $awal . $bintang . '@' . $domain;
    }
}
