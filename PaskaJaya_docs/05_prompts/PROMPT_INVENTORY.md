# PROMPT: Master Item (Owner & Kasir)

| Atribut | Isi |
|---|---|
| PIC | Patricia |
| Branch | `feat/master-item` |
| Bergantung pada | Auth, Frontend |
| Dokumen terkait | `F08_MASTER_ITEM.md`, ADR-010 |

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

## TUGAS: Master Item (F08)
Komponen `Shared\MasterItem\Index` dipakai route `/owner/master-item` dan `/kasir/master-item` (akses penuh keduanya).
1. 4 kartu: Total Barang (+jumlah kategori), Nilai Stok (Σ stok × harga_beli, "Berdasarkan harga beli"), Stok Menipis (`v_stok_menipis`), Stok Aman.
2. Pencarian "Cari bahan/barang atau kode…" (live debounce) + dropdown kategori **otomatis** dari `kategori_barang` yang punya barang aktif (ADR-010) + toggle **Kartu | Tabel** (`#[Url] view`). Info "n barang · n stok menipis".
3. Kartu: foto, nama, kode, ikon hapus, badge stok ("Stok: 142" / "Kritis: 4"), harga jual. Tabel: No, Kode, Nama, Kategori, Satuan, Harga Beli, Harga Jual, Stok (badge), Tanggal Masuk, Aksi (pensil, hapus).
4. Modal **Tambah Barang**: kode otomatis `BRG-###` (editable, unik), nama, kategori (combobox, ketik baru → dibuat otomatis), satuan, harga beli/jual, stok awal (→ `mutasi_stok.stok_awal`), batas stok menipis (default 10), foto (opsional).
5. Modal **Restock / Edit**: semua field + "Tambah stok barang baru datang (stok saat ini: N)" dengan preview "Stok setelah disimpan: N+x". Penambahan stok memanggil `CALL sp_restock_barang`. Perubahan harga dicatat ke `activity_logs`.
6. **Hapus**: dialog "Hapus barang? Barang 'X' akan dihapus dari master item." → soft delete.
7. Validasi: angka ≥ 0, kode unik, peringatan bila harga jual < harga beli. Foto → disk public (`FileService`).
## ACCEPTANCE
Barang berkategori baru → kategori muncul di filter; restock menambah stok & tanggal masuk; test CRUD, restock, filter, kategori otomatis; database kosong menampilkan empty state "Belum ada barang".
````
