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

        if (! $user || ! $user->role) {
            abort(403, 'Akses ditolak.');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
