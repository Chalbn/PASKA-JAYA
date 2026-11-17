<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

/**
 * Satu komponen untuk dua rute (API_ROUTES.md):
 *  - /lupa-password            → minta tautan reset lewat email
 *  - /reset-password/{token}   → set password baru memakai tautan dari email
 */
#[Layout('components.layouts.auth')]
#[Title('Lupa Password')]
class LupaPassword extends Component
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    #[Locked]
    public ?string $token = null;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terkirim = false;

    public function mount(?string $token = null): void
    {
        $this->token = $token;

        if ($token !== null) {
            $this->email = (string) request()->query('email', '');
        }
    }

    /**
     * Selalu menjawab netral agar tidak bisa dipakai menebak email yang terdaftar.
     */
    public function kirimTautan(): void
    {
        $this->validate(['email' => ['required', 'email:rfc', 'max:150']]);
        $this->pastikanBelumTerblokir('lupa-password');

        RateLimiter::hit($this->kunciLimiter('lupa-password'), self::DETIK_BLOKIR);

        try {
            Password::sendResetLink(['email' => Str::lower(trim($this->email))]);
        } catch (Throwable $e) {
            report($e);
        }

        $this->terkirim = true;
    }

    public function resetPassword(ActivityLogService $audit): void
    {
        $this->validate([
            'email' => ['required', 'email:rfc', 'max:150'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['required', 'same:password'],
        ]);
        $this->pastikanBelumTerblokir('reset-password');

        $pengguna = null;

        $status = Password::reset(
            [
                'email' => Str::lower(trim($this->email)),
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => (string) $this->token,
            ],
            function (User $user, string $password) use (&$pengguna): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                $pengguna = $user;

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            RateLimiter::hit($this->kunciLimiter('reset-password'), self::DETIK_BLOKIR);

            $this->addError('password', 'Tautan reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
            $this->reset('password', 'password_confirmation');

            return;
        }

        $audit->catat('ubah', 'pengguna', 'Reset password lewat email', $pengguna, $pengguna?->id);

        session()->flash('success', 'Password berhasil diubah. Silakan login dengan password baru.');

        $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.auth.lupa-password');
    }

    private function kunciLimiter(string $aksi): string
    {
        return $aksi.'|'.request()->ip();
    }

    private function pastikanBelumTerblokir(string $aksi): void
    {
        $kunci = $this->kunciLimiter($aksi);

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.",
            ]);
        }
    }
}
