<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Verifikasi Kode')]
class VerifikasiOtp extends Component
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public string $kode = '';

    /** Naik setiap kirim ulang agar hitung mundur di browser dibangun ulang. */
    public int $versi = 0;

    private ?User $penggunaCache = null;

    public function mount(): void
    {
        if (! config('auth.otp_enabled') || ! $this->pengguna()) {
            $this->redirectRoute('login');
        }
    }

    public function verifikasi(OtpService $otp, ActivityLogService $audit): void
    {
        $user = $this->pengguna();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $this->kode = preg_replace('/\D/', '', $this->kode) ?? '';
        $this->validate(['kode' => ['required', 'digits:6']]);

        $kunci = 'otp|'.$user->id.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'kode' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.",
            ]);
        }

        $hasil = $otp->verifikasi($user, $this->kode);

        if ($hasil !== OtpService::OK) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);

            $this->addError('kode', match ($hasil) {
                OtpService::KEDALUWARSA => 'Kode sudah kedaluwarsa. Silakan kirim ulang kode.',
                OtpService::TIDAK_ADA => 'Kode tidak ditemukan. Silakan kirim ulang kode.',
                default => 'Kode tidak sesuai. Periksa kembali kode Anda.',
            });
            $this->reset('kode');

            return;
        }

        RateLimiter::clear($kunci);

        $user->update(['email_verified_at' => now()]);

        session()->forget('otp.user_id');
        Auth::login($user);
        session()->regenerate();

        $audit->catat('login', 'auth', 'Login setelah verifikasi OTP', $user, $user->id);

        $this->redirectIntended(default: $user->urlBeranda());
    }

    public function kirimUlang(OtpService $otp): void
    {
        $user = $this->pengguna();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $sisa = $otp->detikSisaCooldown($user);

        if ($sisa > 0) {
            $this->addError('kode', "Tunggu {$sisa} detik sebelum meminta kode baru.");

            return;
        }

        $this->resetErrorBag();
        $this->reset('kode');
        $this->versi++;

        if ($otp->kirim($user)) {
            $this->dispatch('toast', message: 'Kode baru telah dikirim ke email Anda.', type: 'success');
        } else {
            $this->dispatch('toast', message: 'Kode gagal dikirim. Coba lagi beberapa saat lagi.', type: 'error');
        }
    }

    public function render()
    {
        $user = $this->pengguna();

        return view('livewire.auth.verifikasi-otp', [
            'emailTersamar' => $user ? $this->samarkanEmail($user->email) : '',
            'sisaCooldown' => $user ? app(OtpService::class)->detikSisaCooldown($user) : 0,
        ]);
    }

    /**
     * Pengguna yang sedang menunggu verifikasi (id disimpan di sesi server, bukan di klien).
     * Akun yang sudah dinonaktifkan tidak boleh melanjutkan.
     */
    private function pengguna(): ?User
    {
        if ($this->penggunaCache) {
            return $this->penggunaCache;
        }

        $id = session('otp.user_id');

        if (! $id) {
            return null;
        }

        $user = User::query()->find($id);

        if (! $user || ! $user->isAktif()) {
            session()->forget('otp.user_id');

            return null;
        }

        return $this->penggunaCache = $user;
    }

    private function samarkanEmail(string $email): string
    {
        [$lokal, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $awal = mb_substr($lokal, 0, min(2, mb_strlen($lokal)));
        $bintang = str_repeat('*', max(mb_strlen($lokal) - mb_strlen($awal), 1));

        return $awal.$bintang.'@'.$domain;
    }
}
