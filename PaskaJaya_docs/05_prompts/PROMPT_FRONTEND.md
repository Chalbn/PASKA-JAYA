# PROMPT: Design System & Layout per Role

| Atribut | Isi |
|---|---|
| PIC | Jeli |
| Branch | `feat/design-system-layout` |
| Bergantung pada | Setup Project, Logo |
| Dokumen terkait | `UI_UX_GUIDELINES.md`, PDF desain |

## PROMPT
````
Kamu adalah senior Laravel developer. Proyek: **PASKA JAYA** — sistem manajemen Toko Bahan Bangunan (Laravel 13 + Livewire 3 + Tailwind CSS + MySQL 8, dev di Laragon).
BACA DULU: `PRD_PASKAJAYA.md`, `02_architecture/DATABASE_SCHEMA.md` + `ADR/`, fitur terkait di `03_features/`, `01_guides/AGENT_GUIDELINES.md`, dan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf`.
ATURAN KERAS:
- GIT: JANGAN jalankan perintah git apa pun (add, commit, push, branch, checkout, PR). Saya yang commit sendiri. Abaikan semua instruksi branch/commit/PR di file prompt. Jangan menambahkan "Co-Authored-By" atau atribusi AI di file mana pun.
- Database dimulai KOSONG. JANGAN membuat seeder/data dummy. Nama & angka di PDF hanya contoh tampilan; semua data diambil dari database.
- Nama tabel/kolom persis `DATABASE_SCHEMA.md`. Jangan ubah migration yang sudah merge.
- Logika kritis stok/kas/status bon mengikuti trigger & stored procedure (ADR-007).
- Semua halaman wajib punya empty state; kartu/grafik bernilai 0 saat data kosong.
- Otorisasi di server (middleware `role:`, Policy), bukan hanya menyembunyikan menu.
- UI hanya Tailwind + komponen `x-ui.*`; teks Bahasa Indonesia sesuai PDF; uang `Rp 1.820.000`; zona Asia/Jakarta.
FORMAT LAPORAN AKHIR: ✅ Selesai · 📁 File · 🧪 Test · ⚠️ Asumsi · ➡️ Berikutnya.

## TUGAS: Design System & Layout
1. Komponen Blade `x-ui.*`: button (primary/outline/danger/ghost), card, stat-card, badge (varian lunas, belum-lunas, selesai, kritis, cash, transfer, lewat-tempo), modal, confirm, input (ikon kiri, error), select/combobox (bisa ketik opsi baru), date, table, pill-filter, toggle (Kartu|Tabel), timeline (Alur Status + jam), empty-state, upload (preview + kamera), toast, spinner.
2. `x-layouts.app`: sidebar gradien biru (logo, label "MENU {ROLE}", item aktif pil putih, Keluar), topbar (tanggal Indonesia, judul, avatar+nama+role). Menu per role dari satu konfigurasi (`config/menu.php`):
 - Admin: Ringkasan Sistem, Manajemen Pengguna, Tim
 - Owner: Ringkasan, Laporan Barang Keluar, Daftar Bon, Master Item, Kehadiran, Profil
 - Kasir: Beranda, Barang Keluar, Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, Catat Bon, Daftar Bon, Bahan Harian, Kehadiran, Master Item, Profil
 - Karyawan: Pengantaran (badge merah jumlah tugas baru), Profil
3. `x-layouts.auth`: split-screen — kiri **`public/images/foto-toko.jpg`** + overlay biru gradien + logo + headline "Every sack, bar, and delivery in one place." + subteks; kanan kartu form. **Foto toko ini menggantikan foto di desain** (Login & Register).
4. Komponen logo `x-brand.logo` & `x-brand.mark` memakai file hasil `PROMPT_LOGO` (fallback teks "PJ" bila belum ada).
5. Helper Chart.js (Alpine) untuk bar & stacked bar; tooltip Rupiah.
6. Responsif: sidebar → drawer < lg; tabel scroll horizontal.
7. Halaman galeri `/dev/ui` (hanya `APP_ENV=local`).
## ACCEPTANCE
Tampilan identik gaya PDF di desktop & mobile; tidak ada warna hardcode di luar token; semua komponen punya contoh di galeri.
````
