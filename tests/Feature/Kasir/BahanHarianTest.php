<?php

namespace Tests\Feature\Kasir;

use App\Models\PenggunaanBahan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BahanHarianTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected User $owner;
    protected User $karyawan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Siapkan 4 roles referensi
        Role::insert([
            ['id' => 1, 'nama' => 'admin', 'label' => 'Admin', 'deskripsi' => null],
            ['id' => 2, 'nama' => 'owner', 'label' => 'Owner', 'deskripsi' => null],
            ['id' => 3, 'nama' => 'kasir', 'label' => 'Kasir', 'deskripsi' => null],
            ['id' => 4, 'nama' => 'karyawan', 'label' => 'Karyawan', 'deskripsi' => null],
        ]);

        $this->kasir = User::factory()->kasir()->create(['nama' => 'Kasir Rina']);
        $this->owner = User::factory()->owner()->create(['nama' => 'Owner Hendra']);
        $this->karyawan = User::factory()->karyawan()->create(['nama' => 'Karyawan Joko']);
    }

    // ── 1. OTORISASI & HAK AKSES ──

    public function test_kasir_dapat_mengakses_halaman_bahan_harian(): void
    {
        $this->actingAs($this->kasir)
            ->get(route('kasir.bahan-harian'))
            ->assertOk()
            ->assertSee('Bahan Harian')
            ->assertSee('Tambah bahan')
            ->assertSee('Daftar Bahan Harian');
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('kasir.bahan-harian'))
            ->assertRedirect(route('login'));
    }

    public function test_role_karyawan_ditolak_mengakses_bahan_harian(): void
    {
        $this->actingAs($this->karyawan)
            ->get(route('kasir.bahan-harian'))
            ->assertForbidden();
    }

    public function test_role_owner_ditolak_mengakses_halaman_tulis_kasir(): void
    {
        $this->actingAs($this->owner)
            ->get(route('kasir.bahan-harian'))
            ->assertForbidden();
    }

    // ── 2. DATABASE KOSONG & EMPTY STATE ──

    public function test_database_kosong_menampilkan_empty_state_dan_total_rp_0(): void
    {
        $this->assertEquals(0, PenggunaanBahan::count());

        $this->actingAs($this->kasir);

        Livewire::test(\App\Livewire\Kasir\BahanHarian::class)
            ->assertSee('Rp 0')
            ->assertSee('Belum ada bahan harian')
            ->assertSee('Bahan operasional harian yang dicatat kasir akan muncul di tabel ini.');
    }

    // ── 3. VALIDASI FORM & PESAN ERROR MERAH ──

    public function test_validasi_menampilkan_pesan_error_yang_diminta_saat_input_kosong(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(\App\Livewire\Kasir\BahanHarian::class)
            ->set('nama_bahan', '')
            ->set('jumlah', '')
            ->set('harga', '')
            ->call('simpan')
            ->assertHasErrors(['nama_bahan', 'jumlah', 'harga'])
            ->assertSee('Nama bahan, jumlah, dan harga wajib diisi.');

        $this->assertEquals(0, PenggunaanBahan::count());
    }

    // ── 4. TAMBAH BAHAN HARIAN (CREATE) ──

    public function test_kasir_dapat_menambah_bahan_harian_dan_pencatat_adalah_kasir_login(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(\App\Livewire\Kasir\BahanHarian::class)
            ->set('nama_bahan', 'Bensin Pick-up')
            ->set('jumlah', 10)
            ->set('satuan', 'liter')
            ->set('harga', 100000)
            ->set('tanggal', '2026-11-18')
            ->set('keterangan', 'Operasional antar bon Toko Subur')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertDatabaseHas('penggunaan_bahan', [
            'nama_bahan' => 'Bensin Pick-up',
            'satuan' => 'liter',
            'harga' => 100000,
            'keterangan' => 'Operasional antar bon Toko Subur',
            'user_id' => $this->kasir->id,
        ]);

        $this->assertEquals(1, PenggunaanBahan::count());
    }

    // ── 5. TOTAL PENGELUARAN AKURAT ──

    public function test_total_pengeluaran_akurat_sesuai_akumulasi_data_database(): void
    {
        $this->actingAs($this->kasir);

        PenggunaanBahan::factory()->create([
            'harga' => 50000,
            'user_id' => $this->kasir->id,
        ]);

        PenggunaanBahan::factory()->create([
            'harga' => 75000,
            'user_id' => $this->kasir->id,
        ]);

        PenggunaanBahan::factory()->create([
            'harga' => 25000,
            'user_id' => $this->kasir->id,
        ]);

        $totalHarapan = 50000 + 75000 + 25000; // 150000

        Livewire::test(\App\Livewire\Kasir\BahanHarian::class)
            ->assertSee('Rp 150.000')
            ->assertSet('totalPengeluaran', $totalHarapan);
    }

    // ── 6. EDIT BAHAN HARIAN (UPDATE) ──

    public function test_kasir_dapat_mengubah_data_bahan_harian(): void
    {
        $this->actingAs($this->kasir);

        $bahan = PenggunaanBahan::factory()->create([
            'nama_bahan' => 'Tali Rafia Lama',
            'jumlah' => 2,
            'satuan' => 'roll',
            'harga' => 20000,
            'user_id' => $this->kasir->id,
        ]);

        Livewire::test(\App\Livewire\Kasir\BahanHarian::class)
            ->call('edit', $bahan->id)
            ->assertSet('editId', $bahan->id)
            ->assertSet('nama_bahan', 'Tali Rafia Lama')
            ->set('nama_bahan', 'Tali Rafia Hitam Baru')
            ->set('harga', 35000)
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSet('editId', null);

        $bahan->refresh();
        $this->assertEquals('Tali Rafia Hitam Baru', $bahan->nama_bahan);
        $this->assertEquals(35000, $bahan->harga);
    }

    // ── 7. HAPUS BAHAN HARIAN DENGAN KONFIRMASI (DELETE) ──

    public function test_kasir_dapat_menghapus_bahan_harian_setelah_konfirmasi(): void
    {
        $this->actingAs($this->kasir);

        $bahan = PenggunaanBahan::factory()->create([
            'nama_bahan' => 'Bahan Untuk Dihapus',
            'harga' => 15000,
            'user_id' => $this->kasir->id,
        ]);

        $this->assertEquals(1, PenggunaanBahan::count());

        Livewire::test(\App\Livewire\Kasir\BahanHarian::class)
            ->call('konfirmasiHapus', $bahan->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('hapusId', $bahan->id)
            ->call('hapus')
            ->assertSet('showDeleteModal', false)
            ->assertSet('hapusId', null);

        $this->assertEquals(0, PenggunaanBahan::count());
        $this->assertDatabaseMissing('penggunaan_bahan', ['id' => $bahan->id]);
    }

    // ── 8. POLICY LEVEL TEST: HANYA KASIR YANG BOLEH MENULIS ──

    public function test_policy_menolak_owner_dan_karyawan_membuat_bahan_harian(): void
    {
        $policy = new \App\Policies\PenggunaanBahanPolicy();

        $this->assertTrue($policy->create($this->kasir));
        $this->assertFalse($policy->create($this->owner));
        $this->assertFalse($policy->create($this->karyawan));

        $bahan = PenggunaanBahan::factory()->create(['user_id' => $this->kasir->id]);

        $this->assertTrue($policy->update($this->kasir, $bahan));
        $this->assertFalse($policy->update($this->owner, $bahan));
        $this->assertFalse($policy->update($this->karyawan, $bahan));

        $this->assertTrue($policy->delete($this->kasir, $bahan));
        $this->assertFalse($policy->delete($this->owner, $bahan));
        $this->assertFalse($policy->delete($this->karyawan, $bahan));
    }
}
