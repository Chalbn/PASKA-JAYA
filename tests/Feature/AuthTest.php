<?php

use App\Enums\RolePengguna;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\LupaPassword;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifikasiOtp;
use App\Mail\KodeOtpMail;
use App\Models\ActivityLog;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

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

test('pengguna dapat login dengan email dan diarahkan sesuai rolenya', function (RolePengguna $role, string $tujuan) {
    $user = User::factory()->create([
        'role_id' => User::factory()->{$role->value}()->make()->role_id,
        'password' => 'rahasia123',
    ]);

    Livewire::test(Login::class)
        ->set('login', $user->email)
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertHasNoErrors()
        ->assertRedirect($tujuan);

    $this->assertAuthenticatedAs($user);

    expect(
        ActivityLog::query()
            ->where('user_id', $user->id)
            ->where('aksi', 'login')
            ->exists()
    )->toBeTrue();
})->with([
    'admin' => [RolePengguna::Admin, '/admin'],
    'owner' => [RolePengguna::Owner, '/owner'],
    'kasir' => [RolePengguna::Kasir, '/kasir'],
    'karyawan' => [RolePengguna::Karyawan, '/karyawan/pengantaran'],
]);

test('pengguna dapat login dengan nomor hp termasuk format +62 atau spasi', function () {
    $kasir = User::factory()->kasir()->create([
        'no_hp' => '081234567890',
        'password' => 'rahasia123',
    ]);

    Livewire::test(Login::class)
        ->set('login', '+62 812-3456-7890')
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertHasNoErrors()
        ->assertRedirect('/kasir');

    $this->assertAuthenticatedAs($kasir);
});

test('login dengan password salah ditolak dan tidak mengautentikasi pengguna', function () {
    $owner = User::factory()->owner()->create([
        'password' => 'passwordbenar',
    ]);

    Livewire::test(Login::class)
        ->set('login', $owner->email)
        ->set('password', 'passwordsalah')
        ->call('masuk')
        ->assertHasErrors(['login']);

    $this->assertGuest();
});

test('akun nonaktif ditolak saat login dengan pesan jelas', function () {
    $user = User::factory()->kasir()->nonaktif()->create([
        'password' => 'rahasia123',
    ]);

    Livewire::test(Login::class)
        ->set('login', $user->email)
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertHasErrors(['login']);

    $this->assertGuest();
});

test('middleware pastikan aktif mengeluarkan pengguna yang dinonaktifkan saat sesi masih ada', function () {
    $user = User::factory()->owner()->nonaktif()->create();

    $this->actingAs($user)
        ->get('/owner')
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun Anda dinonaktifkan. Hubungi admin.');

    $this->assertGuest();
});

test('login dibatasi maksimal 5 percobaan per menit', function () {
    $user = User::factory()->kasir()->create([
        'password' => 'passwordbenar',
    ]);

    for ($i = 0; $i < 5; $i++) {
        Livewire::test(Login::class)
            ->set('login', $user->email)
            ->set('password', 'passwordsalah')
            ->call('masuk')
            ->assertHasErrors(['login']);
    }

    // Percobaan ke-6 tetap diblokir meski password benar
    Livewire::test(Login::class)
        ->set('login', $user->email)
        ->set('password', 'passwordbenar')
        ->call('masuk')
        ->assertHasErrors(['login']);

    $this->assertGuest();
});

test('pengguna yang sudah login sebagai owner dapat kembali ke halaman login dan mengganti akun', function () {
    $owner = User::factory()->owner()->create(['nama' => 'Owner Toko']);
    $kasir = User::factory()->kasir()->create(['password' => 'rahasia123']);

    $this->actingAs($owner)
        ->get('/owner')
        ->assertOk();

    // Kembali ke /login saat sudah login TIDAK memaksa redirect kembali ke /owner
    $this->actingAs($owner)
        ->get(route('login'))
        ->assertOk()
        ->assertSee('Owner Toko')
        ->assertSee('Welcome back');

    // Dapat langsung login sebagai kasir dari halaman login
    $this->actingAs($owner);

    Livewire::test(Login::class)
        ->set('login', $kasir->email)
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertHasNoErrors()
        ->assertRedirect('/kasir');

    $this->assertAuthenticatedAs($kasir);
});

test('url intended lintas role tidak menyebabkan error 403 setelah login', function () {
    $owner = User::factory()->owner()->create(['password' => 'rahasia123']);

    session(['url.intended' => url('/admin/pengguna')]);

    Livewire::test(Login::class)
        ->set('login', $owner->email)
        ->set('password', 'rahasia123')
        ->call('masuk')
        ->assertRedirect('/owner');
});

