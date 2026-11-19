<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PenggunaService;
use App\Services\RoleNotificationService;
use App\Support\NomorHp;
use Closure;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use ValueError;

class PenggunaController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $search = trim((string) $request->input('cari', $request->input('search', '')));

        $daftarPengguna = User::query()
            ->with('role')
            ->when($search !== '', function ($query) use ($search): void {
                $pola = '%' . $search . '%';
                $query->where(fn ($sub) => $sub->where('nama', 'like', $pola)->orWhere('email', 'like', $pola));
            })
            ->when($request->filled('role'), function ($query) use ($request): void {
                $roleFilter = (string) $request->input('role');
                $query->whereHas('role', fn ($q) => $q->where('nama', $roleFilter));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', (string) $request->input('status')))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $daftarRole = RolePengguna::cases();
        $actorId = (int) Auth::id();
        $roles = DB::table('roles')->orderBy('id')->get();

        $pengguna = collect();
        try {
            $pengguna = DB::table('v_daftar_pengguna')
                ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
                ->orderBy('nama')
                ->get();
        } catch (\Throwable) {
            $pengguna = $daftarPengguna->getCollection();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'pengguna' => $pengguna,
                'roles' => $roles,
                'daftar_pengguna' => $daftarPengguna,
            ]);
        }

        return view('admin.pengguna.index', compact(
            'daftarPengguna',
            'daftarRole',
            'actorId',
            'search',
            'pengguna',
            'roles'
        ));
    }

    public function store(Request $request, PenggunaService $service): JsonResponse|RedirectResponse
    {
        Gate::authorize('create', User::class);

        /** @var User $actor */
        $actor = Auth::user();

        if ($request->filled('role_id') && ! $request->filled('role')) {
            $roleNama = DB::table('roles')->where('id', (int) $request->input('role_id'))->value('nama');
            if ($roleNama) {
                $request->merge(['role' => $roleNama]);
            }
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')],
            'no_hp' => ['nullable', 'string', 'max:20', $this->aturanNomorHp(null)],
            'role' => ['required', Rule::enum(RolePengguna::class)],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'role.required' => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        try {
            $user = $service->buat($actor, [
                'nama' => trim($validated['nama']),
                'email' => strtolower(trim($validated['email'])),
                'no_hp' => ! empty($validated['no_hp']) ? trim($validated['no_hp']) : null,
                'role' => $validated['role'],
                'password' => $validated['password'],
            ]);
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }

        try {
            app(RoleNotificationService::class)->syncForRole('admin');
        } catch (\Throwable) {
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pengguna baru berhasil ditambahkan.',
                'user_id' => $user->id,
                'inisial' => inisial($user->nama),
            ], 201);
        }

        return redirect()->route('admin.pengguna')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, int $id, PenggunaService $service): JsonResponse|RedirectResponse
    {
        $target = User::query()->with('role')->findOrFail($id);
        Gate::authorize('update', $target);

        /** @var User $actor */
        $actor = Auth::user();

        if ($request->filled('role_id') && ! $request->filled('role')) {
            $roleNama = DB::table('roles')->where('id', (int) $request->input('role_id'))->value('nama');
            if ($roleNama) {
                $request->merge(['role' => $roleNama]);
            }
        }

        $roleInput = (string) $request->input('role', $target->role?->nama ?? RolePengguna::Karyawan->value);
        if ($roleInput !== (string) $target->role?->nama) {
            Gate::authorize('ubahRole', $target);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($target->id)],
            'no_hp' => ['nullable', 'string', 'max:20', $this->aturanNomorHp($target->id)],
            'role' => ['required', Rule::enum(RolePengguna::class)],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
        ]);

        try {
            $payload = [
                'nama' => trim($validated['nama']),
                'email' => strtolower(trim($validated['email'])),
                'no_hp' => ! empty($validated['no_hp']) ? trim($validated['no_hp']) : null,
            ];

            if (! $actor->is($target)) {
                $payload['role'] = $validated['role'];
                $payload['password'] = ! empty($validated['password']) ? $validated['password'] : null;
            }

            $service->perbarui($actor, $target, $payload);
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->withInput()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Data pengguna berhasil diperbarui.']);
        }

        return redirect()->route('admin.pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function ubahRole(Request $request, int $id, PenggunaService $service): JsonResponse|RedirectResponse
    {
        /** @var User $actor */
        $actor = Auth::user();
        $target = User::query()->with('role')->findOrFail($id);

        Gate::authorize('ubahRole', $target);

        $roleBaru = (string) $request->input('role', '');

        try {
            $enumRole = RolePengguna::from($roleBaru);
            $service->ubahRole($actor, $target, $enumRole);
            $pesan = "Role {$target->nama} diubah ke {$enumRole->label()}.";

            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan]);
            }

            return back()->with('success', $pesan);
        } catch (ValueError) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Role tidak valid.'], 422);
            }

            return back()->with('error', 'Role tidak valid.');
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, int $id, PenggunaService $service): JsonResponse|RedirectResponse
    {
        /** @var User $actor */
        $actor = Auth::user();
        $target = User::query()->with('role')->findOrFail($id);

        Gate::authorize('ubahStatus', $target);

        $statusBaru = $target->isAktif()
            ? StatusPengguna::Nonaktif
            : StatusPengguna::Aktif;

        try {
            $service->ubahStatus($actor, $target, $statusBaru);
            try {
                app(RoleNotificationService::class)->syncForRole('admin');
            } catch (\Throwable) {
            }
            $pesan = $statusBaru === StatusPengguna::Aktif
                ? "Akun {$target->nama} berhasil diaktifkan."
                : "Akun {$target->nama} berhasil dinonaktifkan.";

            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan]);
            }

            return back()->with('success', $pesan);
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, int $id, PenggunaService $service): JsonResponse|RedirectResponse
    {
        /** @var User $actor */
        $actor = Auth::user();
        $target = User::query()->with('role')->findOrFail($id);

        Gate::authorize('delete', $target);

        try {
            $service->hapus($actor, $target);
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            $msg = $this->dbErrorMessage($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Pengguna berhasil dihapus.']);
        }

        return redirect()->route('admin.pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function aturanNomorHp(?int $ignoreId): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($ignoreId): void {
            $teks = trim((string) $value);
            if ($teks === '') {
                return;
            }

            if (! NomorHp::valid($teks)) {
                $fail('Format nomor HP tidak valid (contoh: 081234567890).');

                return;
            }

            $normal = NomorHp::normalisasi($teks);
            $sudahAda = User::query()
                ->where('no_hp', $normal)
                ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists();

            if ($sudahAda) {
                $fail('Nomor HP sudah digunakan oleh akun lain.');
            }
        };
    }
}
