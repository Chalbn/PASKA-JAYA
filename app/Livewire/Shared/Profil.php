<?php

namespace App\Livewire\Shared;

use App\Enums\RolePengguna;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Support\NomorHp;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

/**
 * Profil Saya (F10) — dipakai semua role pada /{role}/profil.
 * Komponen hanya pernah menyentuh pengguna yang sedang login; tidak ada id dari klien.
 */
#[Layout('components.layouts.app')]
#[Title('Profil Saya')]
class Profil extends Component
{
    use WithFileUploads;

    private const MAKS_PERCOBAAN = 5;

    private const DETIK_BLOKIR = 60;

    // Data Profil
    public string $nama = '';

    public string $no_hp = '';

    public string $alamat = '';

    public ?TemporaryUploadedFile $foto = null;

    // Ubah Password
    public string $password_lama = '';

    public string $password_baru = '';

    public string $password_baru_konfirmasi = '';

    // Hapus Akun
    public string $password_hapus = '';

    public function mount(): void
    {
        $user = $this->pengguna();

        $this->nama = $user->nama;
        $this->no_hp = (string) $user->no_hp;
        $this->alamat = (string) $user->alamat;
    }

    /**
     * Validasi foto begitu dipilih; berkas yang ditolak langsung dibuang dari state.
     */
    public function updatedFoto(): void
    {
        try {
            $this->validateOnly('foto');
        } catch (ValidationException $e) {
            $this->foto = null;

            throw $e;
        }
    }

    public function simpanProfil(): void
    {
        $user = $this->pengguna();

        $this->nama = trim($this->nama);
        $this->no_hp = trim($this->no_hp);

        $this->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20', $this->aturanNomorHp($user)],
            'alamat' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $fotoLama = $user->foto_profil;
        $fotoBaru = null;

        if ($this->foto) {
            // Nama berkas acak (hashName) — nama asli dari klien tidak dipakai.
            $fotoBaru = $this->foto->store('foto-profil', 'public');
            $user->foto_profil = $fotoBaru;
        }

        $user->nama = $this->nama;
        $user->no_hp = $this->no_hp !== '' ? NomorHp::normalisasi($this->no_hp) : null;
        $user->alamat = trim($this->alamat) !== '' ? trim($this->alamat) : null;

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

        $this->foto = null;
        $this->no_hp = (string) $user->no_hp;

        $this->dispatch(
            'profil-diperbarui',
            nama: $user->nama,
            inisial: inisial($user->nama),
            foto: $user->fotoUrl(),
        );
        $this->dispatch('toast', message: 'Profil berhasil diperbarui.', type: 'success');
    }

    public function ubahPassword(ActivityLogService $audit): void
    {
        $user = $this->pengguna();

        $this->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:8', 'max:255'],
            'password_baru_konfirmasi' => ['required', 'same:password_baru'],
        ]);

        $kunci = $this->kunciLimiter('ubah-password', $user);
        $this->pastikanBelumTerblokir($kunci, 'password_lama');

        if (! Hash::check($this->password_lama, $user->password)) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);
            $this->addError('password_lama', 'Password lama tidak sesuai.');

            return;
        }

        RateLimiter::clear($kunci);

        $user->password = $this->password_baru;
        $user->setRememberToken(Str::random(60));
        $user->save();

        $audit->catat('ubah', 'pengguna', 'Mengubah password sendiri', $user, $user->id);

        $this->reset('password_lama', 'password_baru', 'password_baru_konfirmasi');
        $this->dispatch('toast', message: 'Password berhasil diubah.', type: 'success');
    }

    public function hapusAkun(ActivityLogService $audit): void
    {
        $user = $this->pengguna();

        $this->validate(['password_hapus' => ['required', 'string']]);

        $kunci = $this->kunciLimiter('hapus-akun', $user);
        $this->pastikanBelumTerblokir($kunci, 'password_hapus');

        if (! Hash::check($this->password_hapus, $user->password)) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);
            $this->addError('password_hapus', 'Password tidak sesuai.');

            return;
        }

        if ($this->adalahAdminAktifTerakhir($user)) {
            $this->addError('password_hapus', 'Anda admin aktif terakhir. Akun ini tidak dapat dihapus agar sistem tetap punya admin.');

            return;
        }

        $fotoLama = $user->foto_profil;

        try {
            DB::transaction(function () use ($user, $audit): void {
                $audit->catat('hapus', 'pengguna', 'Menghapus akun sendiri', $user, $user->id);
                // Soft delete (users.deleted_at): riwayat transaksi & audit tetap utuh.
                $user->delete();
            });
        } catch (QueryException $e) {
            // Pengaman terakhir dari trigger T7 bila terjadi balapan dengan perubahan admin lain.
            if (str_contains($e->getMessage(), 'Admin terakhir')) {
                $this->addError('password_hapus', 'Anda admin aktif terakhir. Akun ini tidak dapat dihapus.');

                return;
            }

            throw $e;
        }

        if ($fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        session()->flash('success', 'Akun Anda telah dihapus.');

        $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.shared.profil', [
            'user' => $this->pengguna(),
        ]);
    }

    private function pengguna(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->loadMissing('role');
    }

    /**
     * Nomor HP harus berformat seluler Indonesia dan tidak dipakai pengguna lain.
     */
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

    private function kunciLimiter(string $aksi, User $user): string
    {
        return $aksi.'|'.$user->id.'|'.request()->ip();
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
