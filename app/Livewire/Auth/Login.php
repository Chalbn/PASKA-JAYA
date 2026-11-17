<?php

namespace App\Livewire\Auth;

use App\Enums\RolePengguna;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use App\Support\NomorHp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Login')]
class Login extends Component
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public string $login = '';

    public string $password = '';

    protected function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function masuk(ActivityLogService $audit, OtpService $otp): void
    {
        $this->validate();
        $this->pastikanBelumTerblokir();

        $user = $this->cariPengguna();

        // Bila pengguna tidak ditemukan, tetap lakukan 1 operasi hash agar waktu respons
        // tidak membocorkan apakah email/nomor HP terdaftar.
        if ($user) {
            $passwordCocok = Hash::check($this->password, $user->password);
        } else {
            Hash::make($this->password);
            $passwordCocok = false;
        }

        if (! $user || ! $passwordCocok) {
            RateLimiter::hit($this->kunciLimiter(), self::DETIK_BLOKIR);
            $this->addError('login', 'Email/nomor HP atau password salah.');
            $this->reset('password');

            return;
        }

        // Status dicek setelah password benar: pesan "dinonaktifkan" tidak bisa dipakai menebak akun orang lain.
        if (! $user->isAktif()) {
            $this->addError('login', 'Akun Anda dinonaktifkan. Hubungi admin.');
            $this->reset('password');

            return;
        }

        RateLimiter::clear($this->kunciLimiter());

        $sedangGantiAkun = Auth::check();

        if ($sedangGantiAkun) {
            Auth::guard('web')->logout();
            session()->invalidate();
        }

        if (config('auth.otp_enabled') && $user->email_verified_at === null) {
            session()->regenerate();
            session(['otp.user_id' => $user->id]);

            if ($otp->detikSisaCooldown($user) === 0) {
                $otp->kirim($user);
            }

            $this->redirectRoute('otp.verify');

            return;
        }

        $tujuan = $sedangGantiAkun
            ? $user->urlBeranda()
            : $this->tentukanTujuanRedirect($user);

        Auth::login($user);
        session()->regenerate();

        $audit->catat('login', 'auth', 'Login berhasil', $user, $user->id);

        $this->redirect($tujuan);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }

    private function cariPengguna(): ?User
    {
        $identitas = $this->identitas();

        if ($identitas === '') {
            return null;
        }

        return str_contains($identitas, '@')
            ? User::query()->where('email', $identitas)->first()
            : User::query()->where('no_hp', $identitas)->first();
    }

    /**
     * Email (huruf kecil) atau nomor HP yang sudah dinormalisasi.
     */
    private function identitas(): string
    {
        $login = trim($this->login);

        return str_contains($login, '@')
            ? Str::lower($login)
            : NomorHp::normalisasi($login);
    }

    private function kunciLimiter(): string
    {
        return 'login|'.sha1($this->identitas().'|'.request()->ip());
    }

    private function pastikanBelumTerblokir(): void
    {
        $kunci = $this->kunciLimiter();

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
            ]);
        }
    }

    /**
     * Hanya ikuti url.intended bila jalurnya memang milik role pengguna yang baru login,
     * agar tidak langsung mendapat 403 setelah login lintas-role.
     */
    private function tentukanTujuanRedirect(User $user): string
    {
        $default = $user->urlBeranda();
        $intended = session()->pull('url.intended');

        if (! is_string($intended) || $intended === '') {
            return $default;
        }

        $path = '/'.ltrim((string) parse_url($intended, PHP_URL_PATH), '/');
        $roleNama = RolePengguna::tryFrom((string) $user->role?->nama)?->value;

        if ($roleNama && ($path === '/'.$roleNama || str_starts_with($path, '/'.$roleNama.'/'))) {
            return $intended;
        }

        return $default;
    }
}
