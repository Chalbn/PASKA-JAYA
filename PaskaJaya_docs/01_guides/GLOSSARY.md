# PASKA JAYA — Glossary & Terminology

## Roles & User Types
| Istilah | DB (`roles.nama`) | Arti |
|---|---|---|
| Admin | `admin` | Mengelola pengguna, tim, status sistem |
| Owner (Pemilik) | `owner` | Memantau laporan, stok, bon (read-only bon & kehadiran) |
| Kasir | `kasir` | Transaksi, saldo laci, bon, bahan harian, kehadiran |
| Karyawan | `karyawan` | Pengantar barang (driver) & pekerja gudang |
Catatan: laporan menyebut karyawan pengantar sebagai *pengemudi/driver/supir*; di sistem tetap role `karyawan`.

## Transaksi & Piutang
| Istilah | Arti |
|---|---|
| Barang Keluar | Penjualan langsung (`penjualan.tipe='langsung'`), dibayar Cash/Transfer saat itu |
| Bon | Penjualan kredit (`tipe='bon'`) + catatan `bon_piutang` |
| Piutang | Total bon belum lunas |
| Sisa | `total_tagihan − total_terbayar` |
| Lewat tempo | Belum lunas & `jatuh_tempo < hari ini` |
| Bayar Sebagian | Cicilan (`pembayaran_bon`) < sisa |
| Tandai Lunas | Pembayaran sebesar sisa |
| TRX / BON | Kode transaksi / kode bon |

## Stok
| Istilah | Arti |
|---|---|
| Master Item | Tabel `barang` + kategori |
| Stok menipis / Kritis | `stok <= stok_minimum` |
| Restock | Tambah stok barang baru datang (`sp_restock_barang`) |
| Nilai Stok | Σ `stok × harga_beli` |
| Stock discrepancy | Selisih stok catatan vs fisik (masalah utama di laporan) |
| Mutasi stok | Jejak keluar-masuk (`mutasi_stok`) |

## Kas
| Istilah | Arti |
|---|---|
| Saldo Laci | Σ `kas_laci` aktif (cash + transfer) |
| Reset Manual / Otomatis | Penutupan laci oleh kasir / oleh sistem 00:00 |
| Direset oleh | `penutupan_laci.direset_oleh` (NULL = Sistem) |

## Pengiriman
| Status tampil | `pengiriman_barang.status` / log |
|---|---|
| Belum Dikirim | tidak ada pengiriman aktif |
| Dikirim ke Karyawan | `dikirim_ke_karyawan` |
| Sedang Disiapkan | `sedang_disiapkan` |
| Siap Berangkat / Sedang Diantar | `siap_berangkat` |
| Selesai | `selesai` (+ foto bukti) |
| Baru / Berikutnya | `dibaca_pada IS NULL` / jadwal terdekat |

## Lainnya
| Istilah | Arti |
|---|---|
| Bahan Harian | `penggunaan_bahan` — pengeluaran operasional (bensin, tali, dll.) |
| Kehadiran | `absensi_karyawan` + `detail_pekerjaan_absensi` |
| Tim | `anggota_tim` — tim pembuat web (halaman Admin) |
| POS | Point of Sale (modul kasir) |
| ERD / Normalisasi | Materi MSBD: rancangan relasi & bentuk 1NF–3NF |
| Trigger / Stored Procedure / View / Privilege | Objek & hak akses MySQL (ADR-007, ADR-013) |

## DEPRECATED / Jangan Dipakai
| Jangan | Pakai |
|---|---|
| "demo account" / data dummy | Data asli dari database |
| "supir" di UI | "Karyawan" |
| nama tabel Inggris untuk 14 entitas inti | nama dari laporan |
