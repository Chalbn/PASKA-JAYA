<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RolePengguna;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\OtpService;
use App\Support\NomorHp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, ActivityLogService $audit, OtpService $otp): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $identitas = $this->identitas($validated['login']);
        $kunciLimiter = 'login|' . sha1($identitas . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($kunciLimiter, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunciLimiter);

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
            ]);
        }

        $user = $this->cariPengguna($identitas);

        if ($user !== null) {
            $passwordCocok = Hash::check($validated['password'], $user->password);
        } else {
            Hash::make($validated['password']);
            $passwordCocok = false;
        }

        if ($user === null || ! $passwordCocok) {
            RateLimiter::hit($kunciLimiter, self::DETIK_BLOKIR);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email/nomor HP atau password salah.'], 422);
            }

            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Email/nomor HP atau password salah.']);
        }

        if (! $user->isAktif()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akun Anda dinonaktifkan. Hubungi admin.'], 403);
            }

            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Akun Anda dinonaktifkan. Hubungi admin.']);
        }

        RateLimiter::clear($kunciLimiter);

        $sedangGantiAkun = Auth::check();

        if ($sedangGantiAkun) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }

        if (config('auth.otp_enabled') && $user->email_verified_at === null) {
            $request->session()->regenerate();
            session(['otp.user_id' => $user->id]);

            if ($otp->detikSisaCooldown($user) === 0) {
                $otp->kirim($user);
            }

            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('otp.verify')]);
            }

            return redirect()->route('otp.verify');
        }

        $tujuan = $sedangGantiAkun
            ? $user->urlBeranda()
            : $this->tentukanTujuanRedirect($user);

        Auth::login($user);
        $request->session()->regenerate();

        $audit->catat('login', 'auth', 'Login berhasil', $user, $user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Login berhasil.',
                'redirect' => $tujuan,
            ]);
        }

        return redirect()->to($tujuan);
    }

    private function cariPengguna(string $identitas): ?User
    {
        if ($identitas === '') {
            return null;
        }

        return str_contains($identitas, '@')
            ? User::query()->where('email', $identitas)->first()
            : User::query()->where('no_hp', $identitas)->first();
    }

    private function identitas(string $rawLogin): string
    {
        $login = trim($rawLogin);

        return str_contains($login, '@')
            ? Str::lower($login)
            : NomorHp::normalisasi($login);
    }

    private function tentukanTujuanRedirect(User $user): string
    {
        $default = $user->urlBeranda();
        $intended = session()->pull('url.intended');

        if (! is_string($intended) || $intended === '') {
            return $default;
        }

        $path = '/' . ltrim((string) parse_url($intended, PHP_URL_PATH), '/');
        $roleNama = RolePengguna::tryFrom((string) $user->role?->nama)?->value;

        if ($roleNama && ($path === '/' . $roleNama || str_starts_with($path, '/' . $roleNama . '/'))) {
            return $intended;
        }

        return $default;
    }
}
