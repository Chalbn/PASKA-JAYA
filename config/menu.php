<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PASKA JAYA Navigation Menu Configuration
    |--------------------------------------------------------------------------
    |
    | Menu navigasi aplikasi per-peran (Admin, Owner, Kasir, Karyawan)
    | Sesuai spesifikasi PROMPT_FRONTEND.md dan PDF UI/UX PASKA JAYA.
    |
    */

    'admin' => [
        'label' => 'MENU ADMIN',
        'items' => [
            [
                'title' => 'Ringkasan Sistem',
                'route' => 'admin.dashboard',
                'url' => '/admin',
                'icon' => 'dashboard',
            ],
            [
                'title' => 'Manajemen Pengguna',
                'route' => 'admin.pengguna',
                'url' => '/admin/pengguna',
                'icon' => 'users',
            ],
            [
                'title' => 'Tim',
                'route' => 'admin.tim',
                'url' => '/admin/tim',
                'icon' => 'team',
            ],
        ],
    ],

    'owner' => [
        'label' => 'MENU OWNER',
        'items' => [
            [
                'title' => 'Ringkasan',
                'route' => 'owner.dashboard',
                'url' => '/owner',
                'icon' => 'dashboard',
            ],
            [
                'title' => 'Laporan Barang Keluar',
                'route' => 'owner.laporan',
                'url' => '/owner/laporan-barang-keluar',
                'icon' => 'report',
            ],
            [
                'title' => 'Daftar Bon',
                'route' => 'owner.bon',
                'url' => '/owner/daftar-bon',
                'icon' => 'bon',
            ],
            [
                'title' => 'Master Item',
                'route' => 'owner.master-item',
                'url' => '/owner/master-item',
                'icon' => 'items',
            ],
            [
                'title' => 'Kehadiran',
                'route' => 'owner.kehadiran',
                'url' => '/owner/kehadiran',
                'icon' => 'attendance',
            ],
            [
                'title' => 'Profil',
                'route' => 'owner.profil',
                'url' => '/owner/profil',
                'icon' => 'profile',
            ],
        ],
    ],

    'kasir' => [
        'label' => 'MENU KASIR',
        'items' => [
            [
                'title' => 'Beranda',
                'route' => 'kasir.beranda',
                'url' => '/kasir',
                'icon' => 'home',
            ],
            [
                'title' => 'Barang Keluar',
                'route' => 'kasir.barang-keluar',
                'url' => '/kasir/barang-keluar',
                'icon' => 'pos',
            ],
            [
                'title' => 'Lihat Saldo',
                'route' => 'kasir.saldo',
                'url' => '/kasir/saldo',
                'icon' => 'wallet',
            ],
            [
                'title' => 'Grafik Mingguan',
                'route' => 'kasir.grafik',
                'url' => '/kasir/grafik-mingguan',
                'icon' => 'chart',
            ],
            [
                'title' => 'Riwayat Saldo Laci',
                'route' => 'kasir.riwayat-laci',
                'url' => '/kasir/riwayat-saldo',
                'icon' => 'history',
            ],
            [
                'title' => 'Catat Bon',
                'route' => 'kasir.catat-bon',
                'url' => '/kasir/catat-bon',
                'icon' => 'note-bon',
            ],
            [
                'title' => 'Daftar Bon',
                'route' => 'kasir.daftar-bon',
                'url' => '/kasir/daftar-bon',
                'icon' => 'bon',
            ],
            [
                'title' => 'Bahan Harian',
                'route' => 'kasir.bahan-harian',
                'url' => '/kasir/bahan-harian',
                'icon' => 'materials',
            ],
            [
                'title' => 'Kehadiran',
                'route' => 'kasir.kehadiran',
                'url' => '/kasir/kehadiran',
                'icon' => 'attendance',
            ],
            [
                'title' => 'Master Item',
                'route' => 'kasir.master-item',
                'url' => '/kasir/master-item',
                'icon' => 'items',
            ],
            [
                'title' => 'Profil',
                'route' => 'kasir.profil',
                'url' => '/kasir/profil',
                'icon' => 'profile',
            ],
        ],
    ],

    'karyawan' => [
        'label' => 'MENU KARYAWAN',
        'items' => [
            [
                'title' => 'Pengantaran',
                'route' => 'karyawan.pengantaran.index',
                'url' => '/karyawan/pengantaran',
                'icon' => 'delivery',
                'has_badge' => true,
                'badge_key' => 'tugas_baru_count',
            ],
            [
                'title' => 'Profil',
                'route' => 'karyawan.profil',
                'url' => '/karyawan/profil',
                'icon' => 'profile',
            ],
        ],
    ],
];
