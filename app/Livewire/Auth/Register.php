<?php

namespace App\Livewire\Auth;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use App\Support\NomorHp;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Create Account')]
class Register extends Component
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public string $nama = '';

    public string $email = '';

    public string $no_hp = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            // Unique mencakup akun soft-deleted karena constraint UNIQUE di DB tetap berlaku untuk barisnya.
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')],
            'no_hp' => ['required', 'string', 'max:20', $this->aturanNomorHp()],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['required', 'same:password'],
            'terms' => ['accepted'],
        ];
    }

    /**
     * Validasi realtime saat field kehilangan fokus (wire:model.blur).
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['nama', 'email', 'no_hp', 'password', 'password_confirmation'], true)) {
            $this->validateOnly($property);
        }

        if ($property === 'password' && $this->password_confirmation !== '') {
            $this->validateOnly('password_confirmation');
        }
    }

    public function daftar(OtpService $otp, ActivityLogService $audit): void
    {
        $this->nama = trim($this->nama);
        $this->email = Str::lower(trim($this->email));

        $this->pastikanBelumTerblokir();
        $this->validate();

        RateLimiter::hit($this->kunciLimiter(), self::DETIK_BLOKIR);

        $role = Role::query()->where('nama', RolePengguna::Karyawan->value)->first();

        if (! $role) {
            $this->addError('nama', 'Pendaftaran belum dapat digunakan karena data role belum tersedia. Hubungi admin.');

            return;
        }

        try {
            $user = User::create([
                'role_id' => $role->id,
                'nama' => $this->nama,
                'email' => $this->email,
                'no_hp' => NomorHp::normalisasi($this->no_hp),
                'password' => $this->password,
                'status' => StatusPengguna::Aktif,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Balapan: email/HP diambil pendaftar lain di antara validasi dan insert.
            $this->addError('email', 'Email atau nomor HP sudah terdaftar.');

            return;
        }

        $audit->catat('tambah', 'pengguna', 'Registrasi akun baru (role karyawan)', $user, $user->id);

        if (Auth::check()) {
            Auth::guard('web')->logout();
            session()->invalidate();
        }

        if (config('auth.otp_enabled')) {
            session()->regenerate();
            session(['otp.user_id' => $user->id]);

            if (! $otp->kirim($user)) {
                session()->flash('error', 'Kode verifikasi gagal dikirim. Silakan kirim ulang kode.');
            }

            $this->redirectRoute('otp.verify');

            return;
        }

        Auth::login($user);
        session()->regenerate();

        $audit->catat('login', 'auth', 'Login otomatis setelah registrasi', $user, $user->id);

        $this->redirect($user->urlBeranda());
    }

    public function render()
    {
        return view('livewire.auth.register');
    }

    /**
     * Nomor HP harus berformat seluler Indonesia dan belum dipakai (dibandingkan setelah dinormalisasi).
     */
    private function aturanNomorHp(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! NomorHp::valid((string) $value)) {
                $fail('Format nomor HP tidak valid. Contoh: 0812 3456 7890 atau +62 812 3456 7890.');

                return;
            }

            if (User::withTrashed()->where('no_hp', NomorHp::normalisasi((string) $value))->exists()) {
                $fail('Nomor HP sudah terdaftar.');
            }
        };
    }

    private function kunciLimiter(): string
    {
        return 'register|'.request()->ip();
    }

    private function pastikanBelumTerblokir(): void
    {
        $kunci = $this->kunciLimiter();

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'nama' => "Terlalu banyak percobaan pendaftaran. Coba lagi dalam {$detik} detik.",
            ]);
        }
    }
}
