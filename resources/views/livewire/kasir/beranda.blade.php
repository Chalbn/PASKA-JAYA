<div class="space-y-6">
    {{-- Header Halaman Sesuai Gambar Referensi --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">
            Beranda Kasir
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-text-muted">
            Ringkasan shift dan akses cepat.
        </p>
    </div>

    {{-- Grid Atas: Saldo Laci Hari Ini (Kiri) & Transaksi Terbaru (Kanan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
        {{-- KARTU UTAMA BIRU: SALDO LACI HARI INI --}}
        <div class="lg:col-span-7 bg-gradient-to-r from-primary-deep via-primary to-primary-container text-white rounded-2xl p-6 sm:p-7 shadow-sm flex flex-col justify-between relative overflow-hidden">
            {{-- Aksen Latar Lembut --}}
            <div class="absolute -right-12 -bottom-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

            <div>
                {{-- Header Kartu: Label & Tombol Reset Manual --}}
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-[#DBEAFE]/90">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="text-[11px] font-extrabold uppercase tracking-widest">
                            SALDO LACI HARI INI
                        </span>
                    </div>

                    {{-- Tombol Reset Manual --}}
                    <button 
                        type="button" 
                        wire:click="bukaModalReset"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-white/30 text-white text-xs font-semibold hover:bg-white/10 active:scale-95 transition cursor-pointer select-none"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset Manual</span>
                    </button>
                </div>

                {{-- Angka Total Saldo Laci --}}
                <div class="my-3">
                    <span class="text-3xl sm:text-4xl lg:text-[42px] font-black text-white tabular-nums tracking-tight whitespace-nowrap leading-none">
                        {{ rupiah($saldoTotal) }}
                    </span>
                </div>

                <p class="text-xs text-[#DBEAFE]/80 font-medium">
                    Reset otomatis setiap hari 00:00
                </p>
            </div>

            {{-- Dua Sub-Kartu Sejajar: Saldo Cash & Saldo Transfer --}}
            <div class="grid grid-cols-2 gap-3 mt-6 pt-5 border-t border-white/15">
                {{-- Saldo Cash --}}
                <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3 sm:p-3.5 border border-white/10">
                    <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-[#DBEAFE]/90 mb-1">
                        <svg class="w-3.5 h-3.5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>SALDO CASH</span>
                    </div>
                    <div class="text-base sm:text-xl font-black text-white tabular-nums whitespace-nowrap">
                        {{ rupiah($saldoCash) }}
                    </div>
                </div>

                {{-- Saldo Transfer --}}
                <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3 sm:p-3.5 border border-white/10">
                    <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-[#DBEAFE]/90 mb-1">
                        <svg class="w-3.5 h-3.5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>SALDO TRANSFER</span>
                    </div>
                    <div class="text-base sm:text-xl font-black text-white tabular-nums whitespace-nowrap">
                        {{ rupiah($saldoTransfer) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU TRANSAKSI TERBARU (Kanan) --}}
        <div class="lg:col-span-5 bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <h2 class="text-base font-bold text-text-on-surface tracking-tight">
                        Transaksi Terbaru
                    </h2>
                    <p class="text-xs text-text-muted mt-0.5">
                        Sumber penambahan saldo hari ini
                    </p>
                </div>

                @if($transaksiTerbaru->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach($transaksiTerbaru as $trx)
                            <div class="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                                <div class="flex items-center gap-3 truncate">
                                    <div class="p-2 rounded-xl bg-surface-container text-primary shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-text-slate truncate">
                                            {{ $trx->keterangan }}
                                        </p>
                                        <p class="text-[11px] text-text-muted font-medium tabular-nums mt-0.5">
                                            {{ $trx->waktu ? $trx->waktu->format('H.i') : '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 flex items-center gap-2">
                                    @php
                                        $metodeVal = $trx->metode instanceof \BackedEnum ? $trx->metode->value : (string) $trx->metode;
                                    @endphp
                                    <x-ui.badge :variant="$metodeVal === 'transfer' ? 'transfer' : 'cash'" size="sm">
                                        {{ strtoupper($metodeVal) }}
                                    </x-ui.badge>
                                    <span class="text-xs font-extrabold text-text-on-surface tabular-nums whitespace-nowrap">
                                        {{ rupiah($trx->jumlah) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-6 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-surface-container text-primary flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-text-slate">Belum ada transaksi hari ini</p>
                        <p class="text-[11px] text-text-muted mt-1 max-w-xs mx-auto">
                            Transaksi penjualan langsung atau pembayaran cicilan bon akan langsung tercatat di sini.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Grid Tengah: 3 Kartu Ringkasan Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        {{-- 1. Transaksi Hari Ini --}}
        <x-ui.stat-card 
            title="Transaksi Hari Ini"
            :value="(string) $transaksiHariIniCount"
            :subtitle="rupiah($transaksiHariIniTotal)"
            accent="primary"
            :href="route('kasir.barang-keluar')"
            actionText="Lihat barang keluar"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        {{-- 2. Stok Menipis --}}
        <x-ui.stat-card 
            title="Stok Menipis"
            :value="$stokMenipisCount . ' barang'"
            :subtitle="$stokMenipisCount . ' barang di bawah batas minimum'"
            accent="secondary"
            :href="route('kasir.master-item')"
            actionText="Cek master item"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        {{-- 3. Bon Belum Lunas --}}
        <x-ui.stat-card 
            title="Bon Belum Lunas"
            :value="$bonBelumLunasCount . ' bon'"
            :subtitle="rupiah($bonBelumLunasSisa)"
            accent="tertiary"
            :href="route('kasir.daftar-bon')"
            actionText="Buka daftar bon"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>
    </div>

    {{-- Grid Bawah: 4 Tombol / Kartu Quick Link --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Quick Link 1: Barang Keluar --}}
        <a 
            href="{{ route('kasir.barang-keluar') }}"
            class="bg-surface-card border border-surface-border rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-primary/40 transition flex items-center justify-between group"
        >
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-xl bg-surface-container text-primary group-hover:bg-primary group-hover:text-white transition shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-text-on-surface group-hover:text-primary transition">
                        Barang Keluar
                    </h3>
                    <p class="text-[11px] text-text-muted mt-0.5">Catat penjualan</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-text-muted group-hover:text-primary group-hover:translate-x-1 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- Quick Link 2: Catat Bon --}}
        <a 
            href="{{ route('kasir.catat-bon') }}"
            class="bg-surface-card border border-surface-border rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-primary/40 transition flex items-center justify-between group"
        >
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-xl bg-surface-container text-primary group-hover:bg-primary group-hover:text-white transition shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-text-on-surface group-hover:text-primary transition">
                        Catat Bon
                    </h3>
                    <p class="text-[11px] text-text-muted mt-0.5">Utang pelanggan</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-text-muted group-hover:text-primary group-hover:translate-x-1 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- Quick Link 3: Bahan Harian --}}
        <a 
            href="{{ route('kasir.bahan-harian') }}"
            class="bg-white hover:border-blue-300 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between transition group shadow-2xs"
        >
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-xl bg-surface-container text-primary group-hover:bg-primary group-hover:text-white transition shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-text-on-surface group-hover:text-primary transition">
                        Bahan Harian
                    </h3>
                    <p class="text-[11px] text-text-muted mt-0.5">Pengeluaran bahan</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-text-muted group-hover:text-primary group-hover:translate-x-1 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- Quick Link 4: Kehadiran --}}
        <a 
            href="{{ route('kasir.kehadiran') }}"
            class="bg-surface-card border border-surface-border rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-primary/40 transition flex items-center justify-between group"
        >
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-xl bg-surface-container text-primary group-hover:bg-primary group-hover:text-white transition shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-text-on-surface group-hover:text-primary transition">
                        Kehadiran
                    </h3>
                    <p class="text-[11px] text-text-muted mt-0.5">Absensi & pekerjaan</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-text-muted group-hover:text-primary group-hover:translate-x-1 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    {{-- Modal Konfirmasi Reset Laci Manual --}}
    @if($modalResetOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-amber-100 text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-text-on-surface">Konfirmasi Reset Saldo Laci</h3>
                        <p class="text-xs text-text-muted">Prosedur penutupan kas laci kasir</p>
                    </div>
                </div>

                <div class="text-xs text-text-body space-y-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <p>Apakah Anda yakin ingin mereset saldo laci saat ini?</p>
                    <div class="font-mono text-[11px] space-y-0.5 pt-1">
                        <div>Saldo Cash: <span class="font-bold text-text-on-surface">{{ rupiah($saldoCash) }}</span></div>
                        <div>Saldo Transfer: <span class="font-bold text-text-on-surface">{{ rupiah($saldoTransfer) }}</span></div>
                        <div class="border-t border-slate-200 pt-1 font-bold">Total Laci: {{ rupiah($saldoTotal) }}</div>
                    </div>
                    <p class="text-[11px] text-text-muted pt-1">
                        Saldo aktif akan dibukukan ke tabel penutupan laci dan laci akan dimulai kembali dari Rp 0.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <x-ui.button variant="outline" size="sm" wire:click="tutupModalReset">
                        Batal
                    </x-ui.button>
                    <x-ui.button variant="primary" size="sm" wire:click="resetManual">
                        Ya, Reset Sekarang
                    </x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>

