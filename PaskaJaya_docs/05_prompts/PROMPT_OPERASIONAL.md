# PROMPT: Bahan Harian & Kehadiran

| Atribut | Isi |
|---|---|
| PIC | Chyntia (Bahan Harian), Jona (Kehadiran) |
| Branch | `feat/daily-materials, feat/attendance` |
| Bergantung pada | Auth |
| Dokumen terkait | `F13_KASIR_BON_MATERIALS.md`, `F09_ATTENDANCE.md` |

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

## TUGAS A: Bahan Harian (tabel `penggunaan_bahan`)
Kartu kiri "Tambah bahan": nama bahan, jumlah + satuan, harga (Rp), tanggal, keterangan; error merah "Nama bahan, jumlah, dan harga wajib diisi."; tombol Tambah. Kartu kanan "Daftar Bahan Harian" + "Total pengeluaran Rp …": Tanggal, Bahan, Jumlah, Harga, Keterangan, Aksi (edit, hapus + konfirmasi). Pencatat = kasir login.
## TUGAS B: Kehadiran (tabel `absensi_karyawan` + `detail_pekerjaan_absensi`)
- Kasir (`Kasir\Kehadiran`): "+ Tambah Kehadiran"; filter cari nama/pekerjaan, dropdown "Semua karyawan", tanggal; tabel No, Nama Karyawan, Tanggal, Pekerjaan (chip), Aksi (pensil, hapus). Modal Tambah/Edit: karyawan (role karyawan aktif), tanggal (default hari ini), shift (opsional), pekerjaan dinamis ("+ Tambah pekerjaan"). Hapus: "Hapus baris kehadiran? Catatan {nama} tanggal {tanggal} akan dihapus."
- Owner (`Owner\Kehadiran`): read-only + badge "Hanya Lihat". Tulis → 403.
- Unik `(karyawan_id, tanggal)`; simpan header + detail dalam satu transaksi.
## ACCEPTANCE
CRUD berfungsi; total pengeluaran akurat; owner 403 untuk tulis; database kosong → empty state; test.
````
