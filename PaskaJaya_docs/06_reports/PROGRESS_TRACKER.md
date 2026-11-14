# Progress Tracker
Tandai `[x]` bila acceptance lulus.
- [ ] P01 Setup  - [ ] P02 Migrations  - [ ] P03 Models+Seeders  - [ ] P04 Layout/UI kit
- [ ] P05 F01 Auth  - [ ] P06 F08 Master Item + F10 Profil  - [ ] P07 F11/F12 Kasir
- [ ] P08 F13 Catat Bon + F07 Daftar Bon (kasir)  - [ ] P09 F14 Pengantaran + F07 Owner
- [ ] P10 F13 Bahan Harian + F09 Kehadiran  - [ ] P11 F05/F06 Owner  - [ ] P12 F02–F04 Admin  - [ ] P13 QA

## Checklist E2E
1. Register → login (tanpa demo account) ✔
2. Kasir tambah barang baru kategori baru → muncul di filter kategori ✔
3. Kasir Barang Keluar cash 2 item → stok turun, saldo cash naik ✔
4. Kasir Catat Bon → Daftar Bon (Belum Dikirim) ✔
5. Kirim ke Karyawan → stok turun, karyawan melihat tugas + badge ✔
6. Karyawan ubah status sampai Selesai (foto wajib) ✔
7. Kasir Bayar Sebagian → Tandai Lunas → saldo laci bertambah ✔
8. Owner buka Daftar Bon → detail + timeline + foto; tombol aksi tidak ada, POST = 403 ✔
9. Owner grafik 7 hari terisi; Laporan filter + CSV ✔
10. Reset manual & otomatis tercatat di Riwayat Saldo Laci ✔
11. Admin ubah role, nonaktifkan user → tidak bisa login ✔
