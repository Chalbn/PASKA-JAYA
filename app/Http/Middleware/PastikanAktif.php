<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Akun `nonaktif` tidak boleh memakai sesi yang sudah terlanjur login:
 * sesi dihentikan dan pengguna dikembalikan ke halaman login dengan pesan jelas.
 */
class PastikanAktif
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAktif()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang nonaktif. Hubungi admin.',
            ]);
        }

        return $next($request);
    }
}
