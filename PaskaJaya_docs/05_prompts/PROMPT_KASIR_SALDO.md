# PROMPT: Kasir: Saldo Laci, Grafik Mingguan, Riwayat

| Atribut | Isi |
|---|---|
| PIC | Patricia (+Chyntia UI) |
| Branch | `feat/kasir-balance` |
| Bergantung pada | Kasir Penjualan |
| Dokumen terkait | `F12_KASIR_BALANCE.md`, ADR-008 |

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

## TUGAS: Saldo Laci (F12)
1. **Lihat Saldo**: kartu saldo + Reset Manual, Transaksi Terbaru, tabel "Riwayat 7 Hari Terakhir" (Tanggal, Cash, Transfer, Total, Direset oleh) dari `penutupan_laci` + link "Lihat Semua".
2. **Reset**: manual → konfirmasi → `CALL sp_reset_laci(user_id,'manual')`; otomatis → command `laci:reset` 00:00 → `sp_reset_laci(NULL,'otomatis')`. "Direset oleh" NULL = "Sistem".
3. **Grafik Mingguan**: dropdown Minggu ini | Minggu lalu | Pilih tanggal; kartu Total penjualan (rentang), Hari tertinggi, Dibanding minggu lalu (± %, "Penjualan meningkat/menurun"); **stacked bar** Cash + Transfer per hari (Senin–Minggu) dari `v_penjualan_harian`; tooltip Rupiah; data kosong → nilai 0.
4. **Riwayat Saldo Laci**: filter tanggal; tabel (Tanggal, Saldo Cash, Saldo Transfer, Total, Waktu Reset, Direset oleh, Detail); modal "Rincian {tanggal}" (Waktu, Keterangan, Metode, Nominal dari `kas_laci`); tombol **Excel** & **PDF** (Export class + dompdf).
## ACCEPTANCE
Angka laci = Σ kas_laci; reset otomatis & manual tercatat; grafik 3 rentang benar; ekspor menghasilkan file valid; test LaciService.
````
