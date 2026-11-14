# PROMPT 04 — Design System & Layout per Role

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
Implementasikan `01_guides/UI_UX_GUIDELINES.md`:
1. Komponen UI: Button (primary/outline/danger/ghost), Card (glass), Badge (varian lunas/belum-lunas/selesai/kritis/cash/transfer), Modal, ConfirmDialog, Input (ikon kiri, error), Select/Combobox, DateInput, Table, Tabs/PillFilter, StatCard, Timeline, EmptyState, ImageUpload (preview + kamera), Toast, Spinner.
2. `AppLayout`: sidebar gradien biru + topbar (tanggal Indonesia, judul halaman, avatar+nama+role). Menu per role dari `lib/menu.ts`:
 - Admin: Ringkasan Sistem, Manajemen Pengguna, Tim
 - Owner: Ringkasan, Laporan Barang Keluar, Daftar Bon, Master Item, Kehadiran, Profil
 - Kasir: Beranda, Barang Keluar, Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, Catat Bon, Daftar Bon, Bahan Harian, Kehadiran, Master Item, Profil
 - Karyawan: Pengantaran (badge merah jumlah baru), Profil
 Tombol **Keluar** di bawah. Responsif (drawer mobile).
3. `AuthLayout` split-screen (foto toko + headline) — placeholder gradien bila foto belum ada.
4. Halaman galeri komponen `/dev/ui` (hanya local).
## Acceptance
Layout identik gaya PDF di desktop & mobile; komponen reusable; tidak ada warna hardcode di luar token.
