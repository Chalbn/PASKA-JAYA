# PROMPT: QA, UAT, Polish & Deploy

| Atribut | Isi |
|---|---|
| PIC | Jona + semua |
| Branch | `feat/qa-polish → release/1.0.0` |
| Bergantung pada | Semua prompt |
| Dokumen terkait | `TESTING_STRATEGY.md`, `06_reports/PROGRESS_TRACKER.md` |

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

## TUGAS: QA & Deploy PROMPT_QA_DEPLOY.md
1. Jalankan seluruh checklist E2E di `PROGRESS_TRACKER.md` pada database kosong lalu isi data nyata uji; perbaiki bug.
2. Audit hak akses: uji matriks 4 role × semua route (PRD §6) → 200/403/redirect.
3. Audit N+1, indeks, empty state, loading, error, responsif mobile (terutama Karyawan).
4. Bandingkan tiap layar dengan PDF (`SCREEN_MAPPING.md`): teks, urutan, badge, warna; pastikan foto toko & logo terpasang di Login/Register.
5. Uji backup & restore; uji privilege akun MySQL.
6. UAT dengan pemilik/kasir toko (skenario harian: jual, bon, kirim, tutup laci).
7. Deploy: ikuti Pre-Deploy Checklist `SECURITY_GUIDELINES.md`; `release/1.0.0` → `main` + tag `v1.0.0`.
8. Tulis `06_reports/REPORT_<tanggal>_RELEASE.md` dan finalisasi laporan MSBD.
## ACCEPTANCE
Semua checklist lulus; coverage Service ≥ 80%; tidak ada celah akses lintas role; mitra menyetujui UAT.
````
