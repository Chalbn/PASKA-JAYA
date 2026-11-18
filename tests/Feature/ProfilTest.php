<?php

use App\Enums\RolePengguna;
use App\Livewire\Shared\Profil;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('role non-admin dapat membuka halaman profil masing-masing dan admin dialihkan ke tim', function (RolePengguna $role) {
    $user = User::factory()->{$role->value}()->create();

    $this->actingAs($user)
        ->get("/{$role->value}/profil")
        ->assertOk()
        ->assertSee('Profil Saya')
        ->assertSee($user->nama)
        ->assertSee($user->email);
})->with([
    RolePengguna::Owner,
    RolePengguna::Kasir,
    RolePengguna::Karyawan,
]);

test('admin yang membuka rute profil diarahkan ke fitur tim', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin/profil')
        ->assertRedirect(route('admin.tim'));
});

test('pengguna dapat memperbarui profil termasuk unggah foto dan menghapus foto lama', function () {
    Storage::fake('public');

    $owner = User::factory()->owner()->create([
        'foto_profil' => 'foto-profil/lama.jpg',
    ]);
    Storage::disk('public')->put('foto-profil/lama.jpg', 'dummy');

    $fotoBaru = UploadedFile::fake()->image('profil.jpg', 300, 300);

    Livewire::actingAs($owner)
        ->test(Profil::class)
        ->set('nama', 'Pak Hendra Diperbarui')
        ->set('no_hp', '+62 812-9999-0000')
        ->set('alamat', 'Jl. Jamin Ginting No. 12, Medan')
        ->set('foto', $fotoBaru)
        ->call('simpanProfil')
        ->assertHasNoErrors()
        ->assertDispatched('profil-diperbarui')
        ->assertDispatched('toast');

    $fresh = $owner->fresh();

    expect($fresh->nama)->toBe('Pak Hendra Diperbarui')
        ->and($fresh->no_hp)->toBe('081299990000')
        ->and($fresh->alamat)->toBe('Jl. Jamin Ginting No. 12, Medan')
        ->and($fresh->foto_profil)->not->toBeNull()
        ->and($fresh->foto_profil)->not->toBe('foto-profil/lama.jpg');

    Storage::disk('public')->assertMissing('foto-profil/lama.jpg');
    Storage::disk('public')->assertExists($fresh->foto_profil);
});

test('validasi profil menolak nama kosong dan format nomor hp salah', function () {
    $kasir = User::factory()->kasir()->create();

    Livewire::actingAs($kasir)
        ->test(Profil::class)
        ->set('nama', '')
        ->set('no_hp', 'bukan-nomor')
        ->call('simpanProfil')
        ->assertHasErrors(['nama', 'no_hp']);
});

test('ubah password wajib password lama benar dan minimal 8 karakter', function () {
    $kasir = User::factory()->kasir()->create([
        'password' => 'passwordlama123',
    ]);

    Livewire::actingAs($kasir)
        ->test(Profil::class)
        ->set('password_lama', 'salah123')
        ->set('password_baru', 'passwordbaru123')
        ->set('password_baru_konfirmasi', 'passwordbaru123')
        ->call('ubahPassword')
        ->assertHasErrors(['password_lama']);

    Livewire::actingAs($kasir)
        ->test(Profil::class)
        ->set('password_lama', 'passwordlama123')
        ->set('password_baru', 'pendek')
        ->set('password_baru_konfirmasi', 'pendek')
        ->call('ubahPassword')
        ->assertHasErrors(['password_baru']);

    Livewire::actingAs($kasir)
        ->test(Profil::class)
        ->set('password_lama', 'passwordlama123')
        ->set('password_baru', 'passwordbaru123')
        ->set('password_baru_konfirmasi', 'passwordbaru123')
        ->call('ubahPassword')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    expect(Hash::check('passwordbaru123', $kasir->fresh()->password))->toBeTrue();
});

test('hapus akun membutuhkan password yang benar dan tidak boleh menghapus admin terakhir', function () {
    $admin = User::factory()->admin()->create([
        'password' => 'adminrahasia123',
    ]);

    // Password salah ditolak
    Livewire::actingAs($admin)
        ->test(Profil::class)
        ->set('password_hapus', 'passwordsalah')
        ->call('hapusAkun')
        ->assertHasErrors(['password_hapus']);

    // Admin aktif terakhir tidak boleh dihapus
    Livewire::actingAs($admin)
        ->test(Profil::class)
        ->set('password_hapus', 'adminrahasia123')
        ->call('hapusAkun')
        ->assertHasErrors(['password_hapus']);

    expect($admin->fresh()->deleted_at)->toBeNull();

    // Pengguna non-admin dapat menghapus akunnya sendiri dengan password benar
    $karyawan = User::factory()->karyawan()->create([
        'password' => 'karyawan123',
    ]);

    Livewire::actingAs($karyawan)
        ->test(Profil::class)
        ->set('password_hapus', 'karyawan123')
        ->call('hapusAkun')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertGuest();
    expect($karyawan->fresh()->deleted_at)->not->toBeNull();
});
