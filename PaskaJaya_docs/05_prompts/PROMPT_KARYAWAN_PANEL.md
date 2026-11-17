# PROMPT: Panel Karyawan: Pengantaran

| Atribut | Isi |
|---|---|
| PIC | Chyntia |
| Branch | `feat/delivery-karyawan` |
| Bergantung pada | Bon |
| Dokumen terkait | `F14_KARYAWAN_DELIVERY.md`, ADR-011 |

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

## TUGAS: Pengantaran (F14)
1. **Daftar** (`Karyawan\Pengantaran\Index`): 3 kartu (Pengantaran baru, Sedang berjalan, Selesai); tab "Akan diantar (n)" / "Selesai (n)"; kartu bon: badge BERIKUTNYA (jadwal terdekat) / Baru (`dibaca_pada IS NULL`), status, pelanggan, alamat, DAFTAR BARANG + jumlah (dari `detail_pengiriman`), jadwal, "Catatan kasir", tombol "Detail & Ubah Status →" / "Lihat Detail →". Hanya pengiriman milik karyawan yang login (Policy). Badge merah di menu = jumlah baru.
2. **Detail** (`…\Detail`): tombol ← Kembali; "Detail Pengantaran — Bon {pelanggan} · Rp total"; kartu Informasi bon; kartu Status pengantaran: stepper **Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai**, area "Foto bukti selesai diantar (wajib)" + "Pilih / ambil foto" (`capture="environment"`), tombol "Ubah Status → …" (disabled sampai foto ada saat menuju Selesai).
3. Ubah status → update `pengiriman_barang` + insert `log_status_pengiriman`; saat Selesai trigger T6 mewajibkan `foto_bukti` dan mengisi `waktu_selesai`. Foto disimpan di disk **privat**; route `/berkas/bukti-pengiriman/{id}` dengan Policy (owner, kasir, karyawan pemilik).
4. Membuka detail pertama kali mengisi `dibaca_pada`.
5. Optimasi HP: tombol besar, layout satu kolom.
## ACCEPTANCE
Perubahan status langsung terlihat di Kasir & Owner; selesai tanpa foto ditolak; karyawan lain 403; bukti tidak dapat diakses tanpa login; test.
````
