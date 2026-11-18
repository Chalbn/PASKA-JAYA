<div class="-m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 min-h-[calc(100vh-5rem)] bg-gradient-to-br from-[#F4F7FE] via-[#EEF4FF] to-[#DBE8FF]">
    <div class="space-y-6">
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Ringkasan Sistem</h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">Gambaran data dan pengguna di seluruh sistem PASKA JAYA.</p>
            </div>

            <div class="shrink-0">
                <a
                    href="{{ route('admin.pengguna') }}"
                    wire:navigate
                    title="Kelola Pengguna (Fitur ke-2: Manajemen Pengguna)"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#1D4ED8] hover:bg-[#1E40AF] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Kelola Pengguna
                </a>
            </div>
        </div>

        {{-- 4 Kartu Ringkasan (Gaya Pastel Biru Sesuai Referensi) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Total pengguna --}}
            <div class="rounded-2xl bg-gradient-to-br from-[#F7FAFF] to-[#EBF2FF] border border-white/90 px-5 py-4 shadow-xs flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-white/90 border border-white shadow-2xs flex items-center justify-center text-[#2563EB] shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-text-muted">Total pengguna</p>
                    <p class="text-2xl font-extrabold text-text-on-surface tracking-tight tabular-nums leading-tight mt-0.5">
                        {{ number_format($kartu['pengguna']['total'], 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-medium text-text-muted mt-0.5">
                        {{ $kartu['pengguna']['aktif'] }} aktif
                    </p>
                </div>
            </div>

            {{-- Master item --}}
            <div class="rounded-2xl bg-gradient-to-br from-[#F7F8FF] to-[#ECEFFF] border border-white/90 px-5 py-4 shadow-xs flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-white/90 border border-white shadow-2xs flex items-center justify-center text-[#2563EB] shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-text-muted">Master item</p>
                    <p class="text-2xl font-extrabold text-text-on-surface tracking-tight tabular-nums leading-tight mt-0.5">
                        {{ number_format($kartu['master_item']['total'], 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-medium text-text-muted mt-0.5">
                        {{ $kartu['master_item']['menipis'] }} stok menipis
                    </p>
                </div>
            </div>

            {{-- Transaksi keluar --}}
            <div class="rounded-2xl bg-gradient-to-br from-[#E6F7FE] to-[#CEEFFE] border border-[#BDE6FA] px-5 py-4 shadow-xs flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-white/90 border border-white shadow-2xs flex items-center justify-center text-[#0284C7] shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-text-body">Transaksi keluar</p>
                    <p class="text-2xl font-extrabold text-text-on-surface tracking-tight tabular-nums leading-tight mt-0.5">
                        {{ number_format($kartu['transaksi_keluar']['total'], 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-medium text-text-body/80 mt-0.5">
                        {{ $kartu['transaksi_keluar']['bon'] }} data bon
                    </p>
                </div>
            </div>

            {{-- Pengantaran --}}
            <div class="rounded-2xl bg-gradient-to-br from-[#F6F5FE] to-[#ECEAFD] border border-white/90 px-5 py-4 shadow-xs flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-white/90 border border-white shadow-2xs flex items-center justify-center text-[#4F46E5] shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-text-muted">Pengantaran</p>
                    <p class="text-2xl font-extrabold text-text-on-surface tracking-tight tabular-nums leading-tight mt-0.5">
                        {{ number_format($kartu['pengantaran']['total'], 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] font-medium text-text-muted mt-0.5">
                        {{ $kartu['pengantaran']['berjalan'] }} berjalan
                    </p>
                </div>
            </div>
        </div>

        {{-- Pengguna per Role & Status Sistem --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Pengguna per Role --}}
            <div class="rounded-2xl bg-white/95 border border-white/90 shadow-xs p-6">
                <h2 class="text-base font-bold text-text-on-surface tracking-tight mb-5">Pengguna per Role</h2>

                <div class="space-y-4">
                    @foreach ($perRole as $baris)
                        <div>
                            <div class="flex items-center justify-between text-xs sm:text-sm mb-2">
                                <span class="font-semibold text-text-slate">{{ $baris['label'] }}</span>
                                <span class="text-xs font-medium text-text-muted tabular-nums">{{ number_format($baris['jumlah'], 0, ',', '.') }} akun</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-[#EEF2FA] overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-[#254EDB] transition-all duration-300"
                                    style="width: {{ $baris['persen'] }}%;"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status Sistem --}}
            <div class="rounded-2xl bg-white/95 border border-white/90 shadow-xs p-6">
                <h2 class="text-base font-bold text-text-on-surface tracking-tight mb-3">Status Sistem</h2>

                <dl class="divide-y divide-[#EFF3FA] text-sm">
                    <div class="py-3 flex items-center justify-between gap-4">
                        <dt class="text-text-body font-medium">Layanan aplikasi</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $status['layanan']['normal'] ? 'bg-[#EBF2FF] text-[#1D4ED8]' : 'bg-status-danger-bg text-status-danger' }}">
                                {{ $status['layanan']['label'] }}
                            </span>
                        </dd>
                    </div>

                    <div class="py-3 flex items-center justify-between gap-4">
                        <dt class="text-text-body font-medium">Basis data</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $status['basis_data']['terhubung'] ? 'bg-[#EBF2FF] text-[#1D4ED8]' : 'bg-status-danger-bg text-status-danger' }}">
                                {{ $status['basis_data']['label'] }}
                            </span>
                        </dd>
                    </div>

                    <div class="py-3 flex items-center justify-between gap-4">
                        <dt class="text-text-body font-medium">Pencadangan terakhir</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full bg-[#EBF2FF] text-[#1D4ED8] px-3 py-1 text-xs font-semibold">
                                {{ $status['pencadangan'] }}
                            </span>
                        </dd>
                    </div>

                    <div class="py-3 flex items-center justify-between gap-4">
                        <dt class="text-text-body font-medium">Catatan kehadiran</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full bg-[#EBF2FF] text-[#1D4ED8] px-3 py-1 text-xs font-semibold tabular-nums">
                                {{ number_format($status['kehadiran'], 0, ',', '.') }} data
                            </span>
                        </dd>
                    </div>

                    <div class="py-3 last:pb-0 flex items-center justify-between gap-4">
                        <dt class="text-text-body font-medium">Catatan bahan harian</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full bg-[#EBF2FF] text-[#1D4ED8] px-3 py-1 text-xs font-semibold tabular-nums">
                                {{ number_format($status['bahan_harian'], 0, ',', '.') }} data
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
