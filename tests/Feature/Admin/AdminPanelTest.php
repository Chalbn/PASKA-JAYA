<?php

use App\Enums\RolePengguna;
use App\Models\User;

test('otorisasi admin panel: hanya admin yang diizinkan dan tamu diarahkan ke login', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/pengguna')->assertRedirect('/login');
    $this->get('/admin/tim')->assertRedirect('/login');

    foreach ([RolePengguna::Owner, RolePengguna::Kasir, RolePengguna::Karyawan] as $role) {
        $user = User::factory()->{$role->value}()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/pengguna')->assertForbidden();
        $this->actingAs($user)->get('/admin/tim')->assertForbidden();
    }

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Ringkasan Sistem');
    $this->actingAs($admin)->get('/admin/pengguna')->assertOk()->assertSee('Manajemen Pengguna');
    $this->actingAs($admin)->get('/admin/tim')->assertOk()->assertSee('Tim');
});

test('admin dapat menambah, mengubah role, dan menonaktifkan pengguna melalui controller', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.pengguna.store'), [
            'nama' => 'Kasir Baru',
            'email' => 'kasirbaru@paskajaya.test',
            'no_hp' => '081234567891',
            'role' => 'kasir',
            'password' => 'password123',
        ])
        ->assertRedirect(route('admin.pengguna'));

    $target = User::where('email', 'kasirbaru@paskajaya.test')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('admin.pengguna.role', $target->id), ['role' => 'owner'])
        ->assertRedirect();

    expect($target->fresh()->role->nama)->toBe('owner');
});
