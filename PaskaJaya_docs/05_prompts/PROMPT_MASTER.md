# PROMPT MASTER — Bangun Web PASKA JAYA (satu kali tempel)

> Tempel ke AI coding agent. Lampirkan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf`, foto toko `07_extras/aset/foto_toko.jpg`, dan (bila ada) folder dokumentasi ini. Alternatif: jalankan prompt bertahap sesuai `04_plans/URUTAN_KERJA.md`.

## Peran
Senior full-stack Laravel developer. Bangun **PASKA JAYA — Sistem Manajemen Toko Bangunan** yang identik dengan desain PDF, lengkap semua layar dan fungsi (bukan prototipe statis). Tagline: "Every sack, bar, and delivery in one place."

## Stack (sesuai laporan MSBD Kelompok 7)
Laravel 13 (PHP 8.3) + **Livewire 3** (full-page component) + Alpine.js + Tailwind CSS + MySQL 8 (Laragon) + Chart.js + maatwebsite/excel + dompdf + Pest. Uang = integer rupiah. Zona waktu Asia/Jakarta. UI Bahasa Indonesia (Login/Register berbahasa Inggris sesuai desain).

## Aturan Data (PENTING)
- **Database dimulai KOSONG. JANGAN membuat seeder/data dummy.** Nama & angka di PDF hanyalah contoh tampilan.
- Seeder hanya data referensi sistem: 4 role + konfigurasi sistem.
- Admin pertama lewat `php artisan app:buat-admin` (tanpa password default).
- Semua halaman mengambil data dari database dan **wajib punya empty state**; kartu/grafik bernilai 0 saat kosong.

## Database (22 tabel, nama Indonesia — lihat `02_architecture/DATABASE_SCHEMA.md`)
roles, users, system_configs, activity_logs, kategori_barang, barang, mutasi_stok, penggunaan_bahan, pelanggan, penjualan, detail_penjualan, bon_piutang, pembayaran_bon, pengiriman_barang, detail_pengiriman, log_status_pengiriman, kas_laci, penutupan_laci, absensi_karyawan, detail_pekerjaan_absensi, anggota_tim, otp_codes.
Objek lanjutan (`database/migrations/` & `database/privileges.sql`): 3 view, 8 trigger, 5 stored procedure, 4 akun MySQL berhak akses terbatas. Logika stok/kas/status bon dijaga di database (trigger/procedure); Laravel memanggilnya lewat Service.
Konsep: `penjualan.tipe` = `langsung` (Barang Keluar, Cash/Transfer) atau `bon` (kredit + `bon_piutang` 1:1). Stok penjualan langsung berkurang saat disimpan; stok bon berkurang saat **Kirim ke Karyawan** (`sp_kirim_bon`) dan kembali saat batal. Saldo laci = `kas_laci` aktif; reset otomatis 00:00 / manual (`sp_reset_laci`).

## Brand
Logo "PJ" (kotak rounded biru) + **PASKA JAYA** + **TOKO BAHAN BANGUNAN** (lihat `PROMPT_LOGO.md`). **Halaman Login & Register memakai foto toko yang dilampirkan (`foto_toko.jpg` → `public/images/foto-toko.jpg`) di panel kiri**, overlay biru, headline "Every sack, bar, and delivery in one place.". Tanpa kotak demo accounts.

## Gaya Visual
Sidebar kiri gradien biru (navy → biru), label "MENU ADMIN/OWNER/KASIR/KARYAWAN", item aktif pil putih, Keluar di bawah. Topbar gradien biru: tanggal, judul, avatar+nama+role. Kartu putih kaca radius besar; badge pil (Lunas navy ✓, Belum Lunas kuning, Selesai navy ✓, Kritis merah, CASH navy, TRANSFER biru muda). Font Plus Jakarta Sans. Responsif.

## Hak Akses
`admin`, `owner`, `kasir`, `karyawan`; ditegakkan di server (middleware `role:` + Policy). Owner hanya-lihat untuk Daftar Bon & Kehadiran.

## Fitur
### Auth
Login (email ATAU no HP, rate limit, akun nonaktif ditolak), Register (Full Name, Email, Phone, Password, Confirm, Terms; role awal karyawan), OTP (flag `AUTH_OTP_ENABLED`), lupa password. Profil semua role (data akun, hapus akun dg konfirmasi password, data profil + foto, ubah password).
### ADMIN
1. **Ringkasan Sistem** (tombol "Kelola Pengguna" → Manajemen Pengguna; 4 kartu; Pengguna per Role; Status Sistem: Layanan, Basis data, Pencadangan terakhir, Catatan kehadiran, Catatan bahan harian). 2. **Manajemen Pengguna** (cari, tabel, role dropdown, aktif/nonaktif, edit, hapus, + Tambah Pengguna; proteksi akun sendiri & admin terakhir). 3. **Tim** (kartu anggota tim pembuat web: inisial, nama, jabatan, edit; CRUD).
### OWNER
1. **Ringkasan Toko** (4 kartu; **grafik Penjualan 7 Hari Terakhir berisi data nyata**; Stok Menipis; Bon Belum Lunas; Transaksi Terbaru). 2. **Laporan Barang Keluar** (filter tanggal+metode, 3 kartu, tabel + Rincian expand, Unduh CSV). 3. **Daftar Bon** (read-only; filter Semua/Belum lunas/Lunas; modal Detail + timeline Alur Status + foto bukti). 4. **Master Item** (akses penuh). 5. **Kehadiran** (read-only). 6. **Profil**.
### KASIR
Menu: Beranda, Barang Keluar, Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, Catat Bon, Daftar Bon, Bahan Harian, Kehadiran, Master Item, Profil.
1. **Beranda** (saldo laci + Reset Manual, transaksi terbaru, 3 kartu, quick link: Barang Keluar→Barang Keluar, Catat Bon→Catat Bon, Bahan Harian→Bahan Harian, Kehadiran→Kehadiran). 2. **Barang Keluar** (pilih barang → keranjang → Cash/Transfer → simpan; riwayat di kanan). 3. **Lihat Saldo**. 4. **Grafik Mingguan** (Minggu ini/lalu/pilih tanggal; stacked bar Cash+Transfer; kartu ringkasan & % vs minggu lalu). 5. **Riwayat Saldo Laci** (filter tanggal, Detail, ekspor Excel & PDF). 6. **Catat Bon** (data bon, tambah barang dari master item, keranjang editable, total, simpan). 7. **Daftar Bon** (Kirim ke Karyawan, Ganti Karyawan, Batalkan Pengiriman, Tandai Lunas, Bayar Sebagian, Detail, Hapus). 8. **Bahan Harian** (form + daftar + total pengeluaran). 9. **Kehadiran** (CRUD, banyak pekerjaan per hari). 10. **Master Item**. 11. **Profil**.
**Master Item**: 4 kartu (Total Barang, Nilai Stok, Stok Menipis, Stok Aman); cari + dropdown kategori **otomatis** (bertambah saat produk berkategori baru ditambahkan) + toggle Kartu/Tabel; modal Restock/Edit ("Tambah stok barang baru datang" + "Stok setelah disimpan"); modal Tambah (kode BRG-### otomatis, stok awal, batas menipis default 10, foto); hapus dg konfirmasi. Pilihan barang di Catat Bon/Barang Keluar = isi Master Item.
### KARYAWAN
**Pengantaran** (3 kartu; tab Akan diantar/Selesai; badge BERIKUTNYA/Baru; daftar barang; catatan kasir; Detail & Ubah Status: stepper Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai; **foto bukti wajib** disimpan di disk privat; badge tugas baru di menu). **Profil**.

## Alur Status Bon
Belum Dikirim → Dikirim ke Karyawan → Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai; setiap perubahan tercatat di `log_status_pengiriman` (jam tampil di timeline). Sisa = total − terbayar; Lunas bila terbayar ≥ total; Lewat tempo bila belum lunas & lewat jatuh tempo.

## Keamanan
Password hash; rate limit login/OTP; CSRF; validasi server (hitung ulang total di server); upload jpg/png/webp ≤ 4 MB nama acak; bukti pengiriman privat; `activity_logs` append-only; akun MySQL berlapis (`pj_app`, `pj_migrator`, `pj_report`, `pj_backup`); HTTPS & `APP_DEBUG=false` di produksi.

## Aturan Kerja
1. Urutan: setup → migration → view/trigger/procedure → model/command admin → logo & design system → auth/profil → master item → barang keluar → saldo laci → bon → pengantaran → bahan harian & kehadiran → owner → admin → QA.
2. Test Pest untuk service, trigger/procedure, akses 4 role, dan **database kosong**.
3. Jangan menambah package di luar daftar; jangan ubah migration yang sudah merge; jangan commit `.env`/dump.
4. Bila ambigu, ikuti PDF; catat asumsi di akhir.

## Definisi Selesai
Semua 33 layar PDF berfungsi sesuai desain dengan data dari database; foto toko & logo terpasang; checklist E2E `PROGRESS_TRACKER.md` lulus; tidak ada celah akses lintas role.
