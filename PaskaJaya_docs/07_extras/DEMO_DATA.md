# Data Demo (diambil dari desain PDF) — hanya untuk seeder LOCAL
Password semua akun: `password`.
## Users
| Nama | Email | Role | Dibuat | Status |
|---|---|---|---|---|
| Rudi Hartono | admin@toko.com | admin | 5 Jan 2024 | aktif |
| H. Surya Wijaya | owner@toko.com | owner | 5 Jan 2024 | aktif (HP 0813-2200-4412, Jl. Mawar Raya No. 3, Bekasi) |
| Nadia Putri | kasir@toko.com | kasir | 14 Feb 2024 | aktif (HP 0857-1122-3344, Jl. Kenanga No. 8, Bekasi) |
| Budi Santoso | karyawan@toko.com | karyawan | 1 Mar 2024 | aktif (HP 0821-9087-6543, Jl. Melati No. 21, Bekasi) |
| Agus Prasetyo | agus@toko.com | karyawan | 1 Mar 2024 | aktif |
| Eko Wahyudi | eko@toko.com | karyawan | 10 Apr 2024 | aktif |
| Slamet Riyadi | slamet@toko.com | karyawan | 20 Mei 2024 | nonaktif |
## Tim
RH Rudi Hartono – Administrator Sistem · DA Dewi Anggraini – Manajer Operasional · BP Bagas Pratama – Kepala Gudang · SW Sri Wahyuni – Staf Keuangan · YM Yusuf Maulana – Koordinator Pengantaran
## Kategori
Semen, Pasir & Batu, Batu Bata, Cat, Pipa, Besi, Keramik, Paku & Baut, Atap
## Master Item (kode, nama, kategori, satuan, beli, jual, stok, batas min, tgl masuk)
| Kode | Nama | Kat | Sat | Beli | Jual | Stok | Min | Masuk |
|---|---|---|---|---|---|---|---|---|
| BRG-001 | Semen Gresik 50kg | Semen | Sak | 58.000 | 65.000 | 142 | 30 | 10 Nov 2026 |
| BRG-002 | Semen Tiga Roda 40kg | Semen | Sak | 49.000 | 55.000 | 96 | 30 | 4 Nov |
| BRG-003 | Pasir Cor | Pasir & Batu | M³ | 220.000 | 260.000 | 18 | 5 | 9 Nov |
| BRG-004 | Pasir Pasang | Pasir & Batu | M³ | 200.000 | 240.000 | 12 | 5 | 9 Nov |
| BRG-005 | Batu Split 1/2 | Pasir & Batu | M³ | 280.000 | 330.000 | 4 | 10 | 1 Nov |
| BRG-006 | Batu Bata Merah | Batu Bata | Biji | 650 | 900 | 4200 | 500 | 7 Nov |
| BRG-007 | Batako Press | Batu Bata | Biji | 2.200 | 3.000 | 850 | 200 | 7 Nov |
| BRG-008 | Cat Tembok White 5kg | Cat | Kaleng | 105.000 | 135.000 | 38 | 10 | 5 Nov |
| BRG-009 | Cat Kayu & Besi 1kg | Cat | Kaleng | 42.000 | 58.000 | 7 | 10 | 29 Okt |
| BRG-010 | Pipa PVC 3" AW (4m) | Pipa | Batang | 68.000 | 85.000 | 64 | 20 | 8 Nov |
| BRG-011 | Pipa PVC 1/2" (4m) | Pipa | Batang | 16.000 | 22.000 | 9 | 15 | 24 Okt |
| BRG-012 | Besi Beton Ulir 10mm | Besi | Batang | 70.000 | 82.000 | 12 | 20 | 6 Nov |
| BRG-013 | Besi Beton Polos 8mm | Besi | Batang | 39.000 | 47.000 | 75 | 20 | 6 Nov |
| BRG-014 | Keramik Lantai 40x40 Putih | Keramik | Dus | 55.000 | 68.000 | 120 | 20 | 8 Nov |
| BRG-015 | Keramik Dinding 25x40 | Keramik | Dus | 50.000 | 62.000 | 6 | 15 | 3 Nov |
| BRG-016 | Paku Beton 2" (1kg) | Paku & Baut | Kg | 21.000 | 26.000 | 65 | 15 | 5 Nov |
| BRG-017 | Seng Gelombang 0,25mm (3m) | Atap | Lembar | 65.000 | 78.000 | 55 | 15 | 2 Nov |
Menipis (5): 005, 009, 011, 012, 015.
## Barang Keluar (TRX) — total harus sama dengan desain, isi item bebas (harga jual master)
| ID | Waktu | Pelanggan | Metode | Total |
|---|---|---|---|---|
| TRX-1012 | 14 Nov 2026 10:35 | Bu Lestari | TRANSFER | 1.490.000 (Cat Tembok White 5kg ×6, Keramik Lantai 40x40 ×10) |
| TRX-1011 | 14 Nov 2026 09:10 | Pak Joko | CASH | 690.000 (Batako Press ×100, Semen Gresik 50kg ×6) |
| TRX-1010 | 13 Nov 2026 16:30 | Umum | CASH | 162.000 (Paku Beton 2" ×4, Cat Kayu & Besi 1kg ×1) |
| TRX-1009 | 13 Nov 2026 11:05 | PT Bangun Persada | TRANSFER | 3.700.000 (Semen Gresik ×40, Semen Tiga Roda ×20) |
| TRX-1008 | 13 Nov 2026 08:50 | Pak Darmo | CASH | 760.000 (Pasir Cor ×2, Pasir Pasang ×1) |
| TRX-1007 | 12 Nov 2026 14:25 | Bu Sari | TRANSFER | 1.560.000 (Seng Gelombang ×20) |
| TRX-1006 | 12 Nov 2026 09:45 | Pak Wahyu | CASH | 730.000 (Pipa PVC 3" ×6, Pipa PVC 1/2" ×10) |
Boleh menambah transaksi 6–11 Nov agar grafik 7 hari & riwayat laci terisi. Total angka demo tidak harus persis sama dengan kartu desain.

## Bon
| Pelanggan | Barang | Total | Terbayar | Status bayar | Status antar | Karyawan |
|---|---|---|---|---|---|---|
| Pak Hendra (Kontraktor) – Perumahan Griya Asri Blok C3, Bekasi Timur, jatuh tempo 9 Nov 2026 (lewat tempo) | Semen Gresik 50kg ×20, Pasir Cor ×2 | 1.820.000 | 500.000 | Belum Lunas | Siap Berangkat/Sedang Diantar (jadwal 14 Nov 2026 14:00; catatan "Turunkan di depan gerbang, hubungi mandor saat tiba.") | Budi Santoso |
| Bu Sari – Jl. Kenanga No. 8, Bekasi | Cat Tembok White 5kg ×4, Cat Kayu & Besi 1kg ×2 | 656.000 | 656.000 | Lunas | Selesai + foto bukti | Budi Santoso |
| CV Berkah Konstruksi – jatuh tempo 21 Nov 2026 | Besi Beton Ulir 10mm ×40, Besi Beton Polos 8mm ×10, Paku Beton 2" ×5 | 3.880.000 | 0 | Belum Lunas | Belum Dikirim | – |
| Pak Joko | Keramik Lantai 40x40 ×10, Paku Beton 2" ×5 | 810.000 | 810.000 | Lunas | Belum Dikirim | – |
| Pak Darmo – Jl. Cempaka Putih No. 12, Bekasi Utara | Pipa PVC 3" AW ×10, Pipa PVC 1/2" ×10, Cat Kayu & Besi 1kg ×2 | 1.186.000 | 400.000 | Belum Lunas | Dikirim ke Karyawan | Budi Santoso |
| Toko Maju Jaya | Seng Gelombang ×20, Paku Beton ×10 | 1.820.000 | 0 | Belum Lunas | Sedang Disiapkan | Agus Prasetyo |
## Kehadiran (contoh)
Budi Santoso 13 Nov Bongkar muat semen · Agus 13 Nov Menata stok gudang · Agus 12 Nov Mengantar pesanan, Mengecek stok · Eko 12 Nov Memotong pipa, Melayani pembeli · Eko 11 Nov Menata stok cat · Budi 11 Nov Bongkar muat keramik · Budi 10 Nov Mengantar pesanan · Agus 10 Nov Bongkar muat pasir, Merapikan area toko · Budi 9 Nov Bongkar muat semen, Mengantar pesanan · Agus 9 Nov Menata stok gudang · Eko 9 Nov Melayani pembeli, Memotong besi.
## Bahan Harian (contoh)
14 Nov Bensin pick-up 15 liter Rp 150.000 (Operasional pengantaran) · 13 Nov Sarung tangan kain 12 pasang Rp 84.000 (Bongkar muat karyawan) · 12 Nov Air galon 3 galon Rp 60.000 (Kebutuhan toko) · 11 Nov Lakban coklat 6 roll Rp 54.000 (Pengemasan keramik) · 10 Nov Tali rafia 5 gulung Rp 40.000 (Pengikat besi dan pipa) · 9 Nov Plastik kemasan paku 2 pak Rp 45.000 (Pengemasan eceran) · 8 Nov Solar untuk genset 10 liter Rp 68.000 (Cadangan listrik gudang).
## Riwayat Saldo Laci (contoh)
13 Nov: cash 802.000, transfer 3.700.000 · 12 Nov: 1.230.000 / 1.560.000 · 11 Nov: 780.000 / 2.196.000 (direset Nadia Putri) · 10 Nov: 506.000 / 0 · 9 Nov: 0 / 3.400.000 · 8 Nov: 5.702.000 / 0 · 7 Nov: 0 / 0. Sisanya "Sistem", waktu reset 00:00.
