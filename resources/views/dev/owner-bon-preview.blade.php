<x-layouts.app title="Daftar Bon" role="owner">
    <div class="max-w-7xl mx-auto space-y-6 pb-16" x-data="{ statusFilter: 'semua' }">
        {{-- =========================================================================
             1. HEADER HALAMAN & BADGE HANYA LIHAT (ROLE OWNER)
             ========================================================================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-surface-container text-primary text-[11px] font-bold mb-1">
                    <span>Menu Owner • Pengawasan Keuangan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">
                    Daftar Bon
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">
                    Pantau transaksi kredit bon pelanggan, tempo pembayaran, dan pelacakan pengantaran barang secara real-time.
                </p>
            </div>

            {{-- Badge Kanan Atas: Hanya Lihat (Ikon Mata) --}}
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Hanya Lihat</span>
                </span>
            </div>
        </div>

        {{-- =========================================================================
             2. DUA KARTU BANNER INFORMASI SEJAJAR
             ========================================================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
            {{-- Kartu 1: Mode baca saja oleh Kasir --}}
            <div class="bg-surface-card border border-blue-200/80 rounded-2xl p-4 sm:p-5 shadow-xs flex items-start gap-3.5">
                <div class="p-2.5 rounded-xl bg-surface-container text-primary shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-text-slate">Mode baca saja oleh Kasir</h4>
                    <p class="text-[11px] sm:text-xs text-text-muted mt-1 leading-relaxed">
                        Pencatatan nota bon baru dan penerimaan pelunasan piutang dikelola langsung oleh staf Kasir di kasir toko. Owner memiliki hak pantau mutlak tanpa risiko perubahan data tidak sengaja.
                    </p>
                </div>
            </div>

            {{-- Kartu 2: Update otomatis dari Karyawan --}}
            <div class="bg-surface-card border border-surface-border rounded-2xl p-4 sm:p-5 shadow-xs flex items-start gap-3.5">
                <div class="p-2.5 rounded-xl bg-emerald-50 text-status-success shrink-0 mt-0.5 border border-emerald-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-text-slate">Update otomatis dari Karyawan</h4>
                    <p class="text-[11px] sm:text-xs text-text-muted mt-1 leading-relaxed">
                        Status pengiriman dan kurir terintegrasi langsung dengan tugas karyawan. Saat barang diantar dan serah terima dilakukan, foto bukti serah terima otomatis terlampir pada rincian bon.
                    </p>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             3. KONTAINER TABEL PUTIH (ROUNDED-2XL) & FILTER KAPSUL
             ========================================================================= --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl shadow-xs overflow-hidden">
            {{-- Header Tabel & Filter Kapsul di Pojok Kanan Atas --}}
            <div class="p-4 sm:p-5 border-b border-surface-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-text-on-surface">
                        Daftar Piutang Bon Berjalan
                    </h3>
                    <p class="text-xs text-text-muted">
                        Menampilkan seluruh riwayat transaksi kredit material dengan sisa piutang aktif.
                    </p>
                </div>

                {{-- Tab Filter Kapsul (Semua, Belum lunas, Lunas) --}}
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80 self-start sm:self-center">
                    <button 
                        type="button" 
                        x-on:click="statusFilter = 'semua'"
                        :class="statusFilter === 'semua' ? 'bg-primary text-white font-bold shadow-xs' : 'text-text-muted hover:text-text-slate font-medium'"
                        class="px-3 py-1.5 text-xs rounded-lg transition duration-150 cursor-pointer"
                    >
                        Semua
                    </button>
                    <button 
                        type="button" 
                        x-on:click="statusFilter = 'belum-lunas'"
                        :class="statusFilter === 'belum-lunas' ? 'bg-primary text-white font-bold shadow-xs' : 'text-text-muted hover:text-text-slate font-medium'"
                        class="px-3 py-1.5 text-xs rounded-lg transition duration-150 cursor-pointer"
                    >
                        Belum lunas
                    </button>
                    <button 
                        type="button" 
                        x-on:click="statusFilter = 'lunas'"
                        :class="statusFilter === 'lunas' ? 'bg-primary text-white font-bold shadow-xs' : 'text-text-muted hover:text-text-slate font-medium'"
                        class="px-3 py-1.5 text-xs rounded-lg transition duration-150 cursor-pointer"
                    >
                        Lunas
                    </button>
                </div>
            </div>

            {{-- =========================================================================
                 4. TABEL DATA BON (READ-ONLY) DENGAN 6 KOLOM LENGKAP
                 ========================================================================= --}}
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-surface-border text-text-muted font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 w-16">NO</th>
                            <th class="px-5 py-3.5">PELANGGAN</th>
                            <th class="px-5 py-3.5">RINGKASAN BARANG</th>
                            <th class="px-5 py-3.5 text-right">TOTAL</th>
                            <th class="px-5 py-3.5 text-center">STATUS PEMBAYARAN</th>
                            <th class="px-5 py-3.5">STATUS PENGANTARAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-border">
                        {{-- Baris 1: Belum Lunas & Lewat Tempo --}}
                        <tr 
                            x-show="statusFilter === 'semua' || statusFilter === 'belum-lunas'"
                            class="hover:bg-slate-50/70 transition"
                        >
                            {{-- 1. NO --}}
                            <td class="px-5 py-4 font-mono font-bold text-text-muted">#1</td>

                            {{-- 2. PELANGGAN (nama, tempo, badge lewat tempo) --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-text-slate text-sm">Toko Berkah Abadi</span>
                                        <x-ui.badge variant="lewat-tempo" size="sm">Lewat tempo</x-ui.badge>
                                    </div>
                                    <div class="flex items-center gap-2 text-text-muted">
                                        <span class="font-mono text-[11px] text-primary font-semibold">BON-2026-001</span>
                                        <span>•</span>
                                        <span class="text-[11px] text-status-danger font-semibold tabular-nums">Tempo: 10 Nov 2026</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. RINGKASAN BARANG (pill badge biru muda) --}}
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        50 sak Semen Padang 50kg
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        120 btg Besi Beton 10mm
                                    </span>
                                </div>
                            </td>

                            {{-- 4. TOTAL (nominal utama + keterangan sisa bayar) --}}
                            <td class="px-5 py-4 text-right">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-extrabold text-text-on-surface tabular-nums whitespace-nowrap">
                                        {{ rupiah(5400000) }}
                                    </p>
                                    <p class="text-[11px] text-status-danger font-semibold tabular-nums">
                                        Sisa: {{ rupiah(3400000) }}
                                    </p>
                                </div>
                            </td>

                            {{-- 5. STATUS PEMBAYARAN (badge kuning Belum Lunas) --}}
                            <td class="px-5 py-4 text-center">
                                <x-ui.badge variant="belum-lunas">Belum Lunas</x-ui.badge>
                            </td>

                            {{-- 6. STATUS PENGANTARAN (badge + nama kurir) --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-primary">
                                        Sedang Diantar
                                    </span>
                                    <p class="text-[11px] text-text-muted flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-text-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Supir Joko (BK 8123 AB)</span>
                                    </p>
                                </div>
                            </td>
                        </tr>

                        {{-- Baris 2: Belum Lunas (Masih Berjalan) --}}
                        <tr 
                            x-show="statusFilter === 'semua' || statusFilter === 'belum-lunas'"
                            class="hover:bg-slate-50/70 transition"
                        >
                            {{-- 1. NO --}}
                            <td class="px-5 py-4 font-mono font-bold text-text-muted">#2</td>

                            {{-- 2. PELANGGAN --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-text-slate text-sm">CV Mandiri Perkasa</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-text-muted">
                                        <span class="font-mono text-[11px] text-primary font-semibold">BON-2026-002</span>
                                        <span>•</span>
                                        <span class="text-[11px] text-text-muted tabular-nums">Tempo: 25 Nov 2026</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. RINGKASAN BARANG --}}
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        3 kubik Pasir Cor
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        2 kubik Batu Split
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        15 kg Paku Campur
                                    </span>
                                </div>
                            </td>

                            {{-- 4. TOTAL --}}
                            <td class="px-5 py-4 text-right">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-extrabold text-text-on-surface tabular-nums whitespace-nowrap">
                                        {{ rupiah(2850000) }}
                                    </p>
                                    <p class="text-[11px] text-status-warning font-semibold tabular-nums">
                                        Sisa: {{ rupiah(1500000) }}
                                    </p>
                                </div>
                            </td>

                            {{-- 5. STATUS PEMBAYARAN --}}
                            <td class="px-5 py-4 text-center">
                                <x-ui.badge variant="belum-lunas">Belum Lunas</x-ui.badge>
                            </td>

                            {{-- 6. STATUS PENGANTARAN --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        Sedang Disiapkan
                                    </span>
                                    <p class="text-[11px] text-text-muted flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-text-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Gudang Utama - Truk 02</span>
                                    </p>
                                </div>
                            </td>
                        </tr>

                        {{-- Baris 3: Lunas & Selesai --}}
                        <tr 
                            x-show="statusFilter === 'semua' || statusFilter === 'lunas'"
                            class="hover:bg-slate-50/70 transition"
                        >
                            {{-- 1. NO --}}
                            <td class="px-5 py-4 font-mono font-bold text-text-muted">#3</td>

                            {{-- 2. PELANGGAN --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-text-slate text-sm">Pak Haji Mansyur (Proyek Masjid)</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-text-muted">
                                        <span class="font-mono text-[11px] text-primary font-semibold">BON-2026-003</span>
                                        <span>•</span>
                                        <span class="text-[11px] text-text-muted tabular-nums">Lunas: 15 Nov 2026</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. RINGKASAN BARANG --}}
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        40 sak Semen Gresik 50kg
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        8 dus Keramik 40x40 Putih
                                    </span>
                                </div>
                            </td>

                            {{-- 4. TOTAL --}}
                            <td class="px-5 py-4 text-right">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-extrabold text-text-on-surface tabular-nums whitespace-nowrap">
                                        {{ rupiah(1820000) }}
                                    </p>
                                    <p class="text-[11px] text-status-success font-semibold tabular-nums">
                                        Sisa: {{ rupiah(0) }} (Lunas)
                                    </p>
                                </div>
                            </td>

                            {{-- 5. STATUS PEMBAYARAN (badge biru gelap / emerald Lunas) --}}
                            <td class="px-5 py-4 text-center">
                                <x-ui.badge variant="lunas">Lunas</x-ui.badge>
                            </td>

                            {{-- 6. STATUS PENGANTARAN --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-status-success">
                                        ✓ Selesai
                                    </span>
                                    <p class="text-[11px] text-text-muted flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-status-success shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Diterima Mandor Agus (Bukti Foto ✓)</span>
                                    </p>
                                </div>
                            </td>
                        </tr>

                        {{-- Baris 4: Lunas & Selesai --}}
                        <tr 
                            x-show="statusFilter === 'semua' || statusFilter === 'lunas'"
                            class="hover:bg-slate-50/70 transition"
                        >
                            {{-- 1. NO --}}
                            <td class="px-5 py-4 font-mono font-bold text-text-muted">#4</td>

                            {{-- 2. PELANGGAN --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-text-slate text-sm">PT Graha Bangun Mandiri</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-text-muted">
                                        <span class="font-mono text-[11px] text-primary font-semibold">BON-2026-004</span>
                                        <span>•</span>
                                        <span class="text-[11px] text-text-muted tabular-nums">Lunas: 16 Nov 2026</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. RINGKASAN BARANG --}}
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        100 batang Besi 12mm
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-container text-primary border border-blue-200">
                                        50 batang Besi 8mm
                                    </span>
                                </div>
                            </td>

                            {{-- 4. TOTAL --}}
                            <td class="px-5 py-4 text-right">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-extrabold text-text-on-surface tabular-nums whitespace-nowrap">
                                        {{ rupiah(8750000) }}
                                    </p>
                                    <p class="text-[11px] text-status-success font-semibold tabular-nums">
                                        Sisa: {{ rupiah(0) }} (Lunas)
                                    </p>
                                </div>
                            </td>

                            {{-- 5. STATUS PEMBAYARAN --}}
                            <td class="px-5 py-4 text-center">
                                <x-ui.badge variant="lunas">Lunas</x-ui.badge>
                            </td>

                            {{-- 6. STATUS PENGANTARAN --}}
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-status-success">
                                        ✓ Selesai
                                    </span>
                                    <p class="text-[11px] text-text-muted flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-status-success shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Diterima Bpk. Rudi (Bukti Foto ✓)</span>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
