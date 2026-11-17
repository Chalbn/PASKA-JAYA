<?php

use App\Models\User;

test('halaman utama mengarahkan tamu ke halaman login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('halaman utama mengarahkan pengguna terautentikasi ke beranda rolenya', function () {
    $owner = User::factory()->owner()->create();

    $response = $this->actingAs($owner)->get('/');

    $response->assertRedirect('/owner');
});
