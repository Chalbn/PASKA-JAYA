<?php

use App\Models\AbsensiKaryawan;
use App\Models\User;
use App\Services\AbsensiService;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->service = app(AbsensiService::class);
});

test('kasir dapat menyimpan kehadiran dengan beberapa pekerjaan', function () {
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    $absensi = $this->service->simpan([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => 'pagi',
        'pekerjaan' => [
            'Mengangkut semen',
            'Menyusun keramik',
            'Membersihkan gudang',
        ],
    ], $kasir->id);

    expect($absensi)->toBeInstanceOf(AbsensiKaryawan::class)
        ->and($absensi->detailPekerjaan)->toHaveCount(3);

    $this->assertDatabaseHas('absensi_karyawan', [
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'dibuat_oleh' => $kasir->id,
    ]);

    $this->assertDatabaseCount('detail_pekerjaan_absensi', 3);
});

test('karyawan yang sama tidak boleh memiliki dua kehadiran pada tanggal yang sama', function () {
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    $data = [
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => 'pagi',
        'pekerjaan' => ['Mengangkut semen'],
    ];

    $this->service->simpan($data, $kasir->id);

    expect(fn () => $this->service->simpan($data, $kasir->id))
        ->toThrow(ValidationException::class);

    $this->assertDatabaseCount('absensi_karyawan', 1);
});

test('karyawan berbeda boleh memiliki kehadiran pada tanggal yang sama', function () {
    $kasir = User::factory()->kasir()->create();

    $karyawan1 = User::factory()->karyawan()->create();
    $karyawan2 = User::factory()->karyawan()->create();

    foreach ([$karyawan1, $karyawan2] as $karyawan) {
        $this->service->simpan([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-11-18',
            'shift' => null,
            'pekerjaan' => ['Merapikan gudang'],
        ], $kasir->id);
    }

    $this->assertDatabaseCount('absensi_karyawan', 2);
});

test('karyawan nonaktif tidak dapat dicatat kehadirannya', function () {
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->nonaktif()->create();

    expect(fn () => $this->service->simpan([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => null,
        'pekerjaan' => ['Membersihkan toko'],
    ], $kasir->id))->toThrow(ValidationException::class);

    $this->assertDatabaseCount('absensi_karyawan', 0);
});

test('kasir dapat memperbarui kehadiran dan pekerjaan', function () {
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    $absensi = $this->service->simpan([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => 'pagi',
        'pekerjaan' => ['Mengangkut semen'],
    ], $kasir->id);

    $updated = $this->service->update($absensi->id, [
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => 'siang',
        'pekerjaan' => [
            'Menyusun keramik',
            'Membersihkan gudang',
        ],
    ]);

    expect($updated->shift)->toBe('siang')
        ->and($updated->detailPekerjaan)->toHaveCount(2)
        ->and($updated->detailPekerjaan->pluck('deskripsi')->all())
        ->toBe(['Menyusun keramik', 'Membersihkan gudang']);
});

test('kasir dapat menghapus kehadiran beserta detail pekerjaan', function () {
    $kasir = User::factory()->kasir()->create();
    $karyawan = User::factory()->karyawan()->create();

    $absensi = $this->service->simpan([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => 'pagi',
        'pekerjaan' => [
            'Mengangkut semen',
            'Menyusun keramik',
        ],
    ], $kasir->id);

    $this->service->hapus($absensi->id);

    $this->assertDatabaseMissing('absensi_karyawan', [
        'id' => $absensi->id,
    ]);

    $this->assertDatabaseCount('detail_pekerjaan_absensi', 0);
});

test('filter pencarian dapat menemukan nama karyawan dan pekerjaan', function () {
    $kasir = User::factory()->kasir()->create();

    $karyawan = User::factory()->karyawan()->create([
        'nama' => 'Andi Pratama',
    ]);

    $this->service->simpan([
        'karyawan_id' => $karyawan->id,
        'tanggal' => '2026-11-18',
        'shift' => 'pagi',
        'pekerjaan' => ['Mengangkut semen'],
    ], $kasir->id);

    $hasilNama = $this->service->getDaftarAbsensi([
        'cari' => 'Andi Pratama',
    ]);

    $hasilPekerjaan = $this->service->getDaftarAbsensi([
        'cari' => 'Mengangkut semen',
    ]);

    expect($hasilNama->total())->toBe(1)
        ->and($hasilPekerjaan->total())->toBe(1);
});
