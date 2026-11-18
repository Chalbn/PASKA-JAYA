@php
    $currentRole = request('role', 'admin');
    $defaultTab = match($currentRole) {
        'kasir' => 'beranda',
        default => 'ringkasan',
    };
    $activeTab = $initialTab ?? request('tab', $defaultTab);
    $pageTitle = match($currentRole) {
        'kasir' => ($activeTab === 'barang-keluar' ? 'Barang Keluar' : 'Beranda'),
        'owner' => 'Ringkasan Toko',
        default => ($activeTab === 'pengguna' ? 'Manajemen Pengguna' : 'Ringkasan Sistem'),
    };
@endphp

<x-layouts.app :title="$pageTitle" :role="$currentRole">
    <div 
        x-data="{ 
            activeRole: '{{ $currentRole }}',
            activeFeature: '{{ $activeTab }}',
            setFeature(name) {
                this.activeFeature = name;
                if (history.pushState) {
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('tab', name);
                    window.history.pushState({}, '', newUrl);
                }
            }
        }" 
        class="max-w-7xl mx-auto space-y-10 pb-16"
    >
        @if($currentRole === 'kasir')
            {{-- =========================================================================
                 FITUR KE-1: BERANDA KASIR (Sesuai Gambar Referensi Fitur ke 1)
                 ========================================================================= --}}
            <div x-show="activeFeature === 'beranda'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
                {{-- Header Halaman --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-50 text-[#1D3FA3] text-[11px] font-bold mb-1">
                            <span>Fitur ke-1 (Menu Kasir)</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F172A] tracking-tight">
                            Beranda Kasir
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">
                            Ringkasan shift dan akses cepat.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            x-on:click="setFeature('barang-keluar')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#1D3FA3] hover:bg-[#152e7b] text-white shadow-xs transition duration-150 active:scale-95 cursor-pointer"
                            title="Klik untuk membuka Fitur ke-2: Barang Keluar"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Buka Barang Keluar (Fitur 2) →</span>
                        </button>
                    </div>
                </div>

                {{-- Grid Atas: Saldo Laci Hari Ini (Kiri) & Transaksi Terbaru (Kanan) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
                    {{-- KARTU UTAMA BIRU: SALDO LACI HARI INI --}}
                    <div class="lg:col-span-7 bg-gradient-to-r from-[#1D3FA3] via-[#1E3F99] to-[#2563EB] text-white rounded-2xl p-6 sm:p-7 shadow-sm flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute -right-12 -bottom-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

                        <div>
                            {{-- Header Kartu --}}
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 text-[#DBEAFE]/90">
                                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span class="text-[11px] font-extrabold uppercase tracking-widest">
                                        SALDO LACI HARI INI
                                    </span>
                                </div>

                                <button 
                                    type="button" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-white/30 text-white text-xs font-semibold hover:bg-white/10 active:scale-95 transition cursor-pointer select-none"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Reset Manual</span>
                                </button>
                            </div>

                            <div class="my-3">
                                <span class="text-3xl sm:text-4xl lg:text-[42px] font-black text-white tabular-nums tracking-tight whitespace-nowrap leading-none">
                                    Rp 2.180.000
                                </span>
                            </div>

                            <p class="text-xs text-[#DBEAFE]/80 font-medium">
                                Reset otomatis setiap hari 00:00
                            </p>
                        </div>

                        {{-- Dua Sub-Kartu: Saldo Cash & Saldo Transfer --}}
                        <div class="grid grid-cols-2 gap-3 mt-6 pt-5 border-t border-white/15">
                            <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3 sm:p-3.5 border border-white/10">
                                <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-[#DBEAFE]/90 mb-1">
                                    <svg class="w-3.5 h-3.5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>SALDO CASH</span>
                                </div>
                                <div class="text-base sm:text-xl font-black text-white tabular-nums whitespace-nowrap">
                                    Rp 690.000
                                </div>
                            </div>

                            <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3 sm:p-3.5 border border-white/10">
                                <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-[#DBEAFE]/90 mb-1">
                                    <svg class="w-3.5 h-3.5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span>SALDO TRANSFER</span>
                                </div>
                                <div class="text-base sm:text-xl font-black text-white tabular-nums whitespace-nowrap">
                                    Rp 1.490.000
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KARTU TRANSAKSI TERBARU (Kanan) --}}
                    <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-base font-bold text-[#0F172A] tracking-tight">
                                    Transaksi Terbaru
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Sumber penambahan saldo hari ini
                                </p>
                            </div>

                            <div class="divide-y divide-slate-100">
                                <div class="py-3 flex items-center justify-between gap-3 first:pt-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="p-2 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">Penjualan TRX-1012 – Bu Lestari</p>
                                            <p class="text-[11px] text-slate-400">10.35</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF2FF] text-[#1D3FA3]">
                                            TRANSFER
                                        </span>
                                        <span class="text-xs font-bold text-slate-800 tabular-nums">
                                            Rp 1.490.000
                                        </span>
                                    </div>
                                </div>

                                <div class="py-3 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="p-2 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">Penjualan TRX-1011 – Pak Joko</p>
                                            <p class="text-[11px] text-slate-400">09.10</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172A] text-white">
                                            CASH
                                        </span>
                                        <span class="text-xs font-bold text-slate-800 tabular-nums">
                                            Rp 690.000
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3 Kartu Stat Ringkasan Tengah --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                        <div class="p-3 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500">Transaksi Hari Ini</span>
                            <div class="text-2xl font-black text-slate-800 tabular-nums">2</div>
                            <span class="text-xs font-semibold text-slate-400">Rp 2.180.000</span>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                        <div class="p-3 rounded-xl bg-amber-50 text-amber-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500">Stok Menipis</span>
                            <div class="text-2xl font-black text-slate-800 tabular-nums">5 barang</div>
                            <span class="text-xs font-semibold text-slate-400">5 barang di bawah batas minimum</span>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                        <div class="p-3 rounded-xl bg-orange-50 text-orange-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500">Bon Belum Lunas</span>
                            <div class="text-2xl font-black text-slate-800 tabular-nums">4 bon</div>
                            <span class="text-xs font-semibold text-slate-400">Rp 7.806.000</span>
                        </div>
                    </div>
                </div>

                {{-- 4 Shortcut Buttons Bawah (Termasuk Barang Keluar -> Fitur 2) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button 
                        type="button" 
                        x-on:click="setFeature('barang-keluar')"
                        class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-[#1D3FA3]/40 hover:shadow-md transition flex items-center justify-between group cursor-pointer text-left"
                    >
                        <div class="flex items-center gap-3.5">
                            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] group-hover:bg-[#1D3FA3] group-hover:text-white transition shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-[#1D3FA3] transition">
                                    Barang Keluar
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Catat penjualan (Fitur 2)</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#1D3FA3] group-hover:translate-x-1 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800">Catat Bon</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Utang pelanggan</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>

                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800">Bahan Harian</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Pengeluaran bahan</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>

                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800">Kehadiran</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Absensi & pekerjaan</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
                 FITUR KE-2: BARANG KELUAR KASIR (Sesuai Gambar Referensi Fitur ke 2)
                 ========================================================================= --}}
            <div x-show="activeFeature === 'barang-keluar'" x-transition:enter="transition ease-out duration-200" class="space-y-6" style="display: none;">
                {{-- Header Fitur ke-2 --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            x-on:click="setFeature('beranda')"
                            class="p-2 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition shadow-xs cursor-pointer"
                            title="Kembali ke Beranda Kasir"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </button>
                        <div>
                            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-50 text-[#1D3FA3] text-[11px] font-bold mb-1">
                                <span>Fitur ke-2 (Menu Kasir)</span>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F172A] tracking-tight">
                                Barang Keluar
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500">
                                Catat penjualan: harga dan total terisi otomatis, stok berkurang setelah disimpan.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            x-on:click="setFeature('beranda')"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs cursor-pointer"
                        >
                            ← Kembali ke Beranda
                        </button>
                    </div>
                </div>

                {{-- Grid Form & Riwayat Sesuai Gambar Referensi --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    {{-- SISI KIRI (7 Kolom): 1. Pilih Barang, 2. Keranjang, 3. Pembayaran --}}
                    <div class="lg:col-span-7 space-y-5">
                        {{-- 1. PILIH BARANG --}}
                        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100 space-y-4">
                            <h3 class="text-sm font-bold text-slate-800">
                                1. Pilih barang
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                <div class="sm:col-span-7">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        Barang
                                    </label>
                                    <x-ui.select
                                        placeholder="— Pilih dari master item —"
                                        :searchable="true"
                                        :options="[
                                            '1' => 'Cat Tembok White 5kg - Rp 125.000 [Stok: 24]',
                                            '2' => 'Semen Gresik 50kg - Rp 68.000 [Stok: 120]',
                                            '3' => 'Pipa PVC 3&quot; AW (4m) - Rp 55.000 [Stok: 35]',
                                            '4' => 'Keramik Lantai 40x40 Putih - Rp 74.000 [Stok: 80]',
                                            '5' => 'Besi Beton 10mm (12m) - Rp 85.000 [Stok: 20]',
                                            '6' => 'Granit Lantai 60x60 Cream - Rp 138.000 [Stok: 22]',
                                        ]"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        Jumlah
                                    </label>
                                    <input 
                                        type="number" 
                                        value="1" 
                                        class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-slate-700 font-semibold tabular-nums focus:border-[#1D3FA3] focus:ring-2 focus:ring-[#1D3FA3]/20 transition shadow-xs"
                                    />
                                </div>

                                <div class="sm:col-span-3">
                                    <button 
                                        type="button" 
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#1D3FA3] hover:bg-[#152e7b] text-white text-xs sm:text-sm font-semibold transition active:scale-95 shadow-xs cursor-pointer select-none"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>+ Tambah</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 2. KERANJANG --}}
                        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100 space-y-4">
                            <h3 class="text-sm font-bold text-slate-800">
                                2. Keranjang
                            </h3>
                            <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center bg-slate-50/50">
                                <p class="text-xs sm:text-sm text-slate-400">
                                    Belum ada barang. Pilih barang lalu klik Tambah.
                                </p>
                            </div>
                        </div>

                        {{-- 3. PEMBAYARAN --}}
                        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100 space-y-4">
                            <h3 class="text-sm font-bold text-slate-800">
                                3. Pembayaran
                            </h3>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Nama pelanggan (opsional)
                                </label>
                                <input 
                                    type="text" 
                                    value="Umum" 
                                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 focus:border-[#1D3FA3] focus:ring-2 focus:ring-[#1D3FA3]/20 transition shadow-xs"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Metode pembayaran
                                </label>
                                <div class="grid grid-cols-2 gap-3" x-data="{ metode: 'cash' }">
                                    <button 
                                        type="button" 
                                        x-on:click="metode = 'cash'"
                                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border text-xs sm:text-sm font-bold transition select-none cursor-pointer"
                                        :class="metode === 'cash' ? 'bg-[#1D3FA3] text-white border-[#1D3FA3] shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>CASH</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        x-on:click="metode = 'transfer'"
                                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border text-xs sm:text-sm font-bold transition select-none cursor-pointer"
                                        :class="metode === 'transfer' ? 'bg-[#1D3FA3] text-white border-[#1D3FA3] shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span>TRANSFER</span>
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-xl bg-[#1D3FA3] text-white p-4 sm:p-5 flex items-center justify-between shadow-xs">
                                <span class="text-xs font-semibold text-[#DBEAFE]">Total bayar</span>
                                <span class="text-xl sm:text-2xl font-black text-white tabular-nums">Rp 0</span>
                            </div>

                            <button 
                                type="button" 
                                class="w-full py-3.5 px-4 rounded-xl bg-[#1D3FA3] hover:bg-[#152e7b] text-white text-sm font-bold transition shadow-xs cursor-pointer select-none text-center"
                            >
                                Simpan Transaksi
                            </button>
                        </div>
                    </div>

                    {{-- SISI KANAN (5 Kolom): Riwayat Barang Keluar --}}
                    <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-slate-800">Riwayat Barang Keluar</h3>
                            <p class="text-xs text-slate-400 mt-0.5">12 transaksi</p>
                        </div>

                        <div class="space-y-4 divide-y divide-slate-100 text-xs">
                            <div class="pt-3 first:pt-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-slate-800">TRX-1012</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF2FF] text-[#1D3FA3]">TRANSFER</span>
                                </div>
                                <p class="font-bold text-slate-700 mt-1">Bu Lestari</p>
                                <p class="text-[11px] text-slate-400">Cat Tembok White 5kg x6, Keramik Lantai 40x40 Putih x10</p>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                                    <span>13 November 2026, 10.35</span>
                                    <span class="font-black text-slate-800 text-xs tabular-nums">Rp 1.490.000</span>
                                </div>
                            </div>

                            <div class="pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-slate-800">TRX-1011</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172A] text-white">CASH</span>
                                </div>
                                <p class="font-bold text-slate-700 mt-1">Pak Joko</p>
                                <p class="text-[11px] text-slate-400">Batako Press x100, Semen Gresik 50kg x6</p>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                                    <span>13 November 2026, 09.10</span>
                                    <span class="font-black text-slate-800 text-xs tabular-nums">Rp 690.000</span>
                                </div>
                            </div>

                            <div class="pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-slate-800">TRX-1010</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172A] text-white">CASH</span>
                                </div>
                                <p class="font-bold text-slate-700 mt-1">Umum</p>
                                <p class="text-[11px] text-slate-400">Paku Beton 2" (1kg) x4, Cat Kayu & Besi 1kg x1</p>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                                    <span>12 November 2026, 16.30</span>
                                    <span class="font-black text-slate-800 text-xs tabular-nums">Rp 162.000</span>
                                </div>
                            </div>

                            <div class="pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-slate-800">TRX-1009</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF2FF] text-[#1D3FA3]">TRANSFER</span>
                                </div>
                                <p class="font-bold text-slate-700 mt-1">PT Bangun Persada</p>
                                <p class="text-[11px] text-slate-400">Semen Gresik 50kg x40, Semen Tiga Roda 40kg x20</p>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                                    <span>12 November 2026, 11.05</span>
                                    <span class="font-black text-slate-800 text-xs tabular-nums">Rp 3.700.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- =========================================================================
                 A. TAMPILAN RESMI SESUAI GAMBAR REFERENSI (ADMIN: RINGKASAN SISTEM)
                 ========================================================================= --}}
            <div x-show="activeFeature === 'ringkasan'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            {{-- Header Ringkasan Sistem --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">
                        Ringkasan Sistem
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-text-muted">
                        Gambaran data dan pengguna di seluruh sistem PASKA JAYA.
                    </p>
                </div>

                {{-- Tombol "Kelola Pengguna" (Klik langsung beralih ke Fitur ke-2: Manajemen Pengguna) --}}
                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        x-on:click="setFeature('pengguna')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-primary hover:bg-primary-container text-white shadow-xs transition duration-150 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        title="Klik untuk membuka Fitur ke-2: Manajemen Pengguna"
                    >
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kelola Pengguna</span>
                    </button>
                </div>
            </div>

            {{-- 4 Stat Cards Sesuai Gambar Referensi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                {{-- 1. Total Pengguna --}}
                <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs transition hover:shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-text-muted">Total pengguna</span>
                            <div class="p-2.5 rounded-xl bg-surface-container text-primary shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-3xl font-black text-text-on-surface tabular-nums">7</span>
                        </div>
                        <p class="mt-1 text-xs text-text-muted font-medium">6 aktif</p>
                    </div>
                </div>

                {{-- 2. Master Item --}}
                <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs transition hover:shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-text-muted">Master item</span>
                            <div class="p-2.5 rounded-xl bg-surface-container text-primary shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-3xl font-black text-text-on-surface tabular-nums">17</span>
                        </div>
                        <p class="mt-1 text-xs text-text-muted font-medium">5 stok menipis</p>
                    </div>
                </div>

                {{-- 3. Transaksi Keluar (Dengan background soft tinted card sesuai gambar) --}}
                <div class="bg-gradient-to-br from-[#E0F2FE]/80 to-[#DBEAFE]/40 border border-blue-200/80 rounded-2xl p-5 shadow-xs transition hover:shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-text-muted">Transaksi keluar</span>
                            <div class="p-2.5 rounded-xl bg-white text-primary shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-3xl font-black text-text-on-surface tabular-nums">12</span>
                        </div>
                        <p class="mt-1 text-xs text-text-muted font-medium">6 data bon</p>
                    </div>
                </div>

                {{-- 4. Pengantaran --}}
                <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs transition hover:shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-text-muted">Pengantaran</span>
                            <div class="p-2.5 rounded-xl bg-surface-container text-primary shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-3xl font-black text-text-on-surface tabular-nums">4</span>
                        </div>
                        <p class="mt-1 text-xs text-text-muted font-medium">3 berjalan</p>
                    </div>
                </div>
            </div>

            {{-- 2 Kartu Bagian Bawah: Pengguna per Role & Status Sistem Sesuai Gambar Referensi --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Kartu Kiri: Pengguna per Role --}}
                <div class="bg-surface-card border border-surface-border rounded-2xl p-6 shadow-xs">
                    <h3 class="text-base font-bold text-text-on-surface tracking-tight mb-5">
                        Pengguna per Role
                    </h3>

                    <div class="space-y-4">
                        {{-- Admin --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-text-slate">Admin</span>
                                <span class="text-text-muted tabular-nums">1 akun</span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: 25%;"></div>
                            </div>
                        </div>

                        {{-- Owner --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-text-slate">Owner</span>
                                <span class="text-text-muted tabular-nums">1 akun</span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: 25%;"></div>
                            </div>
                        </div>

                        {{-- Kasir --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-text-slate">Kasir</span>
                                <span class="text-text-muted tabular-nums">1 akun</span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: 25%;"></div>
                            </div>
                        </div>

                        {{-- Karyawan --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="text-text-slate">Karyawan</span>
                                <span class="text-text-muted tabular-nums">4 akun</span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kartu Kanan: Status Sistem --}}
                <div class="bg-surface-card border border-surface-border rounded-2xl p-6 shadow-xs">
                    <h3 class="text-base font-bold text-text-on-surface tracking-tight mb-5">
                        Status Sistem
                    </h3>

                    <div class="divide-y divide-slate-100 text-xs">
                        <div class="py-3 flex items-center justify-between">
                            <span class="text-text-body">Layanan aplikasi</span>
                            <span class="px-3 py-1 rounded-full font-bold bg-surface-container text-primary border border-blue-200">
                                Normal
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <span class="text-text-body">Basis data</span>
                            <span class="px-3 py-1 rounded-full font-bold bg-surface-container text-primary border border-blue-200">
                                Terhubung
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <span class="text-text-body">Pencadangan terakhir</span>
                            <span class="px-3 py-1 rounded-full font-semibold bg-surface-container text-primary border border-blue-200">
                                Hari ini, 02.00
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <span class="text-text-body">Catatan kehadiran</span>
                            <span class="px-3 py-1 rounded-full font-semibold bg-surface-container text-primary border border-blue-200 tabular-nums">
                                11 data
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <span class="text-text-body">Catatan bahan harian</span>
                            <span class="px-3 py-1 rounded-full font-semibold bg-surface-container text-primary border border-blue-200 tabular-nums">
                                7 data
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             B. FITUR KE-2: MANAJEMEN PENGGUNA (Langsung Ditampilkan saat Tombol Diklik)
             ========================================================================= --}}
        <div id="fitur-2" x-show="activeFeature === 'pengguna'" x-transition:enter="transition ease-out duration-200" class="space-y-6" style="display: none;">
            {{-- Header Fitur ke-2 --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        x-on:click="setFeature('ringkasan')"
                        class="p-2 rounded-xl border border-surface-border bg-white text-text-muted hover:text-text-on-surface hover:bg-slate-50 transition shadow-xs cursor-pointer"
                        title="Kembali ke Ringkasan Sistem"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-surface-container text-primary text-[11px] font-bold mb-1">
                            <span>Fitur ke-2 (Menu Admin)</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">
                            Manajemen Pengguna
                        </h2>
                        <p class="text-xs sm:text-sm text-text-muted">
                            Kelola data akun pengguna, peran sistem, dan status akses PASKA JAYA.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <x-ui.button variant="outline" size="sm" x-on:click="setFeature('ringkasan')">
                        Kembali ke Ringkasan
                    </x-ui.button>
                    <x-ui.button variant="primary" size="sm" x-on:click="$dispatch('toast', { message: 'Form tambah pengguna dibuka', type: 'info' })">
                        + Tambah Pengguna
                    </x-ui.button>
                </div>
            </div>

            {{-- Kartu Tabel Pengguna --}}
            <div class="bg-surface-card border border-surface-border rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-surface-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-text-slate">Filter Peran:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-primary text-white cursor-pointer shadow-xs">Semua (7)</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-text-muted hover:bg-slate-200 cursor-pointer">Admin (1)</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-text-muted hover:bg-slate-200 cursor-pointer">Owner (1)</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-text-muted hover:bg-slate-200 cursor-pointer">Kasir (1)</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-text-muted hover:bg-slate-200 cursor-pointer">Karyawan (4)</span>
                        </div>
                    </div>

                    <div class="w-full sm:w-64">
                        <input 
                            type="text" 
                            placeholder="Cari pengguna..." 
                            class="w-full px-3 py-1.5 text-xs rounded-xl border border-surface-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-surface-border text-text-muted font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Nama & Email</th>
                                <th class="px-4 py-3.5">Peran (Role)</th>
                                <th class="px-4 py-3.5">No. Handphone</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-4 py-3.5">Terakhir Masuk</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            {{-- Row 1: Rudi Hartono (Admin) --}}
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            RH
                                        </div>
                                        <div>
                                            <p class="font-bold text-text-slate">Rudi Hartono</p>
                                            <p class="text-[11px] text-text-muted">rudi@paskajaya.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary text-white">
                                        Admin
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-text-body">0812-3456-7890</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-status-success border border-emerald-200">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-text-muted">Hari ini, 08:15 WIB</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button class="text-primary hover:underline font-semibold">Edit</button>
                                </td>
                            </tr>

                            {{-- Row 2: Pak Hendra (Owner) --}}
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-secondary text-secondary-on font-bold flex items-center justify-center text-xs shrink-0">
                                            PH
                                        </div>
                                        <div>
                                            <p class="font-bold text-text-slate">Pak Hendra</p>
                                            <p class="text-[11px] text-text-muted">owner@paskajaya.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">
                                        Owner
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-text-body">0813-9876-5432</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-status-success border border-emerald-200">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-text-muted">Kemarin, 19:40 WIB</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button class="text-primary hover:underline font-semibold">Edit</button>
                                </td>
                            </tr>

                            {{-- Row 3: Rina Wijaya (Kasir) --}}
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            RW
                                        </div>
                                        <div>
                                            <p class="font-bold text-text-slate">Rina Wijaya</p>
                                            <p class="text-[11px] text-text-muted">rina@paskajaya.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-surface-container text-primary border border-blue-200">
                                        Kasir
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-text-body">0821-4567-8901</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-status-success border border-emerald-200">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-text-muted">Hari ini, 07:55 WIB</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button class="text-primary hover:underline font-semibold">Edit</button>
                                </td>
                            </tr>

                            {{-- Row 4: Joko Susilo (Karyawan) --}}
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-700 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            JS
                                        </div>
                                        <div>
                                            <p class="font-bold text-text-slate">Joko Susilo</p>
                                            <p class="text-[11px] text-text-muted">joko@paskajaya.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-text-slate">
                                        Karyawan (Supir)
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-text-body">0852-1234-5678</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-status-success border border-emerald-200">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-text-muted">Hari ini, 08:30 WIB</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button class="text-primary hover:underline font-semibold">Edit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- =========================================================================
             C. BANNER KATALOG & SIMULASI ROLE (DESIGN SYSTEM OVERVIEW)
             ========================================================================= --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-primary text-xs font-bold mb-3">
                        <span>Lingkungan Pengembangan (APP_ENV=local)</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-text-slate tracking-tight">
                        PASKA JAYA Design System & Component Gallery
                    </h2>
                    <p class="mt-1 text-sm text-text-body">
                        Katalog seluruh komponen Blade <code class="text-primary font-bold">x-ui.*</code> dan <code class="text-primary font-bold">x-brand.*</code> berbasis token Tailwind CSS resmi.
                    </p>
                </div>

                {{-- Simulasi Peran --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 bg-surface-canvas p-3 rounded-xl border border-surface-border">
                    <span class="text-xs font-bold text-text-slate">Simulasi Role:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['admin' => 'Admin', 'owner' => 'Owner', 'kasir' => 'Kasir', 'karyawan' => 'Karyawan'] as $rKey => $rLabel)
                            <a 
                                href="?role={{ $rKey }}"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('role', 'admin') === $rKey ? 'bg-primary text-white shadow-xs' : 'bg-surface-card text-text-body hover:text-text-slate border border-surface-border' }}"
                            >
                                {{ $rLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- 1. PALET WARNA & TOKEN RESMI --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <span>1. Palet Warna Resmi (Modern Royal Blue Commerce)</span>
                </h3>
                <span class="text-xs text-text-muted">Token di resources/css/app.css</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#1D4ED8] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Royal Blue</p>
                    <p class="text-[11px] text-text-muted font-mono">#1D4ED8 (Primary)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#1E3A8A] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Deep Cobalt</p>
                    <p class="text-[11px] text-text-muted font-mono">#1E3A8A (Sidebar Top)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#E0F2FE] border border-blue-200 mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Sky Tint</p>
                    <p class="text-[11px] text-text-muted font-mono">#E0F2FE (Surface Box)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#E59D2C] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Marigold</p>
                    <p class="text-[11px] text-text-muted font-mono">#E59D2C (Secondary)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#8A3B08] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Citrine Brown</p>
                    <p class="text-[11px] text-text-muted font-mono">#8A3B08 (Tertiary)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#0F766E] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Emerald Status</p>
                    <p class="text-[11px] text-text-muted font-mono">#0F766E (Lunas/Aman)</p>
                </div>
            </div>
        </section>

        {{-- 2. BRAND & LOGO --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>2. Brand & Logo (<code class="text-xs font-mono">x-brand.*</code>)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.card title="Logo Standar (Default)" subtitle="Digunakan pada latar terang">
                    <div class="py-4 flex items-center justify-center bg-surface-canvas rounded-xl p-4">
                        <x-brand.logo variant="default" class="h-12 w-auto" />
                    </div>
                </x-ui.card>

                <x-ui.card title="Logo Versi Putih (White)" subtitle="Digunakan pada topbar/sidebar biru & halaman auth">
                    <div class="py-4 flex items-center justify-center bg-gradient-to-r from-primary-deep to-primary rounded-xl p-4">
                        <x-brand.logo variant="white" class="h-12 w-auto" />
                    </div>
                </x-ui.card>

                <x-ui.card title="Logo Mark / Favicon (PJ)" subtitle="Icon mark tunggal untuk avatar / mode ciut">
                    <div class="py-4 flex items-center justify-center bg-surface-canvas rounded-xl p-4 gap-6">
                        <x-brand.mark class="h-10 w-10" />
                        <x-brand.mark class="h-14 w-14" />
                    </div>
                </x-ui.card>
            </div>
        </section>

        {{-- 3. TOMBOL (BUTTONS) --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>3. Tombol (<code class="text-xs font-mono">x-ui.button</code>)</span>
            </h3>

            <x-ui.card>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-3">Varian Warna Resmi</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="primary">
                                Simpan Transaksi (Primary Royal Blue)
                            </x-ui.button>
                            <x-ui.button variant="secondary">
                                Aksi Sekunder (Secondary Marigold)
                            </x-ui.button>
                            <x-ui.button variant="outline">
                                Batal (Outline White)
                            </x-ui.button>
                            <x-ui.button variant="danger">
                                Ya, hapus (Danger Red)
                            </x-ui.button>
                            <x-ui.button variant="ghost">
                                Kembali (Ghost)
                            </x-ui.button>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-3">Ukuran (Size: sm, md, lg)</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button size="sm" variant="primary">Kecil (sm)</x-ui.button>
                            <x-ui.button size="md" variant="primary">Standar (md)</x-ui.button>
                            <x-ui.button size="lg" variant="primary">Tombol Besar POS (lg)</x-ui.button>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-3">Keadaan Loading & Dinonaktifkan (Disabled)</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="primary" :loading="true">
                                Menyimpan...
                            </x-ui.button>
                            <x-ui.button variant="primary" :disabled="true">
                                Tidak Aktif
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </section>

        {{-- 4. BADGE & CHIP SATUAN BARANG --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>4. Badge & Chip Satuan Barang (<code class="text-xs font-mono">x-ui.badge</code>)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.card title="Status Transaksi & Pembayaran" subtitle="Sesuai warna resmi desain">
                    <div class="flex flex-wrap gap-2.5">
                        <x-ui.badge variant="lunas">Lunas</x-ui.badge>
                        <x-ui.badge variant="belum-lunas">Belum Lunas</x-ui.badge>
                        <x-ui.badge variant="selesai">Selesai</x-ui.badge>
                        <x-ui.badge variant="normal">Normal</x-ui.badge>
                        <x-ui.badge variant="terhubung">Terhubung</x-ui.badge>
                        <x-ui.badge variant="kritis">Kritis / Habis</x-ui.badge>
                        <x-ui.badge variant="menipis">Menipis</x-ui.badge>
                        <x-ui.badge variant="lewat-tempo">Lewat Tempo</x-ui.badge>
                        <x-ui.badge variant="cash">CASH</x-ui.badge>
                        <x-ui.badge variant="transfer">TRANSFER</x-ui.badge>
                    </div>
                </x-ui.card>

                <x-ui.card title="Chip Satuan Barang (Material Chips)" subtitle="Warna Pearl (#EBDDC5) teks Royal Blue">
                    <div class="flex flex-wrap gap-2.5">
                        <x-ui.badge variant="sak">50 sak</x-ui.badge>
                        <x-ui.badge variant="batang">120 batang</x-ui.badge>
                        <x-ui.badge variant="kubik">3.5 kubik</x-ui.badge>
                        <x-ui.badge variant="pcs">100 pcs</x-ui.badge>
                        <x-ui.badge variant="kaleng">12 kaleng</x-ui.badge>
                        <x-ui.badge variant="kg">250 kg</x-ui.badge>
                        <x-ui.badge variant="dus">8 dus</x-ui.badge>
                        <x-ui.badge variant="meter">15 meter</x-ui.badge>
                    </div>
                </x-ui.card>
            </div>
        </section>

        {{-- 5. KPI STAT CARDS --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                    <span>5. KPI Stat Cards (<code class="text-xs font-mono">x-ui.stat-card</code>)</span>
                </h3>
                <span class="text-xs text-text-muted">Format resmi, angka 1 baris utuh tabular-nums</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.stat-card 
                    title="Penjualan Hari Ini"
                    :value="rupiah(1820000)"
                    badge="+14 transaksi"
                    subtitle="Omzet penjualan langsung & kasir"
                    accent="primary"
                    href="#penjualan"
                >
                    <x-slot:iconSlot>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:iconSlot>
                </x-ui.stat-card>

                <x-ui.stat-card 
                    title="Saldo Cash (Laci)"
                    :value="rupiah(4550000)"
                    badge="Kas Aktif"
                    subtitle="Reset otomatis pukul 00:00"
                    accent="primary"
                    href="#saldo"
                >
                    <x-slot:iconSlot>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </x-slot:iconSlot>
                </x-ui.stat-card>

                <x-ui.stat-card 
                    title="Bon Belum Lunas"
                    :value="rupiah(8750000)"
                    badge="4 Pelanggan"
                    subtitle="Total sisa piutang berjalan"
                    accent="tertiary"
                    href="#bon"
                >
                    <x-slot:iconSlot>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-slot:iconSlot>
                </x-ui.stat-card>

                <x-ui.stat-card 
                    title="Stok Menipis"
                    value="6 Item"
                    badge="Perlu Restock"
                    subtitle="Segera lakukan pemesanan gudang"
                    accent="danger"
                    href="#stok"
                >
                    <x-slot:iconSlot>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </x-slot:iconSlot>
                </x-ui.stat-card>
            </div>
        </section>

        {{-- 6. FORM CONTROLS & INPUTS --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>6. Input, Select/Combobox, Date & Upload</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card title="Input & Pemilihan Tanggal">
                    <div class="space-y-4">
                        <x-ui.input 
                            name="nama_barang"
                            label="Nama Barang"
                            placeholder="Contoh: Semen Padang 50kg"
                            :required="true"
                        >
                            <x-slot:leftIcon>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.input 
                            name="harga_jual"
                            label="Harga Satuan"
                            placeholder="68000"
                            suffix="IDR"
                            class="tabular-nums font-semibold"
                        >
                            <x-slot:leftIcon>
                                <span class="text-xs font-bold text-text-muted">Rp</span>
                            </x-slot:leftIcon>
                        </x-ui.input>

                        <x-ui.date 
                            name="tanggal_transaksi"
                            label="Tanggal Transaksi"
                            value="{{ date('Y-m-d') }}"
                        />
                    </div>
                </x-ui.card>

                <x-ui.card title="Combobox Satuan & Upload Bukti Pengantaran">
                    <div class="space-y-4">
                        <x-ui.select 
                            name="satuan"
                            label="Satuan Barang"
                            :options="[
                                'sak' => 'Sak (Semen)',
                                'batang' => 'Batang (Besi / Pipa)',
                                'kubik' => 'Kubik (Pasir / Batu)',
                                'pcs' => 'Pcs (Bata / Keramik)',
                                'kaleng' => 'Kaleng (Cat)',
                                'kg' => 'Kilogram (Paku)'
                            ]"
                            value="sak"
                            helperText="Pilih dari daftar standar satuan bahan bangunan."
                        />

                        <x-ui.upload 
                            name="foto_pengantaran"
                            label="Foto Bukti Serah Terima Pengantaran"
                            accept="image/*"
                            helperText="Unggah foto atau ambil langsung lewat kamera HP di lokasi proyek."
                        />
                    </div>
                </x-ui.card>
            </div>
        </section>

        {{-- 7. TABEL DATA (DATA TABLES) --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>7. Tabel Data (<code class="text-xs font-mono">x-ui.table</code>)</span>
            </h3>

            <x-ui.card>
                <x-ui.table>
                    <x-slot:header>
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-text-slate">No. Bon</th>
                            <th class="px-4 py-3 text-left font-bold text-text-slate">Pelanggan</th>
                            <th class="px-4 py-3 text-left font-bold text-text-slate">Tanggal Tempo</th>
                            <th class="px-4 py-3 text-right font-bold text-text-slate">Total Tagihan</th>
                            <th class="px-4 py-3 text-right font-bold text-text-slate">Sisa Piutang</th>
                            <th class="px-4 py-3 text-center font-bold text-text-slate">Status</th>
                        </tr>
                    </x-slot:header>

                    <tr class="hover:bg-surface-canvas transition">
                        <td class="px-4 py-3 font-mono font-bold text-primary">BON-2026-001</td>
                        <td class="px-4 py-3 font-medium text-text-slate">Toko Berkah Abadi</td>
                        <td class="px-4 py-3 text-text-muted tabular-nums">20 Nov 2026</td>
                        <td class="px-4 py-3 text-right tabular-nums font-bold text-text-slate">{{ rupiah(3500000) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums font-bold text-tertiary">{{ rupiah(1500000) }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-ui.badge variant="belum-lunas">Belum Lunas</x-ui.badge>
                        </td>
                    </tr>

                    <tr class="hover:bg-surface-canvas transition">
                        <td class="px-4 py-3 font-mono font-bold text-primary">BON-2026-002</td>
                        <td class="px-4 py-3 font-medium text-text-slate">Pak Haji Mansyur (Proyek Masjid)</td>
                        <td class="px-4 py-3 text-text-muted tabular-nums">15 Nov 2026</td>
                        <td class="px-4 py-3 text-right tabular-nums font-bold text-text-slate">{{ rupiah(1820000) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums font-bold text-status-success">{{ rupiah(0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-ui.badge variant="lunas">Lunas</x-ui.badge>
                        </td>
                    </tr>

                    <tr class="hover:bg-surface-canvas transition">
                        <td class="px-4 py-3 font-mono font-bold text-primary">BON-2026-003</td>
                        <td class="px-4 py-3 font-medium text-text-slate">CV Mandiri Karya</td>
                        <td class="px-4 py-3 text-text-muted tabular-nums">10 Nov 2026</td>
                        <td class="px-4 py-3 text-right tabular-nums font-bold text-text-slate">{{ rupiah(5400000) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums font-bold text-status-danger">{{ rupiah(5400000) }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-ui.badge variant="lewat-tempo">Lewat Tempo</x-ui.badge>
                        </td>
                    </tr>
                </x-ui.table>
            </x-ui.card>
        </section>

        {{-- 8. FILTER PILL & TOGGLE TAMPILAN --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>8. Filter Pill & Toggle Tampilan</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.card title="Pill Filter Periode Transaksi">
                    <x-ui.pill-filter 
                        name="filter_waktu"
                        :options="[
                            'hari-ini' => 'Hari Ini',
                            'minggu-ini' => 'Minggu Ini',
                            'bulan-ini' => 'Bulan Ini',
                            'semua' => 'Semua Data'
                        ]"
                        value="hari-ini"
                    />
                </x-ui.card>

                <x-ui.card title="Toggle Tampilan (Kartu | Tabel)">
                    <x-ui.toggle 
                        name="view_mode"
                        leftLabel="Kartu"
                        rightLabel="Tabel"
                        leftValue="grid"
                        rightValue="table"
                        value="grid"
                    />
                </x-ui.card>
            </div>
        </section>

        {{-- 9. ALUR STATUS TIMELINE PENGANTARAN --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>9. Alur Status Timeline Pengantaran</span>
            </h3>

            <x-ui.card title="Pelacakan Pengantaran (Truk BK 8123 AB)" subtitle="Status bergerak dari Kasir -> Gudang -> Supir -> Selesai">
                <x-ui.timeline :steps="[
                    ['title' => 'Bon Dicatat (Belum Dikirim)', 'date' => '17 Nov 2026, 08:30 WIB', 'status' => 'completed', 'note' => 'Oleh Kasir Rina. Tagihan Rp 1.820.000'],
                    ['title' => 'Dikirim ke Karyawan', 'date' => '17 Nov 2026, 09:15 WIB', 'status' => 'completed', 'note' => 'Ditugaskan kepada Supir Joko (BK 8123 AB)'],
                    ['title' => 'Sedang Disiapkan di Gudang', 'date' => '17 Nov 2026, 09:40 WIB', 'status' => 'completed', 'note' => 'Pemuatan 20 sak semen ke armada truk'],
                    ['title' => 'Siap Berangkat / Sedang Diantar', 'date' => '17 Nov 2026, 10:15 WIB', 'status' => 'current', 'note' => 'Armada menuju lokasi proyek Jl. Karya No. 12'],
                    ['title' => 'Selesai (Bukti Foto Wajib)', 'date' => 'Menunggu konfirmasi', 'status' => 'pending', 'note' => 'Karyawan mengunggah foto bukti serah terima barang']
                ]" />
            </x-ui.card>
        </section>

        {{-- 10. GRAFIK CHART.JS (BAR & STACKED BAR) --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <span>10. Grafik Chart.js Alpine (Royal Blue & Marigold, Tooltip Rupiah)</span>
                </h3>
                <span class="text-xs text-text-muted">Data kosong tetap menggambar sumbu bernilai 0</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Bar Chart: Penjualan 7 Hari --}}
                <x-ui.card title="Penjualan 7 Hari Terakhir (Bar Chart)" subtitle="Warna Royal Blue (#1D4ED8) dengan tooltip format Rupiah">
                    <div 
                        x-data="paskaChartBar({
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                            values: [1250000, 2400000, 1850000, 3100000, 2900000, 4800000, 3600000],
                            title: 'Penjualan'
                        })" 
                        class="h-72 w-full pt-2"
                    >
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </x-ui.card>

                {{-- Stacked Bar Chart: Kas Laci (Cash + Transfer) --}}
                <x-ui.card title="Grafik Saldo Laci Mingguan (Stacked Bar)" subtitle="Cash (#1D4ED8) + Transfer (#E59D2C)">
                    <div 
                        x-data="paskaChartStackedBar({
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                            cashData: [800000, 1400000, 1100000, 1900000, 1600000, 2800000, 2100000],
                            transferData: [450000, 1000000, 750000, 1200000, 1300000, 2000000, 1500000]
                        })" 
                        class="h-72 w-full pt-2"
                    >
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </x-ui.card>
            </div>
        </section>

        {{-- 11. MODAL, KONFIRMASI & TOAST NOTIFICATION --}}
        <section class="space-y-4" x-data="{ modalDemo: false, confirmDemo: false }">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>11. Modal Dialog & Notifikasi Toast</span>
            </h3>

            <x-ui.card>
                <div class="space-y-4">
                    <p class="text-xs text-text-muted">Klik tombol di bawah untuk menguji interaktivitas modal dan trigger toast event:</p>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-ui.button variant="primary" x-on:click="$dispatch('open-modal', 'modal-contoh')">
                            Buka Modal Contoh
                        </x-ui.button>

                        <x-ui.button variant="danger" x-on:click="$dispatch('open-modal', 'confirm-hapus')">
                            Buka Modal Konfirmasi Hapus
                        </x-ui.button>

                        <x-ui.button variant="primary" x-on:click="$dispatch('toast', { message: 'Transaksi berhasil disimpan ke kas laci!', type: 'success' })">
                            Trigger Toast Sukses
                        </x-ui.button>

                        <x-ui.button variant="outline" x-on:click="$dispatch('toast', { message: 'Stok semen kurang dari batas minimum!', type: 'warning' })">
                            Trigger Toast Peringatan
                        </x-ui.button>

                        <x-ui.button variant="danger" x-on:click="$dispatch('toast', { message: 'Gagal memproses data: Stok tidak mencukupi.', type: 'error' })">
                            Trigger Toast Error
                        </x-ui.button>
                    </div>
                </div>

                {{-- Modal Contoh --}}
                <x-ui.modal name="modal-contoh" title="Tambah Barang Baru" maxWidth="md">
                    <div class="space-y-4">
                        <x-ui.input label="Kode Barang" placeholder="Contoh: SMN-001" />
                        <x-ui.input label="Nama Barang" placeholder="Contoh: Semen Gresik 50kg" />
                        <x-ui.select label="Satuan" :options="['sak' => 'Sak', 'batang' => 'Batang', 'pcs' => 'Pcs']" />
                    </div>

                    <x-slot:footer>
                        <x-ui.button variant="outline" size="sm" x-on:click="open = false">
                            Batal
                        </x-ui.button>
                        <x-ui.button variant="primary" size="sm" x-on:click="open = false; $dispatch('toast', { message: 'Barang berhasil disimpan', type: 'success' })">
                            Simpan Barang
                        </x-ui.button>
                    </x-slot:footer>
                </x-ui.modal>

                {{-- Modal Konfirmasi Hapus --}}
                <x-ui.confirm 
                    name="confirm-hapus"
                    title="Hapus Data Transaksi"
                    message="Apakah Anda yakin ingin menghapus data bon ini? Mutasi stok dan catatan kas akan disesuaikan otomatis."
                    confirmText="Ya, hapus"
                />
            </x-ui.card>
        </section>

        {{-- 12. TAUTAN KE PREVIEW AUTH & ROLE OWNER --}}
        <section class="p-6 bg-gradient-to-r from-primary-deep via-primary to-primary-container rounded-2xl text-white shadow-md">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h4 class="text-base font-bold">Pratinjau Halaman Terkait (Auth & Role Owner)</h4>
                    <p class="text-xs text-[#DBEAFE] mt-1">
                        Halaman Login, Register Create Account, serta Halaman Daftar Bon khusus Role Owner (Read-Only).
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button variant="secondary" size="sm" href="/dev/auth">
                        Pratinjau Login
                    </x-ui.button>
                    <x-ui.button variant="secondary" size="sm" href="/dev/register">
                        Pratinjau Register
                    </x-ui.button>
                    <x-ui.button variant="outline" size="sm" href="/owner/daftar-bon" class="bg-white/10 text-white border-white/20 hover:bg-white/20">
                        Pratinjau Owner (Daftar Bon)
                    </x-ui.button>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
