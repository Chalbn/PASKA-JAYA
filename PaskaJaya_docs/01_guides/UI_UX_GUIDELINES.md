# PASKA JAYA — UI/UX Guidelines (diturunkan dari desain PDF)

> Referensi: `07_extras/referensi_desain/uiux_paska_jaya.pdf`. Implementasi: Blade components + Tailwind + Livewire.

## 1. Brand
- **Logo**: kotak rounded putih/biru berisi "PJ" + wordmark **PASKA JAYA** + subteks **TOKO BAHAN BANGUNAN** (letter-spaced kecil). Referensi: `07_extras/aset/logo_referensi.png`. Prompt pembuatan: `05_prompts/PROMPT_LOGO.md`.
- **Foto toko**: `07_extras/aset/foto_toko.jpg` → `public/images/foto-toko.jpg`. **Dipakai sebagai gambar kiri halaman Login & Register** (menggantikan foto "Toko Bangunan Indosatu" pada desain), dengan overlay biru gradien.
- Tagline: "Every sack, bar, and delivery in one place."

## 2. Layout Aplikasi
- **Sidebar kiri** gradien biru (navy `#1E3A8A` → biru `#3B5BDB`), lebar ±220px: logo, label menu kecil kapital ("MENU ADMIN/OWNER/KASIR/KARYAWAN"), item aktif = pil putih teks biru, tombol **Keluar** di bawah. Mobile: drawer.
- **Topbar** gradien biru: ikon halaman + tanggal ("Sabtu, 14 November 2026") + judul halaman; kanan: avatar inisial + nama + role.
- **Konten**: latar biru sangat muda → putih; judul bold navy + subjudul abu.
- **Kartu**: putih/kaca, radius 16–20px, bayangan lembut, border biru muda.

## 3. Token
| Token | Nilai |
|---|---|
| Primary | `#1E40AF` / `#2F4FD8` |
| Navy gelap | `#1E3A8A` |
| Cyan muda (kartu statistik) | `#E0F2FE` |
| Kuning (belum lunas) | `#FEF3C7` / teks `#92400E` |
| Hijau/Selesai | navy + ✓ (sesuai desain) |
| Merah (kritis/hapus) | `#C81E1E`, latar `#FEE2E2` |
| Font | **Plus Jakarta Sans** (fallback DM Sans, system-ui) |
Badge: pil. `Lunas` navy ✓ · `Belum Lunas` kuning · `Selesai` navy ✓ · `Kritis/Menipis` merah muda · `Lewat tempo` merah · `CASH` navy solid · `TRANSFER` biru muda.

## 4. Pola Komponen (Blade `x-ui.*`)
button · card · stat-card · badge · modal · confirm · input (ikon kiri, error) · select/combobox · date · table · pill-filter (Semua|Belum lunas|Lunas) · toggle (Kartu|Tabel) · timeline (Alur Status) · empty-state · upload (preview + kamera) · toast.
- Modal: judul kiri, X kanan, **Batal** (outline) + **Simpan** (primary). Hapus: tombol merah "Ya, hapus".
- Tabel: header kapital kecil abu, baris zebra halus, aksi ikon.
- Timeline vertikal: lingkaran ✓ biru + garis; setiap langkah ada tanggal & jam.
- Badge merah jumlah tugas baru di menu Pengantaran.

## 5. Interaksi Livewire
- Loading: `wire:loading` pada tombol simpan (disable + spinner).
- Pencarian live dengan debounce 300ms.
- Konfirmasi sebelum aksi destruktif.
- Toast sukses/gagal lewat event `toast`.
- Grafik: Chart.js di Alpine; tooltip nominal Rupiah.

## 6. Halaman Auth
Split-screen: kiri **foto toko** + overlay biru + headline **"Every sack, bar, and delivery in one place."** + subteks "Stock, cashier, and delivery tracking for your building-material store, all in a single system."; kanan kartu form (Welcome back / Create Account). **Tanpa kotak demo account.**

## 7. Empty State (database kosong)
Setiap daftar/kartu/grafik punya empty state: ikon + teks abu + tombol aksi bila relevan (mis. "Belum ada barang. Klik + Tambah Barang."). Kartu statistik menampilkan 0. Grafik 7 hari/mingguan tetap digambar dengan nilai 0.

## 8. Responsif & Aksesibilitas
Breakpoint sm/md/lg; sidebar → drawer < `lg`; tabel `overflow-x-auto`; halaman Karyawan dioptimalkan untuk HP (tombol besar, input kamera `capture="environment"`). Kontras AA, fokus terlihat, label jelas, `aria-label` pada tombol ikon.

## 9. Teks Resmi (ikuti PDF)
Judul halaman & label menu persis sesuai PDF (mis. "Ringkasan Sistem", "Laporan Barang Keluar", "Catat Bon", "Bahan Harian"). Format uang `Rp 1.820.000`; tanggal `14 November 2026`; zona waktu Asia/Jakarta.
