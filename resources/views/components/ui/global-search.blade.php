@php
    $canSearch = auth()->check();
    $searchUrl = Route::has('global-search') ? route('global-search') : url('/global-search');
@endphp

<div
    x-data="{
        open: false,
        query: '',
        loading: false,
        barangResults: [],
        bonResults: [],
        canViewBon: false,
        canSearch: {{ $canSearch ? 'true' : 'false' }},
        debounceTimer: null,
        openModal() {
            this.open = true;
            this.$nextTick(() => this.$refs.paletteInput?.focus());
            if (this.barangResults.length === 0) {
                this.runSearch();
            }
        },
        closeModal() {
            this.open = false;
        },
        onInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.runSearch(), 220);
        },
        async runSearch() {
            if (!this.canSearch) return;
            this.loading = true;
            try {
                const url = new URL('{{ $searchUrl }}', window.location.origin);
                if (this.query.trim() !== '') {
                    url.searchParams.set('q', this.query.trim());
                }
                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                this.barangResults = Array.isArray(data.barang) ? data.barang : [];
                this.bonResults = Array.isArray(data.bon) ? data.bon : [];
                this.canViewBon = Boolean(data.can_view_bon);
            } catch (e) {
            } finally {
                this.loading = false;
            }
        }
    }"
    x-on:keydown.window="if ((($event.ctrlKey || $event.metaKey) && $event.key.toLowerCase() === 'k')) { $event.preventDefault(); open ? closeModal() : openModal(); }"
    x-on:keydown.escape.window="if (open) closeModal()"
    class="shrink-0"
>
    {{-- Tombol Search Liquid Glass di Topbar --}}
    <button
        type="button"
        x-on:click="openModal()"
        aria-label="Pencarian Cepat Global (Ctrl+K)"
        title="Pencarian Cepat (Ctrl + K)"
        class="inline-flex items-center gap-2 px-2.5 h-9 sm:px-3.5 sm:h-10 rounded-2xl bg-white/12 hover:bg-white/20 backdrop-blur-md border border-white/25 text-white shadow-[inset_0_1px_1px_rgba(255,255,255,0.35),0_4px_12px_rgba(15,23,42,0.12)] transition-all duration-200 cursor-pointer focus:outline-none"
    >
        <svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span class="hidden lg:inline text-xs font-medium text-blue-100">Cari cepat…</span>
        <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-white/15 text-white rounded-md border border-white/20 tracking-wider">Ctrl K</kbd>
    </button>

    {{-- Modal Command Palette --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
        class="fixed inset-0 z-[70] flex items-start justify-center pt-16 sm:pt-24 px-4 bg-slate-900/50 backdrop-blur-xs text-left"
        role="dialog"
        aria-modal="true"
        aria-label="Pencarian Cepat Global"
    >
        <div
            x-on:click.outside="closeModal()"
            class="w-full max-w-2xl rounded-2xl bg-white shadow-[0_24px_60px_-12px_rgba(15,23,42,0.28)] border border-slate-200/80 overflow-hidden"
        >
            {{-- Input Pencarian --}}
            <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100">
                <svg class="w-5 h-5 text-[#1D3FA3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="search"
                    x-ref="paletteInput"
                    x-model="query"
                    x-on:input="onInput()"
                    placeholder="Cek harga & stok barang (nama/kode) atau cari pelanggan bon..."
                    class="flex-1 bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none"
                />
                <div x-show="loading" class="w-4 h-4 border-2 border-[#1D3FA3] border-t-transparent rounded-full animate-spin shrink-0"></div>
                <button
                    type="button"
                    x-on:click="closeModal()"
                    class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold text-slate-500 transition cursor-pointer"
                >
                    ESC
                </button>
            </div>

            {{-- Hasil Pencarian --}}
            <div class="max-h-[60vh] overflow-y-auto dropdown-scrollbar divide-y divide-slate-100">
                {{-- Bagian 1: Cek Harga & Stok Barang --}}
                <div class="p-3 sm:p-4">
                    <div class="flex items-center justify-between mb-2.5 px-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Cek Harga & Stok Barang
                        </span>
                        <span class="text-[11px] text-slate-400" x-text="query.trim() ? 'Hasil pencarian' : 'Produk teratas'"></span>
                    </div>

                    <div class="space-y-1.5" x-show="barangResults.length > 0">
                        <template x-for="b in barangResults" :key="b.id">
                            <a
                                :href="b.url || '#'"
                                x-on:click="if (!b.url) { $event.preventDefault(); }"
                                class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl bg-slate-50/70 hover:bg-[#EEF3FF] border border-transparent hover:border-[#1D3FA3]/20 transition group"
                                :class="b.url ? 'cursor-pointer' : 'cursor-default'"
                            >
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600" x-text="b.kode"></span>
                                        <p class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-[#1D3FA3] truncate" x-text="b.nama"></p>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        Satuan: <span class="font-semibold text-slate-700" x-text="b.satuan"></span>
                                    </p>
                                </div>

                                <div class="text-right shrink-0 flex items-center gap-3">
                                    <div>
                                        <p class="text-xs sm:text-sm font-extrabold text-[#1D3FA3] tabular-nums" x-text="b.harga_formatted"></p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold mt-0.5 tabular-nums border"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': b.stok_status === 'aman',
                                                'bg-amber-50 text-amber-700 border-amber-200': b.stok_status === 'menipis',
                                                'bg-rose-50 text-rose-700 border-rose-200': b.stok_status === 'habis',
                                            }"
                                            x-text="b.stok_status === 'habis' ? 'Stok Habis (0)' : `Stok: ${b.stok} ${b.satuan}`"
                                        ></span>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <div x-show="!loading && barangResults.length === 0" class="py-6 text-center text-xs text-slate-400">
                        Tidak ada barang yang cocok dengan kata kunci tersebut.
                    </div>
                </div>

                {{-- Bagian 2: Pelanggan & Status Bon (Owner, Kasir, Admin) --}}
                <template x-if="canViewBon && query.trim() !== ''">
                    <div class="p-3 sm:p-4 bg-slate-50/40">
                        <div class="flex items-center justify-between mb-2.5 px-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Pelanggan & Status Bon
                            </span>
                        </div>

                        <div class="space-y-1.5" x-show="bonResults.length > 0">
                            <template x-for="bon in bonResults" :key="bon.id">
                                <a
                                    :href="bon.url || '#'"
                                    x-on:click="if (!bon.url) { $event.preventDefault(); }"
                                    class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl bg-white hover:bg-[#EEF3FF] border border-slate-200/70 hover:border-[#1D3FA3]/25 transition"
                                >
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-[#EBF2FF] text-[#1D3FA3]" x-text="bon.kode_bon"></span>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800 truncate" x-text="bon.nama_pelanggan"></p>
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-1">
                                            Jatuh tempo: <span class="font-medium text-slate-700" x-text="bon.jatuh_tempo"></span>
                                        </p>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-extrabold tabular-nums" :class="bon.is_lunas ? 'text-emerald-600' : 'text-rose-600'" x-text="bon.is_lunas ? bon.total_tagihan_formatted : `Sisa ${bon.sisa_formatted}`"></p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold mt-0.5 border"
                                            :class="bon.is_lunas ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                            x-text="bon.status_label"
                                        ></span>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <div x-show="!loading && bonResults.length === 0" class="py-4 text-center text-xs text-slate-400">
                            Tidak ada data bon pelanggan untuk pencarian ini.
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
