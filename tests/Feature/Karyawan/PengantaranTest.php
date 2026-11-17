<?php

namespace Tests\Feature\Karyawan;

use App\Enums\StatusPengiriman;
use App\Models\BonPiutang;
use App\Models\PengirimanBarang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Role;
use App\Models\User;
use App\Services\PengirimanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PengantaranTest extends TestCase
{
    use RefreshDatabase;

    protected User $karyawan;
    protected User $karyawanLain;
    protected User $kasir;
    protected User $owner;
    protected PengirimanBarang $pengiriman;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Siapkan roles sistem
        Role::insert([
            ['id' => 1, 'nama' => 'admin', 'label' => 'Admin', 'deskripsi' => null],
            ['id' => 2, 'nama' => 'owner', 'label' => 'Owner', 'deskripsi' => null],
            ['id' => 3, 'nama' => 'kasir', 'label' => 'Kasir', 'deskripsi' => null],
            ['id' => 4, 'nama' => 'karyawan', 'label' => 'Karyawan', 'deskripsi' => null],
        ]);

        $this->kasir = User::factory()->kasir()->create();
        $this->owner = User::factory()->owner()->create();
        $this->karyawan = User::factory()->karyawan()->create();
        $this->karyawanLain = User::factory()->karyawan()->create();

        $pelanggan = Pelanggan::factory()->create(['nama' => 'Toko Subur']);

        $penjualan = Penjualan::factory()->create([
            'tipe' => 'bon',
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $this->kasir->id,
        ]);

        $bon = BonPiutang::factory()->create([
            'penjualan_id' => $penjualan->id,
            'pelanggan_id' => $pelanggan->id,
            'dibuat_oleh' => $this->kasir->id,
        ]);

        $this->pengiriman = PengirimanBarang::factory()->create([
            'bon_id' => $bon->id,
            'karyawan_id' => $this->karyawan->id,
            'status' => 'dikirim_ke_karyawan',
            'dibuat_oleh' => $this->kasir->id,
        ]);
    }

    // ── ACCEPTANCE TEST 1: OTORISASI & AKSES ──

    public function test_karyawan_bisa_melihat_daftar_pengantaran_miliknya(): void
    {
        $this->actingAs($this->karyawan)
            ->get(route('karyawan.pengantaran.index'))
            ->assertOk()
            ->assertSee('Toko Subur')
            ->assertSee('Pengantaran');
    }

    public function test_kasir_tidak_bisa_mengakses_panel_karyawan(): void
    {
        $this->actingAs($this->kasir)
            ->get(route('karyawan.pengantaran.index'))
            ->assertForbidden();
    }

    public function test_karyawan_lain_mendapat_403_saat_mengakses_detail_milik_orang_lain(): void
    {
        $this->actingAs($this->karyawanLain)
            ->get(route('karyawan.pengantaran.detail', $this->pengiriman->id))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('karyawan.pengantaran.index'))
            ->assertRedirect(route('login'));
    }

    // ── ACCEPTANCE TEST 2: DIBACA PADA OTOMATIS ──

    public function test_membuka_detail_pertama_kali_mengisi_dibaca_pada(): void
    {
        $this->assertNull($this->pengiriman->dibaca_pada);

        $this->actingAs($this->karyawan)
            ->get(route('karyawan.pengantaran.detail', $this->pengiriman->id))
            ->assertOk();

        $this->pengiriman->refresh();
        $this->assertNotNull($this->pengiriman->dibaca_pada);
    }

    // ── ACCEPTANCE TEST 3: UBAH STATUS & LOG ──

    public function test_karyawan_bisa_mengubah_status_ke_sedang_disiapkan(): void
    {
        Livewire::actingAs($this->karyawan)
            ->test(\App\Livewire\Karyawan\Pengantaran\Detail::class, ['id' => $this->pengiriman->id])
            ->call('ubahStatus');

        $this->pengiriman->refresh();
        $this->assertEquals(StatusPengiriman::SedangDisiapkan, $this->pengiriman->status);

        // Pastikan tercatat di log_status_pengiriman
        $this->assertDatabaseHas('log_status_pengiriman', [
            'pengiriman_id' => $this->pengiriman->id,
            'status' => 'sedang_disiapkan',
            'user_id' => $this->karyawan->id,
        ]);
    }

    public function test_karyawan_bisa_mengubah_status_ke_siap_berangkat(): void
    {
        $this->pengiriman->update(['status' => 'sedang_disiapkan', 'dibaca_pada' => now()]);

        Livewire::actingAs($this->karyawan)
            ->test(\App\Livewire\Karyawan\Pengantaran\Detail::class, ['id' => $this->pengiriman->id])
            ->call('ubahStatus');

        $this->pengiriman->refresh();
        $this->assertEquals(StatusPengiriman::SiapBerangkat, $this->pengiriman->status);
    }

    // ── ACCEPTANCE TEST 4: SELESAI TANPA FOTO DITOLAK ──

    public function test_selesai_tanpa_foto_ditolak_validasi(): void
    {
        $this->pengiriman->update(['status' => 'siap_berangkat', 'dibaca_pada' => now()]);

        Livewire::actingAs($this->karyawan)
            ->test(\App\Livewire\Karyawan\Pengantaran\Detail::class, ['id' => $this->pengiriman->id])
            ->call('ubahStatus')
            ->assertHasErrors(['fotoBukti' => 'required']);

        $this->pengiriman->refresh();
        $this->assertEquals(StatusPengiriman::SiapBerangkat, $this->pengiriman->status);
    }

    public function test_selesai_dengan_foto_berhasil_dan_tersimpan_di_disk_privat(): void
    {
        Storage::fake('local');

        $this->pengiriman->update(['status' => 'siap_berangkat', 'dibaca_pada' => now()]);

        $foto = UploadedFile::fake()->image('bukti_antar.jpg', 640, 480);

        Livewire::actingAs($this->karyawan)
            ->test(\App\Livewire\Karyawan\Pengantaran\Detail::class, ['id' => $this->pengiriman->id])
            ->set('fotoBukti', $foto)
            ->call('ubahStatus')
            ->assertHasNoErrors();

        $this->pengiriman->refresh();
        $this->assertEquals(StatusPengiriman::Selesai, $this->pengiriman->status);
        $this->assertNotNull($this->pengiriman->foto_bukti);
        $this->assertNotNull($this->pengiriman->waktu_selesai);

        Storage::disk('local')->assertExists($this->pengiriman->foto_bukti);
    }

    // ── ACCEPTANCE TEST 5: BUKTI PENGIRIMAN TIDAK BISA DIAKSES TANPA LOGIN ──

    public function test_bukti_tidak_dapat_diakses_tanpa_login(): void
    {
        $this->get(route('berkas.bukti-pengiriman', $this->pengiriman->id))
            ->assertRedirect(route('login'));
    }

    public function test_bukti_bisa_diakses_oleh_karyawan_pemilik_kasir_dan_owner(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('bukti-pengiriman/foto.jpg', 'fake-image-binary-data');

        $this->pengiriman->update([
            'foto_bukti' => 'bukti-pengiriman/foto.jpg',
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        // Karyawan pemilik -> OK
        $this->actingAs($this->karyawan)
            ->get(route('berkas.bukti-pengiriman', $this->pengiriman->id))
            ->assertOk();

        // Kasir -> OK
        $this->actingAs($this->kasir)
            ->get(route('berkas.bukti-pengiriman', $this->pengiriman->id))
            ->assertOk();

        // Owner -> OK
        $this->actingAs($this->owner)
            ->get(route('berkas.bukti-pengiriman', $this->pengiriman->id))
            ->assertOk();

        // Karyawan lain -> 403 Forbidden
        $this->actingAs($this->karyawanLain)
            ->get(route('berkas.bukti-pengiriman', $this->pengiriman->id))
            ->assertForbidden();
    }

    // ── ACCEPTANCE TEST 6: PERUBAHAN STATUS LANGSUNG TERLIHAT OLEH KASIR & OWNER ──

    public function test_perubahan_status_pengiriman_tersimpan_di_database_dan_tercatat_di_log(): void
    {
        $this->actingAs($this->karyawan);

        // Karyawan mengubah ke sedang disiapkan
        app(PengirimanService::class)->ubahStatus(
            $this->pengiriman,
            StatusPengiriman::SedangDisiapkan,
            $this->karyawan
        );

        $this->pengiriman->refresh();

        // Kasir / Owner query pengiriman dan melihat status terbarunya
        $this->assertEquals(StatusPengiriman::SedangDisiapkan, $this->pengiriman->status);

        $logTerbaru = $this->pengiriman->logStatus()->latest('id')->first();
        $this->assertNotNull($logTerbaru);
        $this->assertEquals('sedang_disiapkan', $logTerbaru->status);
        $this->assertEquals($this->karyawan->id, $logTerbaru->user_id);
    }

    // ── ACCEPTANCE TEST 7: BADGE BERIKUTNYA, BARU, & EMPTY STATE ──

    public function test_badge_baru_muncul_pada_pengiriman_yang_belum_dibaca(): void
    {
        $this->actingAs($this->karyawan);

        Livewire::test(\App\Livewire\Karyawan\Pengantaran\Index::class)
            ->assertSee('Baru')
            ->assertSee('BERIKUTNYA');
    }

    public function test_empty_state_tampil_saat_karyawan_tidak_memiliki_pengantaran(): void
    {
        $this->actingAs($this->karyawanLain);

        Livewire::test(\App\Livewire\Karyawan\Pengantaran\Index::class)
            ->assertSee('Tidak ada pengantaran aktif')
            ->assertSee('Pengantaran baru')
            ->assertSee('0');
    }

    // ── ACCEPTANCE TEST 8: TRIGGER T6 DI DATABASE ──

    public function test_trigger_t6_menolak_update_status_selesai_tanpa_foto_pada_level_database(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Memaksa query SQL langsung untuk memastikan trigger T6 MySQL berjalan
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE pengiriman_barang SET status = 'selesai', foto_bukti = NULL WHERE id = ?",
            [$this->pengiriman->id]
        );
    }
}
