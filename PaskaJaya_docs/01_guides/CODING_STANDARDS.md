# PASKA JAYA — Coding Standards (Laravel 11 + Livewire 3)

## 1. PHP / Laravel
### 1.1 Naming
| Objek | Aturan | Contoh |
|---|---|---|
| Tabel/kolom DB | snake_case Indonesia | `detail_penjualan.harga_satuan` |
| Model | PascalCase singular | `BonPiutang` (`$table = 'bon_piutang'`) |
| Enum | PascalCase, case snake/huruf kecil | `StatusPengiriman::SiapBerangkat = 'siap_berangkat'` |
| Komponen Livewire | PascalCase per folder role | `Kasir\BarangKeluar` |
| View Livewire | kebab-case | `livewire/kasir/barang-keluar.blade.php` |
| Route name | `role.fitur` | `kasir.barang-keluar` |
| Kode | `BRG-001`, `TRX-1001`, `BON-0001` | dibuat Service, bukan input bebas |
### 1.2 Style
PSR-12, `declare(strict_types=1)`, Pint (`./vendor/bin/pint`). Type hint & return type wajib.
### 1.3 Model Standards
`$fillable` eksplisit, `casts()` untuk enum/json/date, relasi bernama jelas, scope: `Barang::stokMenipis()`, `BonPiutang::belumLunas()`, `BonPiutang::lewatTempo()`. SoftDeletes pada `users`, `barang`, `bon_piutang`.
### 1.4 Service Standards
- `PenjualanService::simpanLangsung(array $item, MetodeBayar $metode, ?string $pelanggan)` — transaksi DB.
- `BonService::buat()`, `kirim()` (`CALL sp_kirim_bon`), `batalkanPengiriman()`, `bayar()`.
- `LaciService::saldoAktif()`, `reset(?User $user)`.
- Service menangkap `QueryException` dari `SIGNAL` dan melempar `DomainException` berpesan ramah.
### 1.5 Migration Standards
- Satu tabel per file, urut sesuai DATABASE_SCHEMA. FK eksplisit (`constrained()->restrictOnDelete()`).
- View/trigger/procedure: file migration terpisah memakai `DB::unprepared()` (tanpa `DELIMITER`), `down()` men-drop objek.
- Jangan edit migration yang sudah di-merge.

## 2. Livewire / Frontend
### 2.1 Komponen
```php
#[Layout('components.layouts.app', ['judul' => 'Barang Keluar'])]
class BarangKeluar extends Component { /* ... */ }
```
- Full-page route: `Route::get('/barang-keluar', BarangKeluar::class)`.
- Form besar → Livewire Form Object (`app/Livewire/Forms`).
- List besar → `WithPagination` + `#[Url]` untuk filter.
- Upload → `WithFileUploads`; validasi `image|max:4096`.
### 2.2 Interaksi
- Pencarian: `wire:model.live.debounce.300ms`.
- Tombol aksi: `wire:loading.attr="disabled"`.
- Modal: komponen Blade `<x-ui.modal>` (Alpine); konfirmasi hapus `<x-ui.confirm>`.
- Flash/toast: `$this->dispatch('toast', tipe: 'sukses', pesan: '...')`.
### 2.3 Tailwind
Tanpa CSS custom selain token di `app.css`. Responsif: sidebar jadi drawer < `lg`; tabel `overflow-x-auto`.
### 2.4 Chart
Chart.js diinisialisasi di Alpine (`x-data="grafik(@js($data))"`). Data kosong tetap menggambar sumbu (nilai 0).

## 3. Route Standards
Grup per role: `['auth','aktif','role:<x>']`. Nama URL Indonesia (`/barang-keluar`, `/daftar-bon`). Route unduhan memakai controller + Policy.

## 4. Database Access
- Panggil procedure: `DB::select('CALL sp_kirim_bon(?,?,?,?,?)', [...])`.
- Baca laporan dari view (`v_stok_menipis`, `v_penjualan_harian`, `v_piutang_aktif`) lewat model read-only atau `DB::table`.
- Dilarang `DB::statement('DROP/ALTER')` di runtime.

## 5. Testing Standards (minimal)
Setiap Service punya feature test; tiap trigger/procedure punya test di `tests/Database`; tiap halaman: test akses role + test database kosong. Detail di TESTING_STRATEGY.md.

## 6. Komentar & Dokumentasi
Komentar dalam Bahasa Indonesia seperlunya (jelaskan *kenapa*). Docblock untuk Service publik.
