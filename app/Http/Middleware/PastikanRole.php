<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pembatas role di server (ADR-009): `role:admin` atau `role:owner,kasir`.
 * Menyembunyikan menu BUKAN otorisasi — middleware inilah yang menolak dengan 403.
 */
class PastikanRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        $user->loadMissing('role');

        abort_unless(
            in_array($user->role?->nama, $roles, true),
            403,
            'Anda tidak memiliki akses ke halaman ini.'
        );

        return $next($request);
    }
}
