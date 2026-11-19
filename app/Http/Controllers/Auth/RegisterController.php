<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use App\Support\NomorHp;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public function show(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, OtpService $otp, ActivityLogService $audit): RedirectResponse|JsonResponse
    {
        $kunciLimiter = 'register|' . $request->ip();

        if (RateLimiter::tooManyAttempts($kunciLimiter, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunciLimiter);

            throw ValidationException::withMessages([
                'nama' => "Terlalu banyak percobaan pendaftaran. Coba lagi dalam {$detik} detik.",
            ]);
        }

        $request->merge([
            'nama' => trim((string) $request->input('nama', '')),
            'email' => Str::lower(trim((string) $request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')],
            'no_hp' => ['required', 'string', 'max:20', $this->aturanNomorHp()],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['required', 'same:password'],
            'terms' => ['accepted'],
        ]);

        RateLimiter::hit($kunciLimiter, self::DETIK_BLOKIR);

        $role = Role::query()->where('nama', RolePengguna::Karyawan->value)->first();

        if (! $role) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['nama' => 'Pendaftaran belum dapat digunakan karena data role belum tersedia. Hubungi admin.']);
        }

        try {
            $user = User::create([
                'role_id' => $role->id,
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_hp' => NomorHp::normalisasi($validated['no_hp']),
                'password' => $validated['password'],
                'status' => StatusPengguna::Aktif,
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['email' => 'Email atau nomor HP sudah terdaftar.']);
        }

        $audit->catat('tambah', 'pengguna', 'Registrasi akun baru (role karyawan)', $user, $user->id);

        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }

        if (config('auth.otp_enabled')) {
            $request->session()->regenerate();
            session(['otp.user_id' => $user->id]);

            if (! $otp->kirim($user)) {
                session()->flash('error', 'Kode verifikasi gagal dikirim. Silakan kirim ulang kode.');
            }

            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('otp.verify')], 201);
            }

            return redirect()->route('otp.verify');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $audit->catat('login', 'auth', 'Login otomatis setelah registrasi', $user, $user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registrasi berhasil.',
                'redirect' => $user->urlBeranda(),
            ], 201);
        }

        return redirect()->to($user->urlBeranda());
    }

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
}
