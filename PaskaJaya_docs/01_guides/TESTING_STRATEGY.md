# PASKA JAYA — Testing Strategy

## Testing Pyramid
```
        E2E manual (checklist PROGRESS_TRACKER, UAT mitra)
      Feature test (Livewire + akses role)
   Database test (trigger, stored procedure, view)
 Unit test (Service, helper, enum)
```
## Framework
Pest + `pestphp/pest-plugin-laravel`; Livewire: `Livewire::test(Komponen::class)`. Database test memakai MySQL khusus test (`paska_jaya_test`) karena trigger/procedure tidak tersedia di SQLite. Gunakan factory **hanya di test**.

## Template: Feature Test (Livewire + role)
```php
it('kasir dapat menyimpan barang keluar dan stok berkurang', function () {
    $kasir = User::factory()->kasir()->create();
    $barang = Barang::factory()->create(['stok' => 10, 'harga_jual' => 65000]);

    Livewire::actingAs($kasir)->test(BarangKeluar::class)
        ->set('barangId', $barang->id)->set('jumlah', 3)->call('tambah')
        ->set('metode', 'cash')->call('simpanTransaksi')->assertHasNoErrors();

    expect($barang->fresh()->stok)->toBe(7);
    expect(KasLaci::whereNull('penutupan_id')->sum('jumlah'))->toBe(195000);
});

it('owner tidak dapat membayar bon', function () {
    $this->actingAs(User::factory()->owner()->create());
    Livewire::test(\App\Livewire\Kasir\DaftarBon::class)->assertForbidden();
});
```
## Template: Database Test (trigger/procedure)
```php
it('T1: stok tidak boleh negatif', function () {
    $b = Barang::factory()->create(['stok' => 2]);
    expect(fn () => DB::update('UPDATE barang SET stok = stok - 5 WHERE id = ?', [$b->id]))
        ->toThrow(QueryException::class);
});
it('sp_kirim_bon memotong stok dan batal mengembalikan', function () { /* ... */ });
```
## Cakupan Wajib
| Area | Test |
|---|---|
| Auth | login email/HP, akun nonaktif, rate limit, register, OTP, redirect per role |
| Hak akses | matriks 4 role × semua route (PRD §6) → 200/403/redirect |
| Master Item | tambah, edit, restock, hapus, kategori otomatis, filter |
| Penjualan | stok turun, kas laci naik, stok kurang ditolak, kode TRX berurutan |
| Bon | buat bon, kirim, ganti, batal, bayar sebagian, lunas, lewat tempo |
| Pengiriman | status berurutan, foto wajib, hanya milik sendiri, bukti privat |
| Laci | saldo aktif, reset manual & otomatis, riwayat & detail |
| Laporan | filter tanggal/metode, CSV, angka = view |
| Admin | CRUD pengguna, admin terakhir terjaga, activity_logs append-only |
| **Database kosong** | semua halaman render tanpa error + empty state |
## Running Tests
```bash
php artisan test
./vendor/bin/pest --coverage --min=80
```
## Coverage Target
| Layer | Target |
|---|---|
| Service | ≥ 80% |
| Trigger/procedure | semua objek punya ≥ 1 happy path + 1 error |
| Halaman | akses role + data kosong |
## Testing Checklist per Fitur (Definition of Done)
- [ ] Feature test hijau  - [ ] Akses 4 role diuji  - [ ] Empty state  - [ ] Pesan error ramah  - [ ] Sesuai screen PDF  - [ ] Activity log tercatat  - [ ] Tracker diupdate
## UAT dengan Mitra
Sebelum rilis, pemilik/kasir menjalankan skenario harian (jual, bon, kirim, tutup laci) di database staging dengan data asli mereka.
