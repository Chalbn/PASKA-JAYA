# PROMPT: Bon & Piutang (Catat Bon, Daftar Bon Kasir & Owner)

| Atribut | Isi |
|---|---|
| PIC | Jona (+Chyntia UI) |
| Branch | `feat/bon-kasir, feat/bon-owner-readonly` |
| Bergantung pada | Master Item, Kasir Saldo |
| Dokumen terkait | `F07_BON_MANAGEMENT.md`, `F13_KASIR_BON_MATERIALS.md`, ADR-005, ADR-006 |

## PROMPT
````
Kamu adalah senior Laravel developer. Proyek: **PASKA JAYA** — sistem manajemen Toko Bahan Bangunan (Laravel 11 + Livewire 3 + Tailwind CSS + MySQL 8, dev di Laragon).
BACA DULU: `PRD_PASKAJAYA.md`, `02_architecture/DATABASE_SCHEMA.md` + `ADR/`, fitur terkait di `03_features/`, `01_guides/AGENT_GUIDELINES.md`, dan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf`.
ATURAN KERAS:
- Database dimulai KOSONG. JANGAN membuat seeder/data dummy. Nama & angka di PDF hanya contoh tampilan; semua data diambil dari database.
- Nama tabel/kolom persis `DATABASE_SCHEMA.md`. Jangan ubah migration yang sudah merge.
- Logika kritis stok/kas/status bon mengikuti trigger & stored procedure (ADR-007).
- Semua halaman wajib punya empty state; kartu/grafik bernilai 0 saat data kosong.
- Otorisasi di server (middleware `role:`, Policy), bukan hanya menyembunyikan menu.
- UI hanya Tailwind + komponen `x-ui.*`; teks Bahasa Indonesia sesuai PDF; uang `Rp 1.820.000`; zona Asia/Jakarta.
- Kerja di branch jatah, commit Conventional Commits, PR ke `develop`. Setelah selesai update `04_plans/TASK_DISTRIBUTION.md` dan `06_reports/PROGRESS_TRACKER.md`.
FORMAT LAPORAN AKHIR: ✅ Selesai · 📁 File · 🧪 Test · ⚠️ Asumsi · ➡️ Berikutnya.

## TUGAS: Bon (F07, F13)
1. **Catat Bon** (`Kasir\CatatBon`): (1) Data bon — nama pelanggan, alamat pengantaran (opsional), tanggal (default hari ini), jatuh tempo; (2) Tambah barang — select master item ("BRG-001 · Nama (stok N)"), jumlah, harga satuan (default harga jual, **editable**), subtotal; (3) Keranjang bon — tabel editable + hapus; footer **TOTAL BON** + Simpan Bon. Alert: "Bon yang dibuat otomatis berstatus Belum Dikirim, dan hanya terlihat oleh Kasir dan Owner. Stok berkurang saat bon dikirim."
2. `BonService::buat`: transaksi — cari/buat `pelanggan` berdasarkan nama; insert `penjualan(tipe='bon')` + `detail_penjualan` + `bon_piutang` (kode `BON-####`) + `log_status_pengiriman('belum_dikirim')`. Tidak memotong stok.
3. **Daftar Bon Kasir** (`Kasir\DaftarBon`): filter Semua | Belum lunas | Lunas; kolom No, Pelanggan (jatuh tempo + badge Lewat tempo), Ringkasan Barang (chip maks 3 + "+n"), Total (+ "Sisa Rp"), Status Pembayaran, Status Pengantaran (+ nama karyawan), Aksi.
4. Aksi Kasir: **Kirim ke Karyawan** (modal: karyawan aktif, jadwal, catatan → `CALL sp_kirim_bon`), **Ganti Karyawan**, **Batalkan Pengiriman** (`sp_batalkan_pengiriman`), **Tandai Lunas** (insert `pembayaran_bon` = sisa + metode), **Bayar Sebagian** (nominal ≤ sisa + metode), **Detail**, **Hapus** (hanya bila belum dikirim & belum ada pembayaran). Tombol tidak relevan tampil disabled.
5. **Modal Detail Bon**: pelanggan + badge status kirim, alamat, tabel BARANG/JML/SUBTOTAL, Total bon/Terbayar/Sisa, kotak Karyawan/Jadwal/Catatan, timeline "ALUR STATUS" (5 langkah dengan jam dari `log_status_pengiriman`); bila Selesai tampil "Foto bukti selesai diantar" (via route berkas privat).
6. **Daftar Bon Owner** (`Owner\DaftarBon`): sama + badge "Hanya Lihat" + alert "Mode baca saja…"; kolom Detail (Lihat Detail). Semua aksi tulis → 403 (Policy).
7. Catat `activity_logs` untuk kirim/batal/bayar/hapus.
## ACCEPTANCE
Sisa & lewat tempo benar; stok turun hanya saat kirim dan kembali saat batal; pembayaran menambah kas laci; Owner tidak dapat menulis (test 403); test BonService lengkap.
````
