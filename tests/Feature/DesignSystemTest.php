<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    /**
     * Uji helper rupiah, tanggal_id, dan inisial.
     */
    public function test_helpers_format_correctly(): void
    {
        $this->assertEquals('Rp 1.820.000', rupiah(1820000));
        $this->assertEquals('Rp 0', rupiah(0));
        $this->assertEquals('Rp 0', rupiah(null));

        $this->assertEquals('PJ', inisial('Paska Jaya'));
        $this->assertEquals('AB', inisial('Admin Budi'));
        $this->assertEquals('K', inisial('Kasir'));

        $tanggal = tanggal_id('2026-11-14');
        $this->assertStringContainsString('14 November 2026', $tanggal);
    }

    /**
     * Uji konfigurasi menu 4 peran.
     */
    public function test_menu_config_contains_all_four_roles(): void
    {
        $menu = config('menu');

        $this->assertArrayHasKey('admin', $menu);
        $this->assertArrayHasKey('owner', $menu);
        $this->assertArrayHasKey('kasir', $menu);
        $this->assertArrayHasKey('karyawan', $menu);

        $this->assertEquals('MENU ADMIN', $menu['admin']['label']);
        $this->assertEquals('MENU OWNER', $menu['owner']['label']);
        $this->assertEquals('MENU KASIR', $menu['kasir']['label']);
        $this->assertEquals('MENU KARYAWAN', $menu['karyawan']['label']);

        // Verifikasi menu Karyawan memiliki badge pengantaran
        $karyawanItems = collect($menu['karyawan']['items']);
        $pengantaran = $karyawanItems->firstWhere('title', 'Pengantaran');
        $this->assertNotNull($pengantaran);
        $this->assertTrue($pengantaran['has_badge']);
    }

    /**
     * Uji halaman galeri UI dev dapat diakses pada local environment.
     */
    public function test_dev_ui_gallery_accessible_in_local(): void
    {
        $response = $this->get('/dev/ui');

        $response->assertStatus(200);
        $response->assertSee('PASKA JAYA Design System & Component Gallery');
        $response->assertSee('Palet Warna Resmi');
        $response->assertSee('Rp 1.820.000');
    }

    /**
     * Uji halaman auth preview dengan split-screen.
     */
    public function test_dev_auth_preview_accessible_in_local(): void
    {
        $response = $this->get('/dev/auth');

        $response->assertStatus(200);
        $response->assertSee('Every sack, bar, and delivery in one place.');
        $response->assertSee('Stock, cashier, and delivery tracking');
        $response->assertDontSee('Demo Account');
    }
}
