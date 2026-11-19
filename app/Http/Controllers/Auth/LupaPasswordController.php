<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LupaPasswordController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public function showRequestForm(): View
    {
        return view('auth.lupa-password', [
            'token' => null,
            'email' => '',
            'terkirim' => (bool) session('terkirim', false),
        ]);
    }

    public function sendResetLink(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:150'],
        ]);

        $this->pastikanBelumTerblokir($request, 'lupa-password');
        RateLimiter::hit($this->kunciLimiter($request, 'lupa-password'), self::DETIK_BLOKIR);

        try {
            Password::sendResetLink(['email' => Str::lower(trim($validated['email']))]);
        } catch (Throwable $e) {
            report($e);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Jika email tersebut terdaftar, tautan reset password sudah kami kirim.',
            ]);
        }

        return redirect()->route('password.request')->with('terkirim', true);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.lupa-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
            'terkirim' => false,
        ]);
    }

    public function resetPassword(Request $request, ActivityLogService $audit): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $this->pastikanBelumTerblokir($request, 'reset-password');

        $pengguna = null;

        $status = Password::reset(
            [
                'email' => Str::lower(trim($validated['email'])),
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
                'token' => $validated['token'],
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
            RateLimiter::hit($this->kunciLimiter($request, 'reset-password'), self::DETIK_BLOKIR);

            $pesan = 'Tautan reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan], 422);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => $pesan]);
        }

        $audit->catat('ubah', 'pengguna', 'Reset password lewat email', $pengguna, $pengguna?->id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Password berhasil diubah. Silakan login dengan password baru.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    private function kunciLimiter(Request $request, string $aksi): string
    {
        return $aksi . '|' . $request->ip();
    }

    private function pastikanBelumTerblokir(Request $request, string $aksi): void
    {
        $kunci = $this->kunciLimiter($request, $aksi);

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.",
            ]);
        }
    }
}
