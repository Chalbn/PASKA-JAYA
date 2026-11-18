<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Pengguna;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Models\User;
use App\Services\PenggunaService;
use App\Support\NomorHp;
use Closure;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use ValueError;

/**
 * Manajemen Pengguna (F03) — khusus Admin.
 */
#[Layout('components.layouts.app')]
#[Title('Manajemen Pengguna')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'cari', except: '')]
    public string $search = '';

    // ── State Modal Tambah / Edit ──
    public bool $showFormModal = false;

    public ?int $editId = null;

    public string $nama = '';

    public string $email = '';

    public string $no_hp = '';

    public string $role = RolePengguna::Karyawan->value;

    public string $password = '';

    // ── State Modal Konfirmasi Hapus ──
    public bool $showHapusModal = false;

    public ?int $hapusId = null;

    public string $hapusNama = '';

    public string $hapusEmail = '';

    // Dipakai pada wire:key dropdown role agar DOM kembali sinkron jika perubahan ditolak server.
    public int $tabelVersi = 0;

    public function mount(): void
    {
        Gate::authorize('viewAny', User::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function bukaModalTambah(): void
    {
        Gate::authorize('create', User::class);

        $this->resetValidation();
        $this->editId = null;
        $this->nama = '';
        $this->email = '';
        $this->no_hp = '';
        $this->role = RolePengguna::Karyawan->value;
        $this->password = '';
        $this->showFormModal = true;
    }

    public function bukaModalEdit(int $userId): void
    {
        $target = User::query()->with('role')->findOrFail($userId);
        Gate::authorize('update', $target);

        $this->resetValidation();
        $this->editId = $target->id;
        $this->nama = $target->nama;
        $this->email = $target->email;
        $this->no_hp = (string) $target->no_hp;
        $this->role = (string) ($target->role?->nama ?? RolePengguna::Karyawan->value);
        $this->password = '';
        $this->showFormModal = true;
    }

    public function simpan(PenggunaService $service): void
    {
        $actor = $this->penggunaAktif();
        $this->nama = trim($this->nama);
        $this->email = trim($this->email);
        $this->no_hp = trim($this->no_hp);

        $isEdit = $this->editId !== null;
        $target = $isEdit ? User::query()->with('role')->findOrFail($this->editId) : null;

        if ($isEdit && $target !== null) {
            Gate::authorize('update', $target);

            if ($this->role !== (string) $target->role?->nama) {
                Gate::authorize('ubahRole', $target);
            }
        } else {
            Gate::authorize('create', User::class);
        }

        $editId = $this->editId;

        $this->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($editId),
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
                function (string $attribute, mixed $value, Closure $fail) use ($editId): void {
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
                        ->when($editId !== null, fn ($q) => $q->whereKeyNot($editId))
                        ->exists();

                    if ($sudahAda) {
                        $fail('Nomor HP sudah digunakan oleh akun lain.');
                    }
                },
            ],
            'role' => ['required', Rule::enum(RolePengguna::class)],
            'password' => $isEdit
                ? ['nullable', 'string', 'min:8', 'max:100']
                : ['required', 'string', 'min:8', 'max:100'],
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
            if ($isEdit && $target !== null) {
                $payload = [
                    'nama' => $this->nama,
                    'email' => $this->email,
                    'no_hp' => $this->no_hp !== '' ? $this->no_hp : null,
                ];

                if (! $actor->is($target)) {
                    $payload['role'] = $this->role;
                    $payload['password'] = $this->password !== '' ? $this->password : null;
                }

                $service->perbarui($actor, $target, $payload);
                $pesan = 'Data pengguna berhasil diperbarui.';
            } else {
                $service->buat($actor, [
                    'nama' => $this->nama,
                    'email' => $this->email,
                    'no_hp' => $this->no_hp !== '' ? $this->no_hp : null,
                    'role' => $this->role,
                    'password' => $this->password,
                ]);
                $pesan = 'Pengguna baru berhasil ditambahkan.';
            }

            $this->showFormModal = false;
            $this->reset(['editId', 'nama', 'email', 'no_hp', 'password']);
            $this->tabelVersi++;
            $this->dispatch('toast', message: $pesan, type: 'success');
        } catch (DomainException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function ubahRole(int $userId, string $roleBaru, PenggunaService $service): void
    {
        $actor = $this->penggunaAktif();
        $target = User::query()->with('role')->findOrFail($userId);

        Gate::authorize('ubahRole', $target);

        try {
            $enumRole = RolePengguna::from($roleBaru);
            $service->ubahRole($actor, $target, $enumRole);
            $this->tabelVersi++;
            $this->dispatch('toast', message: "Role {$target->nama} diubah ke {$enumRole->label()}.", type: 'success');
        } catch (ValueError) {
            $this->tabelVersi++;
            $this->dispatch('toast', message: 'Role tidak valid.', type: 'error');
        } catch (DomainException $e) {
            $this->tabelVersi++;
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function toggleStatus(int $userId, PenggunaService $service): void
    {
        $actor = $this->penggunaAktif();
        $target = User::query()->with('role')->findOrFail($userId);

        Gate::authorize('ubahStatus', $target);

        $statusBaru = $target->isAktif()
            ? StatusPengguna::Nonaktif
            : StatusPengguna::Aktif;

        try {
            $service->ubahStatus($actor, $target, $statusBaru);
            $pesan = $statusBaru === StatusPengguna::Aktif
                ? "Akun {$target->nama} berhasil diaktifkan."
                : "Akun {$target->nama} berhasil dinonaktifkan.";

            $this->dispatch('toast', message: $pesan, type: 'success');
        } catch (DomainException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function konfirmasiHapus(int $userId): void
    {
        $target = User::query()->findOrFail($userId);
        Gate::authorize('delete', $target);

        $this->hapusId = $target->id;
        $this->hapusNama = $target->nama;
        $this->hapusEmail = $target->email;
        $this->showHapusModal = true;
    }

    public function hapusPengguna(PenggunaService $service): void
    {
        if ($this->hapusId === null) {
            return;
        }

        $actor = $this->penggunaAktif();
        $target = User::query()->with('role')->findOrFail($this->hapusId);

        Gate::authorize('delete', $target);

        try {
            $service->hapus($actor, $target);
            $this->showHapusModal = false;
            $this->reset(['hapusId', 'hapusNama', 'hapusEmail']);
            $this->dispatch('toast', message: 'Pengguna berhasil dihapus.', type: 'success');
        } catch (DomainException $e) {
            $this->showHapusModal = false;
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function render(): View
    {
        Gate::authorize('viewAny', User::class);

        $cari = trim($this->search);

        $daftarPengguna = User::query()
            ->with('role')
            ->when($cari !== '', function ($query) use ($cari): void {
                $pola = '%'.$cari.'%';
                $query->where(fn ($sub) => $sub->where('nama', 'like', $pola)->orWhere('email', 'like', $pola));
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.admin.pengguna.index', [
            'daftarPengguna' => $daftarPengguna,
            'daftarRole' => RolePengguna::cases(),
            'actorId' => (int) Auth::id(),
        ]);
    }

    private function penggunaAktif(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
