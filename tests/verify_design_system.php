<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap container
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Memeriksa Helper...\n";
assert(rupiah(1820000) === 'Rp 1.820.000', 'rupiah(1820000) gagal');
assert(rupiah(0) === 'Rp 0', 'rupiah(0) gagal');
assert(rupiah(null) === 'Rp 0', 'rupiah(null) gagal');
assert(inisial('Paska Jaya') === 'PJ', 'inisial("Paska Jaya") gagal');
assert(inisial('Kasir') === 'K', 'inisial("Kasir") gagal');
assert(str_contains(tanggal_id('2026-11-14'), '14 November 2026'), 'tanggal_id("2026-11-14") gagal');
echo "✓ Helper rupiah, inisial, dan tanggal_id OK\n";

echo "Memeriksa Konfigurasi Menu 4 Role...\n";
$menu = config('menu');
assert(isset($menu['admin'], $menu['owner'], $menu['kasir'], $menu['karyawan']), 'Menu roles belum lengkap');
assert($menu['admin']['label'] === 'MENU ADMIN', 'Label menu admin salah');
assert($menu['owner']['label'] === 'MENU OWNER', 'Label menu owner salah');
assert($menu['kasir']['label'] === 'MENU KASIR', 'Label menu kasir salah');
assert($menu['karyawan']['label'] === 'MENU KARYAWAN', 'Label menu karyawan salah');

$karyawanItems = collect($menu['karyawan']['items']);
$pengantaran = $karyawanItems->firstWhere('title', 'Pengantaran');
assert($pengantaran !== null && ! empty($pengantaran['has_badge']), 'Badge pengantaran karyawan belum aktif');
echo "✓ Menu 4 Role & Badge Karyawan OK\n";

echo "Memeriksa Render View Galeri /dev/ui...\n";
$viewUi = view('dev.ui-gallery')->render();
assert(str_contains($viewUi, 'PASKA JAYA Design System & Component Gallery'), 'Galeri UI gagal memuat judul');
assert(str_contains($viewUi, 'Palet Warna Resmi'), 'Galeri UI gagal memuat palet');
assert(str_contains($viewUi, 'Rp 1.820.000'), 'Galeri UI gagal memuat format uang');
assert(str_contains($viewUi, 'logo.svg') || str_contains($viewUi, 'PASKA JAYA'), 'Galeri UI gagal memuat komponen brand');
echo "✓ View dev/ui-gallery OK\n";

echo "Memeriksa Render View Auth /dev/auth...\n";
$viewAuth = view('dev.auth-preview')->render();
assert(str_contains($viewAuth, 'Every sack, bar, and delivery in one place.'), 'Auth layout gagal memuat headline resmi');
assert(str_contains($viewAuth, 'foto-toko.jpg'), 'Auth layout gagal memuat foto toko');
assert(! str_contains($viewAuth, 'Demo Account') && ! str_contains($viewAuth, 'demo account'), 'Auth layout tidak boleh memuat demo account');
echo "✓ View dev/auth-preview OK\n";

echo "Memeriksa Render View Register /dev/register...\n";
$viewRegister = view('dev.register-preview')->render();
assert(str_contains($viewRegister, 'Create Account'), 'Halaman Register gagal memuat judul Create Account');
assert(str_contains($viewRegister, 'Full Name'), 'Halaman Register gagal memuat input Full Name');
assert(str_contains($viewRegister, 'Confirm Password'), 'Halaman Register gagal memuat input Confirm Password');
assert(str_contains($viewRegister, 'Terms &amp; Conditions') || str_contains($viewRegister, 'Terms & Conditions'), 'Halaman Register gagal memuat Terms & Conditions');
assert(str_contains($viewRegister, 'Already have an account?'), 'Halaman Register gagal memuat link Already have an account?');
echo "✓ View dev/register-preview OK\n";

echo "Memeriksa Render View Owner Daftar Bon /owner/daftar-bon...\n";
$viewOwnerBon = view('dev.owner-bon-preview')->render();
assert(str_contains($viewOwnerBon, 'MENU OWNER'), 'Halaman Owner gagal memuat MENU OWNER');
assert(str_contains($viewOwnerBon, 'Daftar Bon'), 'Halaman Owner gagal memuat judul Daftar Bon');
assert(str_contains($viewOwnerBon, 'Hanya Lihat'), 'Halaman Owner gagal memuat badge Hanya Lihat');
assert(str_contains($viewOwnerBon, 'Mode baca saja oleh Kasir'), 'Halaman Owner gagal memuat banner Kasir');
assert(str_contains($viewOwnerBon, 'Update otomatis dari Karyawan'), 'Halaman Owner gagal memuat banner Karyawan');
assert(str_contains($viewOwnerBon, 'RINGKASAN BARANG'), 'Halaman Owner gagal memuat kolom RINGKASAN BARANG');
assert(str_contains($viewOwnerBon, 'STATUS PENGANTARAN'), 'Halaman Owner gagal memuat kolom STATUS PENGANTARAN');
assert(str_contains($viewOwnerBon, 'Pak Hendra'), 'Halaman Owner gagal memuat profil Pak Hendra');
echo "✓ View dev/owner-bon-preview OK\n";

echo "\n>>> SEMUA VERIFIKASI DESIGN SYSTEM & LAYOUT LOLOS 100%! <<<\n";
