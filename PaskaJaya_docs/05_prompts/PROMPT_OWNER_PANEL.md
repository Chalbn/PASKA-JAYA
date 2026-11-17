# PROMPT: Panel Owner: Ringkasan & Laporan

| Atribut | Isi |
|---|---|
| PIC | Jeli |
| Branch | `feat/owner-dashboard, feat/owner-sales-report` |
| Bergantung pada | Kasir Penjualan, Kasir Saldo, Bon |
| Dokumen terkait | `F05_OWNER_DASHBOARD.md`, `F06_SALES_REPORT.md` |

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

## TUGAS: Owner (F05, F06) PROMPT_OWNER_PANEL.md
1. **Ringkasan Toko**: 4 kartu — Penjualan hari ini (+ jumlah transaksi), Saldo cash (laci) (+ transfer), Stok menipis (`v_stok_menipis`), Bon belum lunas (Σ sisa + jumlah pelanggan, `v_piutang_aktif`). **Grafik "Penjualan 7 Hari Terakhir" berisi data nyata** (bar Chart.js dari `v_penjualan_harian`; hari tanpa transaksi = 0; label Sen–Min). Panel Stok Menipis (link "Kelola" → Master Item, badge "6 Dus"), Bon Belum Lunas (jatuh tempo + sisa), Transaksi Terbaru (link "Laporan").
2. **Laporan Barang Keluar**: filter Dari/Sampai tanggal (default 7 hari terakhir) + Metode (Semua/Cash/Transfer); 3 kartu (Total penjualan + n transaksi, Cash, Transfer) mengikuti filter; tabel ID, Waktu, Pelanggan, Metode, Total, tombol **Rincian** (baris meluas → item; tombol jadi **Tutup**); tombol **Unduh CSV** sesuai filter (`laporan-barang-keluar-YYYYMMDD.csv`). Data via `CALL sp_laporan_penjualan`.
3. Paginasi + indeks `penjualan.waktu_transaksi`.
## ACCEPTANCE
Angka dashboard = angka laporan; CSV sesuai filter; grafik tampil walau data kosong; test LaporanService.
````
