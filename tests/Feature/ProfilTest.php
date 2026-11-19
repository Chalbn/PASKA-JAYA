<?php

use App\Enums\RolePengguna;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

test('pengguna dapat memperbarui profil melalui controller termasuk unggah foto', function () {
    Storage::fake('public');

    $owner = User::factory()->owner()->create();
    $foto = UploadedFile::fake()->image('profil.jpg', 200, 200);

    $this->actingAs($owner)
        ->put(route('owner.profil.update'), [
            'nama' => 'Hendra Wijaya Baru',
            'no_hp' => '081298765432',
            'alamat' => 'Jl. Merdeka No. 10',
            'foto' => $foto,
        ])
        ->assertRedirect();

    $owner->refresh();
    expect($owner->nama)->toBe('Hendra Wijaya Baru')
        ->and($owner->foto_profil)->not->toBeNull();
});

test('pengguna dapat mengubah password melalui controller', function () {
    $kasir = User::factory()->kasir()->create([
        'password' => Hash::make('passwordLama123'),
    ]);

    $this->actingAs($kasir)
        ->put(route('kasir.profil.password'), [
            'password_lama' => 'passwordLama123',
            'password_baru' => 'passwordBaru456',
            'password_baru_konfirmasi' => 'passwordBaru456',
        ])
        ->assertRedirect();

    expect(Hash::check('passwordBaru456', $kasir->fresh()->password))->toBeTrue();
});
