<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Logout hanya lewat POST (dilindungi CSRF), sesi dibuang total.
     */
    public function __invoke(Request $request, ActivityLogService $audit): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $audit->catat('logout', 'auth', 'Logout', $user, $user->id);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
