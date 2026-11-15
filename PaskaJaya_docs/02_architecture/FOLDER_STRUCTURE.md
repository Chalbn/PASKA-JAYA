# PASKA JAYA — Folder Structure (Laravel 11 + Livewire 3)

```
paska-jaya/
├─ app/
│  ├─ Console/Commands/        BuatAdmin (app:buat-admin), ResetLaci (laci:reset), RunBackup (backup:run)
│  ├─ Enums/                   RolePengguna, StatusPengguna, MetodeBayar, TipePenjualan, StatusBayar, StatusPengiriman
│  ├─ Http/Middleware/         PastikanRole (alias `role`), PastikanAktif
│  ├─ Livewire/
│  │  ├─ Auth/                 Login, Register, VerifikasiOtp, LupaPassword
│  │  ├─ Admin/                Dashboard, Pengguna/Index, Tim/Index
│  │  ├─ Owner/                Dashboard, LaporanBarangKeluar, DaftarBon, Kehadiran
│  │  ├─ Kasir/                Beranda, BarangKeluar, LihatSaldo, GrafikMingguan, RiwayatSaldoLaci,
│  │  │                        CatatBon, DaftarBon, BahanHarian, Kehadiran
│  │  ├─ Karyawan/Pengantaran/ Index, Detail
│  │  ├─ Shared/               MasterItem/Index, Profil
│  │  └─ Forms/                BarangForm, BonForm, PenggunaForm, AbsensiForm (Livewire Form Objects)
│  ├─ Models/                  Role, User, SystemConfig, ActivityLog, KategoriBarang, Barang, MutasiStok,
│  │                           PenggunaanBahan, Pelanggan, Penjualan, DetailPenjualan, BonPiutang, PembayaranBon,
│  │                           PengirimanBarang, DetailPengiriman, LogStatusPengiriman, KasLaci, PenutupanLaci,
│  │                           AbsensiKaryawan, DetailPekerjaanAbsensi, AnggotaTim, OtpCode
│  ├─ Observers/               AuditObserver (tulis activity_logs)
│  ├─ Policies/                BarangPolicy, BonPiutangPolicy, PenggunaPolicy, AbsensiPolicy
│  ├─ Services/                PenjualanService, BonService (panggil stored procedure), LaciService,
│  │                           LaporanService, StatusSistemService, FileService
│  └─ Exports/                 LaporanPenjualanExport, RiwayatLaciExport
├─ database/
│  ├─ migrations/              (tabel → view → trigger → stored procedure; lihat urutan di DATABASE_SCHEMA.md)
│  ├─ factories/               HANYA untuk test
│  └─ seeders/                 DatabaseSeeder kosong; RoleSeeder (4 role) & ConfigSeeder (konfigurasi sistem)
├─ resources/
│  ├─ css/app.css              Tailwind + token warna
│  ├─ js/app.js                Alpine plugins, Chart.js helper
│  └─ views/
│     ├─ components/           ui/ (button, card, badge, modal, confirm, input, select, table, stat, timeline, empty-state, upload)
│     │                        layouts/ (app, auth), brand/ (logo)
│     └─ livewire/             (mengikuti struktur app/Livewire)
├─ public/images/              logo.svg, logo-mark.svg, favicon, foto-toko.jpg  (aset statis brand)
├─ routes/web.php              grup per role
├─ storage/app/private/        bukti-pengiriman/ (foto bukti — PRIVAT)
├─ storage/app/public/         foto-barang/, foto-profil/, foto-tim/
└─ tests/Feature, tests/Unit, tests/Database   (trigger & stored procedure)
```
