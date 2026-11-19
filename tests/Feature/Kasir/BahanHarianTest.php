<?php

namespace Tests\Feature\Kasir;

use App\Models\PenggunaanBahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BahanHarianTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_dapat_mengelola_bahan_harian_melalui_controller(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)
            ->get(route('kasir.bahan-harian'))
            ->assertOk()
            ->assertSee('Bahan Harian');

        $this->actingAs($kasir)
            ->post(route('kasir.bahan-harian.store'), [
                'nama_bahan' => 'Solar Pick Up',
                'jumlah' => 10,
                'satuan' => 'liter',
                'harga' => 150000,
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Pengantaran pagi',
            ])
            ->assertRedirect(route('kasir.bahan-harian'));

        $bahan = PenggunaanBahan::where('nama_bahan', 'Solar Pick Up')->firstOrFail();

        $this->actingAs($kasir)
            ->put(route('kasir.bahan-harian.update', $bahan->id), [
                'nama_bahan' => 'Solar Pick Up Armada',
                'jumlah' => 12,
                'satuan' => 'liter',
                'harga' => 180000,
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Diperbarui',
            ])
            ->assertRedirect(route('kasir.bahan-harian'));

        $this->assertDatabaseHas('penggunaan_bahan', [
            'id' => $bahan->id,
            'nama_bahan' => 'Solar Pick Up Armada',
            'harga' => 180000,
        ]);

        $this->actingAs($kasir)
            ->delete(route('kasir.bahan-harian.destroy', $bahan->id))
            ->assertRedirect(route('kasir.bahan-harian'));

        $this->assertDatabaseMissing('penggunaan_bahan', ['id' => $bahan->id]);
    }
}
