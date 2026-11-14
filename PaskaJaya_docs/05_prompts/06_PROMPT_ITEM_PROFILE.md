# PROMPT 06 — Master Item & Profil (F08, F10)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. **Master Item** (`Shared/Items`, rute owner & kasir sama) sesuai `F08_MASTER_ITEM.md`: 4 kartu statistik, pencarian, dropdown kategori otomatis (dari categories yang punya item), toggle Kartu/Tabel (simpan di query `view`), modal Restock/Edit (tambah stok + preview "Stok setelah disimpan"), modal Tambah (kode otomatis BRG-###, combobox kategori yang membuat kategori baru), konfirmasi Hapus (soft delete), unggah foto (disk public).
 - ItemService: `restock` → stock_movement + `last_restocked_at`.
2. **Profil** (`Shared/Profile`) sesuai `F10_PROFILE.md` untuk semua role: data akun, hapus akun (konfirmasi password), data profil + foto, ubah password.
## Acceptance
Produk baru kategori baru → muncul di dropdown filter; restock menambah stok & tanggal masuk; test CRUD + restock + kategori; profil lulus validasi.
