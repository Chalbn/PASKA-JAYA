<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shared;

use App\Enums\RolePengguna;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Support\NomorHp;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ProfilController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    public function show(Request $request): View|JsonResponse
    {
        $user = $this->pengguna();

        $profil = null;
        try {
            $profil = DB::table('v_daftar_pengguna')->where('user_id', $user->id)->first();
        } catch (Throwable) {
            $profil = $user;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'profil' => $profil,
                'user' => $user,
            ]);
        }

        return view('shared.profil', [
            'user' => $user,
            'profil' => $profil,
        ]);
    }

    public function update(Request $request, ActivityLogService $audit): JsonResponse|RedirectResponse
    {
        $user = $this->pengguna();

        $request->merge([
            'nama' => trim((string) $request->input('nama', '')),
            'no_hp' => trim((string) $request->input('no_hp', '')),
        ]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20', $this->aturanNomorHp($user)],
            'alamat' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $dataLama = [
            'nama' => $user->nama,
            'no_hp' => $user->no_hp,
            'alamat' => $user->alamat,
        ];

        $fotoLama = $user->foto_profil;
        $fotoBaru = null;

        if ($request->hasFile('foto')) {
            $fotoBaru = $request->file('foto')->store('foto-profil', 'public');
            $user->foto_profil = $fotoBaru;
        }

        $user->nama = $validated['nama'];
        $user->no_hp = $validated['no_hp'] !== '' ? NomorHp::normalisasi($validated['no_hp']) : null;
        $user->alamat = isset($validated['alamat']) && trim($validated['alamat']) !== '' ? trim($validated['alamat']) : null;

        try {
            $user->save();
        } catch (Throwable $e) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $e;
        }

        if ($fotoBaru && $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        $audit->catat(
            'ubah',
            'pengguna',
            'Memperbarui profil sendiri',
            $user,
            $user->id,
            $dataLama,
            [
                'nama' => $user->nama,
                'no_hp' => $user->no_hp,
                'alamat' => $user->alamat,
                'foto_diubah' => $fotoBaru !== null,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Profil berhasil diperbarui.']);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function ubahPassword(Request $request, ActivityLogService $audit): JsonResponse|RedirectResponse
    {
        $user = $this->pengguna();

        $validated = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:8', 'max:255'],
            'password_baru_konfirmasi' => ['required', 'same:password_baru'],
        ]);

        $kunci = $this->kunciLimiter($request, 'ubah-password', $user);
        $this->pastikanBelumTerblokir($kunci, 'password_lama');

        if (! Hash::check($validated['password_lama'], $user->password)) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Password lama tidak sesuai.'], 422);
            }

            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        RateLimiter::clear($kunci);

        $user->password = $validated['password_baru'];
        $user->setRememberToken(Str::random(60));
        $user->save();

        $audit->catat('ubah', 'pengguna', 'Mengubah password sendiri', $user, $user->id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Password berhasil diubah.']);
        }

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function hapusAkun(Request $request, ActivityLogService $audit): JsonResponse|RedirectResponse
    {
        $user = $this->pengguna();

        $validated = $request->validate([
            'password_hapus' => ['required', 'string'],
        ]);

        $kunci = $this->kunciLimiter($request, 'hapus-akun', $user);
        $this->pastikanBelumTerblokir($kunci, 'password_hapus');

        if (! Hash::check($validated['password_hapus'], $user->password)) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Password tidak sesuai.'], 422);
            }

            return back()->withErrors(['password_hapus' => 'Password tidak sesuai.']);
        }

        if ($this->adalahAdminAktifTerakhir($user)) {
            $pesan = 'Anda admin aktif terakhir. Akun ini tidak dapat dihapus agar sistem tetap punya admin.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan], 422);
            }

            return back()->withErrors(['password_hapus' => $pesan]);
        }

        $fotoLama = $user->foto_profil;

        try {
            DB::transaction(function () use ($user, $audit): void {
                $audit->catat('hapus', 'pengguna', 'Menghapus akun sendiri', $user, $user->id);
                $user->delete();
            });
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Admin terakhir')) {
                $pesan = 'Anda admin aktif terakhir. Akun ini tidak dapat dihapus.';
                if ($request->wantsJson()) {
                    return response()->json(['message' => $pesan], 422);
                }

                return back()->withErrors(['password_hapus' => $pesan]);
            }

            throw $e;
        }

        if ($fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Akun Anda telah dihapus.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', 'Akun Anda telah dihapus.');
    }

    private function pengguna(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->loadMissing('role');
    }

    private function aturanNomorHp(User $user): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($user): void {
            if (trim((string) $value) === '') {
                return;
            }

            if (! NomorHp::valid((string) $value)) {
                $fail('Format nomor HP tidak valid. Contoh: 0812 3456 7890 atau +62 812 3456 7890.');

                return;
            }

            $dipakaiLain = User::withTrashed()
                ->where('no_hp', NomorHp::normalisasi((string) $value))
                ->where('id', '!=', $user->id)
                ->exists();

            if ($dipakaiLain) {
                $fail('Nomor HP sudah dipakai pengguna lain.');
            }
        };
    }

    private function adalahAdminAktifTerakhir(User $user): bool
    {
        if (! $user->hasRole(RolePengguna::Admin) || ! $user->isAktif()) {
            return false;
        }

        return ! User::query()
            ->whereHas('role', fn ($q) => $q->where('nama', RolePengguna::Admin->value))
            ->where('status', 'aktif')
            ->where('id', '!=', $user->id)
            ->exists();
    }

    private function kunciLimiter(Request $request, string $aksi, User $user): string
    {
        return $aksi . '|' . $user->id . '|' . $request->ip();
    }

    private function pastikanBelumTerblokir(string $kunci, string $field): void
    {
        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                $field => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.",
            ]);
        }
    }
}
