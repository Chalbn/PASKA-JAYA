<?php

namespace Tests\Feature\Karyawan;

use App\Enums\StatusPengiriman;
use App\Models\BonPiutang;
use App\Models\Pelanggan;
use App\Models\PengirimanBarang;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengantaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_karyawan_dapat_melihat_daftar_dan_detail_pengantaran_melalui_controller(): void
    {
        $karyawan = User::factory()->karyawan()->create();
        $kasir = User::factory()->kasir()->create();
        $pelanggan = Pelanggan::create(['nama' => 'PT Maju Jaya', 'no_hp' => '08123456789']);
        $penjualan = Penjualan::create([
            'kode_transaksi' => 'TRX-1001',
            'tipe' => 'bon',
            'pelanggan_id' => $pelanggan->id,
            'nama_pelanggan' => $pelanggan->nama,
            'total' => 500000,
            'user_id' => $kasir->id,
            'waktu_transaksi' => now(),
        ]);
        $bon = BonPiutang::create([
            'kode_bon' => 'BON-0001',
            'penjualan_id' => $penjualan->id,
            'pelanggan_id' => $pelanggan->id,
            'alamat_pengantaran' => 'Jl. Sudirman No. 5',
            'tanggal_bon' => now()->toDateString(),
            'jatuh_tempo' => now()->addDays(14)->toDateString(),
            'total_tagihan' => 500000,
            'total_terbayar' => 0,
            'status_bayar' => 'belum_lunas',
            'dibuat_oleh' => $kasir->id,
        ]);
        $pengiriman = PengirimanBarang::create([
            'bon_id' => $bon->id,
            'karyawan_id' => $karyawan->id,
            'status' => StatusPengiriman::DikirimKeKaryawan,
            'dibuat_oleh' => $kasir->id,
        ]);

        $this->actingAs($karyawan)
            ->get(route('karyawan.pengantaran.index'))
            ->assertOk()
            ->assertSee('PT Maju Jaya');

        $this->actingAs($karyawan)
            ->get(route('karyawan.pengantaran.detail', $pengiriman->id))
            ->assertOk()
            ->assertSee('BON-0001');

        $this->actingAs($karyawan)
            ->post(route('karyawan.pengantaran.status', $pengiriman->id))
            ->assertRedirect(route('karyawan.pengantaran.detail', $pengiriman->id));

        $this->assertEquals(StatusPengiriman::SedangDisiapkan, $pengiriman->fresh()->status);
    }
}
