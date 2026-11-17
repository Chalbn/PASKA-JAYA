# ADR-001: Stack: Laravel + Livewire (bukan React/Inertia)

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Laporan MSBD §2.2.1 menetapkan Laravel dengan Livewire untuk POS dan pembaruan stok tanpa reload. Tim menguasai PHP/Laravel; lingkungan dev memakai Laragon + MySQL.

## Decision
Laravel 13 + Livewire 3 (full-page component) + Alpine.js + Tailwind CSS. Chart memakai Chart.js. Tidak ada API REST terpisah.

## Consequences
Satu codebase PHP, tanpa build SPA terpisah. State UI ada di komponen Livewire; interaksi kompleks (keranjang POS) memakai Alpine bila perlu. Tim wajib mengikuti pola komponen di CODING_STANDARDS.

## Alternatives Considered
- React + Inertia (dipakai proyek lain, ditolak: tidak sesuai laporan & butuh keahlian JS lebih besar); Blade murni + AJAX (ditolak: banyak boilerplate).
