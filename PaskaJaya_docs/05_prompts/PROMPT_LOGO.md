# PROMPT: Logo PASKA JAYA

| Atribut | Isi |
|---|---|
| PIC | Jeli (UI/UX) |
| Branch | `feat/design-system-layout` |
| Referensi | `07_extras/aset/logo_referensi.png` (logo pada desain PDF) |
| Hasil | `public/images/logo.svg`, `logo-mark.svg`, `logo-white.svg`, `favicon.ico`, `apple-touch-icon.png` |

## Brief Singkat
Logo mengikuti gaya referensi: **kotak rounded dengan monogram "PJ"** di kiri, dan di kanan wordmark **PASKA JAYA** (tebal, huruf kapital) di atas subteks **TOKO BAHAN BANGUNAN** (kecil, kapital, letter-spacing lebar). Kesan: modern, tepercaya, bersih — cocok untuk toko bahan bangunan (UMKM). Dipakai di sidebar (latar biru gradien → versi putih), halaman Login/Register (di atas foto toko), favicon, dan dokumen.

## A. Prompt untuk AI Pembuat Gambar (Midjourney / DALL·E / Ideogram / Firefly)
```
Flat vector logo for an Indonesian building-material store called "PASKA JAYA".
Left: a rounded-square app-icon style badge (corner radius ~22%) with a soft royal-blue to navy gradient (#3B5BDB to #1E3A8A) containing the white bold monogram letters "PJ" in a geometric sans-serif; the "J" descends slightly and subtly forms a stylized construction beam / corner bracket. 
Right: wordmark "PASKA JAYA" in bold uppercase geometric sans-serif (Plus Jakarta Sans / Manrope style), navy blue (#1E3A8A); beneath it a small tagline "TOKO BAHAN BANGUNAN" in uppercase with wide letter-spacing, medium gray-blue (#64748B).
Style: minimal, modern, clean, professional, flat vector, crisp edges, balanced spacing, horizontal lockup, no 3D, no photo, no gradients inside text, no mockup, no shadows. 
Background: pure white (also provide on transparent). Aspect ratio 16:5.
```
**Negative prompt**: `3d, bevel, emboss, realistic, photo, clutter, extra text, watermark, handwriting font, cartoon, tools clipart, low contrast, blurry, jagged edges`.
**Variasi yang diminta**: (1) horizontal lockup penuh, (2) monogram saja (kotak "PJ"), (3) versi putih untuk latar biru, (4) versi 1 warna (navy) untuk cetak.

## B. Prompt untuk AI Coding Agent (membuat SVG langsung)
```
Buat logo PASKA JAYA sebagai file SVG murni (tanpa gambar raster, tanpa font eksternal — ubah teks menjadi path atau gunakan font stack aman):
1. public/images/logo-mark.svg — viewBox 0 0 64 64: kotak rounded (rx=14) gradien linear #3B5BDB → #1E3A8A (diagonal), monogram "PJ" putih bold geometris di tengah.
2. public/images/logo.svg — viewBox 0 0 360 96: logo-mark di kiri (64×64, margin 16), di kanan teks "PASKA JAYA" (bold, kapital, #1E3A8A, ±30px) dan di bawahnya "TOKO BAHAN BANGUNAN" (kapital, letter-spacing 0.28em, #64748B, ±11px).
3. public/images/logo-white.svg — versi untuk latar biru: kotak putih berisi "PJ" biru (#2F4FD8), teks putih.
4. public/images/favicon.svg + favicon.ico (32×32) dari logo-mark; apple-touch-icon.png 180×180.
Syarat: latar transparan, rapi di ukuran 24px sampai 400px, kontras AA, tidak memakai elemen berhak cipta. Buat komponen Blade <x-brand.logo variant="default|white" /> dan <x-brand.mark /> yang memakai file tersebut, lalu pasang di sidebar (versi white), layout auth, dan <head> (favicon).
```

## C. Spesifikasi Desain
| Elemen | Nilai |
|---|---|
| Warna utama | Navy `#1E3A8A`, Biru `#3B5BDB` / `#2F4FD8` |
| Warna sekunder | Abu-biru `#64748B`, Putih `#FFFFFF` |
| Tipografi | Plus Jakarta Sans (alternatif: Manrope, DM Sans) — Bold untuk wordmark |
| Area aman | margin ≥ ½ tinggi monogram di semua sisi |
| Ukuran minimum | monogram 24 px; lockup horizontal 120 px lebar |
| Larangan | memutar, meregangkan, mengganti warna di luar palet, menaruh di atas foto tanpa overlay |

## D. Foto Toko untuk Login & Register
File: `07_extras/aset/foto_toko.jpg` → salin ke `public/images/foto-toko.jpg`. **Ganti foto toko lama pada halaman Login dan Register dengan foto ini.** Tampilkan `object-cover` setinggi layar di panel kiri, overlay gradien biru (`from-[#1E3A8A]/70 to-[#3B5BDB]/40`), logo putih di kiri atas, headline "Every sack, bar, and delivery in one place." di kiri bawah.
> Foto di zip berukuran 973×547 (diambil dari dokumen). Untuk hasil tajam di layar besar, gunakan foto asli resolusi ≥ 1920 px.

## E. Acceptance
- [ ] 4 variasi logo (SVG) + favicon tersedia di `public/images/`
- [ ] Tampil benar di sidebar, Login, Register, tab browser
- [ ] Konsisten dengan referensi "PJ / PASKA JAYA / TOKO BAHAN BANGUNAN"
- [ ] Foto toko baru terpasang di Login & Register