test('register valid membuat akun karyawan aktif dan mengarahkan ke pengantaran', function () {
    config(['auth.otp_enabled' => false]);

    Livewire::test(Register::class)
        ->set('nama', 'Budi Pengantar')
        ->set('email', 'budi.baru@paskajaya.test')
        ->set('no_hp', '0812 8888 9999')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('terms', true)
        ->call('daftar')
        ->assertHasNoErrors()
        ->assertRedirect('/karyawan/pengantaran');

    $user = User::query()->where('email', 'budi.baru@paskajaya.test')->first();

    expect($user)->not->toBeNull()
        ->and($user->no_hp)->toBe('081288889999')
        ->and($user->hasRole(RolePengguna::Karyawan))->toBeTrue()
        ->and($user->isAktif())->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('register invalid ditolak dengan pesan validasi', function () {
    User::factory()->create([
        'email' => 'terdaftar@paskajaya.test',
        'no_hp' => '081211112222',
    ]);

    Livewire::test(Register::class)
        ->set('nama', '')
        ->set('email', 'terdaftar@paskajaya.test')
        ->set('no_hp', '12345')
        ->set('password', 'pendek')
        ->set('password_confirmation', 'tidak-sama')
        ->set('terms', false)
        ->call('daftar')
        ->assertHasErrors(['nama', 'email', 'no_hp', 'password', 'password_confirmation', 'terms']);

    $this->assertGuest();
});

test('register dengan otp aktif menyimpan kode terhash dan dapat diverifikasi dalam 10 menit', function () {
    Mail::fake();
    config(['auth.otp_enabled' => true]);

    $kodeTerkirim = null;

    Mail::assertNothingSent();

    Livewire::test(Register::class)
        ->set('nama', 'Siti Karyawan')
        ->set('email', 'siti.otp@paskajaya.test')
        ->set('no_hp', '081355556666')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('terms', true)
        ->call('daftar')
        ->assertHasNoErrors()
        ->assertRedirect(route('otp.verify'));

    $this->assertGuest();

    $user = User::query()->where('email', 'siti.otp@paskajaya.test')->firstOrFail();

    Mail::assertSent(KodeOtpMail::class, function (KodeOtpMail $mail) use (&$kodeTerkirim, $user): bool {
        $kodeTerkirim = $mail->kode;

        return $mail->hasTo($user->email) && strlen($mail->kode) === 6;
    });

    $otp = OtpCode::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
    expect($otp->kode_hash)->not->toBe($kodeTerkirim)
        ->and(Hash::check($kodeTerkirim, $otp->kode_hash))->toBeTrue();

    // Cooldown 60 detik menahan kirim ulang langsung
    Livewire::test(VerifikasiOtp::class)
        ->call('kirimUlang')
        ->assertHasErrors(['kode']);

    // Kode salah ditolak
    Livewire::test(VerifikasiOtp::class)
        ->set('kode', '000000')
        ->call('verifikasi')
        ->assertHasErrors(['kode']);

    // Kode benar memverifikasi email dan login ke /karyawan/pengantaran
    Livewire::test(VerifikasiOtp::class)
        ->set('kode', $kodeTerkirim)
        ->call('verifikasi')
        ->assertHasNoErrors()
        ->assertRedirect('/karyawan/pengantaran');

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

test('otp yang sudah kedaluwarsa lebih dari 10 menit ditolak', function () {
    config(['auth.otp_enabled' => true]);

    $user = User::factory()->karyawan()->unverified()->create();

    $user->otpCodes()->create([
        'kode_hash' => Hash::make('123456'),
        'kedaluwarsa_pada' => now()->subMinute(),
    ]);

    expect(app(OtpService::class)->verifikasi($user, '123456'))
        ->toBe(OtpService::KEDALUWARSA);
});

test('lupa password dan reset password bekerja dengan token standar laravel', function () {
    $user = User::factory()->kasir()->create([
        'password' => 'passwordlama123',
    ]);

    Livewire::test(LupaPassword::class)
        ->set('email', $user->email)
        ->call('kirimTautan')
        ->assertHasNoErrors()
        ->assertSet('terkirim', true);

    $token = Password::broker()->createToken($user);

    Livewire::test(LupaPassword::class, ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'passwordbaru123')
        ->set('password_confirmation', 'passwordbaru123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check('passwordbaru123', $user->fresh()->password))->toBeTrue();
});

test('logout hanya via post dan mencatat ke activity_logs', function () {
    $owner = User::factory()->owner()->create();

    $this->actingAs($owner)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();

    expect(
        ActivityLog::query()
            ->where('user_id', $owner->id)
            ->where('aksi', 'logout')
            ->exists()
    )->toBeTrue();
});

test('matriks akses lintas role menolak dengan 403 dan mengizinkan role yang sesuai', function (
    RolePengguna $roleLogin,
    string $url,
    int $expectedStatus,
) {
    $user = User::factory()->{$roleLogin->value}()->create();

    $this->actingAs($user)
        ->get($url)
        ->assertStatus($expectedStatus);
})->with([
    'admin buka /admin' => [RolePengguna::Admin, '/admin', 200],
    'admin buka /owner' => [RolePengguna::Admin, '/owner', 403],
    'owner buka /owner' => [RolePengguna::Owner, '/owner', 200],
    'owner buka /admin' => [RolePengguna::Owner, '/admin', 403],
    'owner buka /kasir' => [RolePengguna::Owner, '/kasir', 403],
    'kasir buka /kasir' => [RolePengguna::Kasir, '/kasir', 200],
    'kasir buka /owner' => [RolePengguna::Kasir, '/owner', 403],
    'karyawan buka /karyawan/pengantaran' => [RolePengguna::Karyawan, '/karyawan/pengantaran', 200],
    'karyawan buka /kasir' => [RolePengguna::Karyawan, '/kasir', 403],
    'karyawan buka /admin' => [RolePengguna::Karyawan, '/admin', 403],
]);

test('tamu yang mengakses halaman terproteksi diarahkan ke login', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with([
    '/admin',
    '/owner',
    '/kasir',
    '/karyawan/pengantaran',
    '/owner/profil',
]);
