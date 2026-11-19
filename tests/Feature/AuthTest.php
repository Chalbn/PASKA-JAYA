<?php

use App\Enums\RolePengguna;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('halaman login dan register dapat dibuka tanpa kotak demo account', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store, private')
        ->assertSee('Welcome back')
        ->assertSee('Email or phone number')
        ->assertSee('Forgot password?')
        ->assertDontSee('Demo account')
        ->assertDontSee('Demo accounts');

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create Account')
        ->assertSee('Back to Login');
});

test('pengguna dapat login melalui controller dengan email dan diarahkan sesuai rolenya', function (RolePengguna $role, string $tujuan) {
    $user = User::factory()->{$role->value}()->create([
        'password' => Hash::make('password123'),
    ]);

    $this->post(route('login.attempt'), [
        'login' => $user->email,
        'password' => 'password123',
    ])->assertRedirect($tujuan);

    $this->assertAuthenticatedAs($user);
})->with([
    [RolePengguna::Admin, '/admin'],
    [RolePengguna::Owner, '/owner'],
    [RolePengguna::Kasir, '/kasir'],
    [RolePengguna::Karyawan, '/karyawan/pengantaran'],
]);

test('pengguna baru dapat mendaftar melalui controller dengan role karyawan', function () {
    config(['auth.otp_enabled' => false]);

    $this->post(route('register.store'), [
        'nama' => 'Budi Santoso',
        'email' => 'budi@paskajaya.test',
        'no_hp' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'terms' => '1',
    ])->assertRedirect('/karyawan/pengantaran');

    $this->assertDatabaseHas('users', [
        'email' => 'budi@paskajaya.test',
        'nama' => 'Budi Santoso',
    ]);
});
