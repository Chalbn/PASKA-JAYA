<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\StatusPengguna;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request, ActivityLogService $audit): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->withErrors(['login' => 'Gagal masuk dengan Google. Silakan coba lagi.']);
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $roleKaryawan = Role::where('nama', 'karyawan')->firstOrFail();

            $user = User::create([
                'role_id' => $roleKaryawan->id,
                'nama' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'password' => Str::random(32),
                'status' => StatusPengguna::Aktif,
                'email_verified_at' => now(),
            ]);
        } else {
            if (! $user->isAktif()) {
                return redirect()->route('login')
                    ->withErrors(['login' => 'Akun Anda dinonaktifkan. Hubungi admin.']);
            }

            if ($user->email_verified_at === null) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        }

        Auth::login($user);
        $request->session()->regenerate();
        $audit->catat('login', 'auth', 'Login berhasil melalui Google', $user, $user->id);

        return redirect()->to($user->urlBeranda());
    }
}