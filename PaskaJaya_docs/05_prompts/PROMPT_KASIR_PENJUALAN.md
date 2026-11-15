# PROMPT: Kasir: Beranda & Barang Keluar

| Atribut | Isi |
|---|---|
| PIC | Patricia (+Chyntia UI) |
| Branch | `feat/kasir-sales` |
| Bergantung pada | Master Item |
| Dokumen terkait | `F11_KASIR_SALES.md`, ADR-005 |

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

## TUGAS: Beranda Kasir & Barang Keluar (F11)
1. **Beranda Kasir**: kartu biru "SALDO LACI HARI INI" (total + "Reset otomatis setiap hari 00:00", tombol Reset Manual, sub-kartu Saldo Cash & Saldo Transfer — dari `kas_laci` aktif); Transaksi Terbaru (dari kas_laci); 3 kartu (Transaksi hari ini, Stok menipis, Bon belum lunas); 4 quick link: **Barang Keluar → /kasir/barang-keluar, Catat Bon → /kasir/catat-bon, Bahan Harian → /kasir/bahan-harian, Kehadiran → /kasir/kehadiran**.
2. **Barang Keluar** (`Kasir\BarangKeluar`): (1) pilih barang dari master item + jumlah + Tambah; (2) keranjang (kosong: "Belum ada barang. Pilih barang lalu klik Tambah."; baris: nama, jumlah editable, harga, subtotal, hapus; barang sama digabung); (3) pembayaran: nama pelanggan opsional (default "Umum"), metode CASH | TRANSFER, kotak "Total bayar", tombol Simpan Transaksi. Kanan: Riwayat Barang Keluar.
3. `PenjualanService::simpanLangsung`: transaksi DB — hitung ulang total di server, buat kode `TRX-####` berurutan (aman dari race condition), insert `penjualan(tipe='langsung')` + `detail_penjualan` (snapshot). Trigger T2 memotong stok & `mutasi_stok`; T3 mengisi `kas_laci`. Error stok kurang → pesan "Stok {nama} tidak cukup (tersisa N)".
4. Catat `activity_logs`.
## ACCEPTANCE
Stok turun, saldo laci naik sesuai metode, kode TRX berurutan, stok kurang ditolak tanpa mengubah data; test service + komponen; database kosong tampil rapi.
````
