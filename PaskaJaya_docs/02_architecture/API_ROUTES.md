# PASKA JAYA — Routes (Livewire full-page)

Semua halaman = komponen Livewire (tidak ada REST API terpisah). Middleware: `auth`, `aktif`, `role:<nama>`. Redirect setelah login: admin→`/admin`, owner→`/owner`, kasir→`/kasir`, karyawan→`/karyawan/pengantaran`.

## Route Summary
| Prefix | Role | Jumlah halaman |
|---|---|---|
| (guest) | – | 4 |
| `/admin` | admin | 4 |
| `/owner` | owner | 6 |
| `/kasir` | kasir | 11 |
| `/karyawan` | karyawan | 2 |
| route unduhan | owner/kasir | 3 |

## 1. Guest
| URL | Komponen | Keterangan |
|---|---|---|
| GET `/login` | `Auth\Login` | email ATAU no HP + password; tanpa kotak demo account |
| GET `/register` | `Auth\Register` | wajib centang Terms; role awal = karyawan |
| GET `/verifikasi-otp` | `Auth\VerifikasiOtp` | bila `AUTH_OTP_ENABLED=true` |
| GET `/lupa-password`, `/reset-password/{token}` | `Auth\LupaPassword` | |
| POST `/logout` | controller | semua role |

## 2. Admin — `/admin` (`role:admin`)
| URL | Komponen | Fitur |
|---|---|---|
| `/admin` | `Admin\Dashboard` | F02 Ringkasan Sistem |
| `/admin/pengguna` | `Admin\Pengguna\Index` | F03 Manajemen Pengguna |
| `/admin/tim` | `Admin\Tim\Index` | F04 Tim |
| `/admin/profil` | `Shared\Profil` | F10 |

## 3. Owner — `/owner` (`role:owner`)
| URL | Komponen | Fitur |
|---|---|---|
| `/owner` | `Owner\Dashboard` | F05 Ringkasan Toko |
| `/owner/laporan-barang-keluar` | `Owner\LaporanBarangKeluar` | F06 |
| `/owner/daftar-bon` | `Owner\DaftarBon` | F07 (read-only) |
| `/owner/master-item` | `Shared\MasterItem\Index` | F08 |
| `/owner/kehadiran` | `Owner\Kehadiran` | F09 (read-only) |
| `/owner/profil` | `Shared\Profil` | F10 |

## 4. Kasir — `/kasir` (`role:kasir`)
| URL | Komponen | Fitur |
|---|---|---|
| `/kasir` | `Kasir\Beranda` | F11 |
| `/kasir/barang-keluar` | `Kasir\BarangKeluar` | F11 |
| `/kasir/saldo` | `Kasir\LihatSaldo` | F12 |
| `/kasir/grafik-mingguan` | `Kasir\GrafikMingguan` | F12 |
| `/kasir/riwayat-saldo` | `Kasir\RiwayatSaldoLaci` | F12 |
| `/kasir/catat-bon` | `Kasir\CatatBon` | F13 |
| `/kasir/daftar-bon` | `Kasir\DaftarBon` | F07 |
| `/kasir/bahan-harian` | `Kasir\BahanHarian` | F13 |
| `/kasir/kehadiran` | `Kasir\Kehadiran` | F09 |
| `/kasir/master-item` | `Shared\MasterItem\Index` | F08 |
| `/kasir/profil` | `Shared\Profil` | F10 |

## 5. Karyawan — `/karyawan` (`role:karyawan`)
| URL | Komponen | Fitur |
|---|---|---|
| `/karyawan/pengantaran` | `Karyawan\Pengantaran\Index` | F14 |
| `/karyawan/pengantaran/{pengiriman}` | `Karyawan\Pengantaran\Detail` | F14 (hanya milik sendiri) |
| `/karyawan/profil` | `Shared\Profil` | F10 |

## 6. Route Unduhan / Berkas
| URL | Role | Fungsi |
|---|---|---|
| GET `/owner/laporan-barang-keluar/unduh-csv` | owner | CSV sesuai filter |
| GET `/kasir/riwayat-saldo/unduh?format=xlsx\|pdf` | kasir | Excel/PDF |
| GET `/berkas/bukti-pengiriman/{pengiriman}` | owner, kasir, karyawan pemilik | foto bukti dari disk **privat** (cek Policy) |

## Aksi (method Livewire, bukan route)
Kasir: `simpanTransaksi()`, `resetManual()`, `simpanBon()`, `kirimKeKaryawan()`, `gantiKaryawan()`, `batalkanPengiriman()`, `tandaiLunas()`, `bayarSebagian()`, `hapusBon()`.
Karyawan: `ubahStatus()` (foto bukti wajib untuk selesai).
Owner: tidak ada aksi tulis pada Bon & Kehadiran (Policy menolak → 403).

## Middleware Registration (`bootstrap/app.php`)
```php
$middleware->alias([
    'role'  => \App\Http\Middleware\PastikanRole::class,
    'aktif' => \App\Http\Middleware\PastikanAktif::class,
]);
```
Penggunaan: `Route::middleware(['auth','aktif','role:kasir'])->prefix('kasir')->group(...)`.
