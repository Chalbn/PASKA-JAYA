<div class="space-y-6">
    {{-- Header Halaman Sesuai Desain PDF F08 --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">Master Item</h1>
            <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                Katalog bahan & barang — restock barang baru datang, edit harga, dan pantau stok.
            </p>
        </div>

        <div>
            <x-ui.button variant="primary" wire:click="openTambahModal" class="w-full sm:w-auto shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Tambah Barang</span>
            </x-ui.button>
        </div>
    </div>

    {{-- 4 Kartu Statistik (Nilai 0 saat Database Kosong) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Barang --}}
        <x-ui.stat-card 
            title="Total Barang"
            :value="(string) $stats['total_barang']"
            :subtitle="$stats['jumlah_kategori'] . ' kategori terdaftar'"
            accent="primary"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        {{-- Nilai Stok --}}
        <x-ui.stat-card 
            title="Nilai Stok"
            :value="rupiah($stats['nilai_stok'])"
            subtitle="Berdasarkan harga beli"
            accent="secondary"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        {{-- Stok Menipis --}}
        <x-ui.stat-card 
            title="Stok Menipis"
            :value="(string) $stats['stok_menipis']"
            :subtitle="$stats['stok_menipis'] > 0 ? 'Segera lakukan restock' : 'Semua stok dalam batas aman'"
            :badge="$stats['stok_menipis'] > 0 ? 'Perlu Restock' : null"
            accent="danger"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        {{-- Stok Aman --}}
        <x-ui.stat-card 
            title="Stok Aman"
            :value="(string) $stats['stok_aman']"
            subtitle="Di atas batas minimum"
            accent="success"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>
    </div>

    {{-- Filter, Pencarian, & Toggle Tampilan --}}
    <div class="bg-surface-card border border-surface-border rounded-2xl p-4 shadow-xs space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            {{-- Kotak Pencarian --}}
            <div class="flex-1 max-w-md relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-muted">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari bahan/barang atau kode…"
                    class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                />
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Dropdown Kategori Otomatis (ADR-010) --}}
                <div class="relative min-w-[160px]">
                    <select 
                        wire:model.live="kategori"
                        class="w-full pl-3 pr-8 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition appearance-none cursor-pointer"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->slug }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-text-muted">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                {{-- Toggle Mode Tampilan (Kartu | Tabel) --}}
                <div class="inline-flex items-center p-1 bg-surface-canvas border border-surface-border rounded-xl shadow-xs">
                    <button 
                        type="button"
                        wire:click="setView('cards')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition duration-150 ease-in-out {{ $view === 'cards' ? 'bg-surface-card text-primary font-bold shadow-xs border border-surface-border' : 'text-text-muted hover:text-text-slate font-medium' }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Kartu</span>
                    </button>

                    <button 
                        type="button"
                        wire:click="setView('table')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition duration-150 ease-in-out {{ $view === 'table' ? 'bg-surface-card text-primary font-bold shadow-xs border border-surface-border' : 'text-text-muted hover:text-text-slate font-medium' }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Tabel</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Info Ringkasan Jumlah Barang & Stok Menipis --}}
        <div class="text-xs text-text-muted flex items-center gap-2 pt-1 border-t border-surface-border/50">
            <span>{{ $barangPaginated->total() }} barang</span>
            <span>•</span>
            <span class="{{ $stats['stok_menipis'] > 0 ? 'text-status-danger font-semibold' : '' }}">
                {{ $stats['stok_menipis'] }} stok menipis
            </span>
        </div>
    </div>

    {{-- KONTEN UTAMA: MODE KARTU ATAU TABEL --}}
    @if($barangPaginated->isEmpty())
        {{-- Empty State jika data kosong --}}
        <x-ui.empty-state 
            title="Belum ada barang"
            description="Katalog master item masih kosong atau tidak ditemukan barang dengan kata kunci tersebut."
            actionLabel="+ Tambah Barang Pertama"
            actionEvent="openTambahModal"
        />
    @else
        @if($view === 'cards')
            {{-- Tampilan Grid Kartu Sesuai PDF F08 --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($barangPaginated as $barang)
                    @php
                        $isMenipis = $barang->stok <= $barang->stok_minimum;
                    @endphp
                    <div class="bg-surface-card border border-surface-border rounded-2xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group relative">
                        {{-- Tombol Hapus di Pojok Kanan Atas --}}
                        <div class="absolute top-3 right-3 z-10">
                            <button 
                                type="button"
                                wire:click="confirmDelete({{ $barang->id }})"
                                class="p-1.5 rounded-lg text-text-muted hover:text-status-danger hover:bg-status-danger-bg transition"
                                title="Hapus Barang"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        {{-- Area Konten yang Bisa Diklik untuk Membuka Restock/Edit --}}
                        <div wire:click="openEditModal({{ $barang->id }})" class="cursor-pointer space-y-3">
                            {{-- Foto / Thumbnail Barang --}}
                            <div class="w-full h-36 rounded-xl bg-surface-canvas overflow-hidden flex items-center justify-center border border-surface-border">
                                @if($barang->foto)
                                    <img 
                                        src="{{ asset('storage/' . $barang->foto) }}" 
                                        alt="{{ $barang->nama_barang }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                @else
                                    <div class="flex flex-col items-center justify-center text-text-muted/60">
                                        <svg class="w-10 h-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-[10px] font-medium tracking-tight">Tanpa Foto</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Nama & Kode Barang --}}
                            <div>
                                <span class="text-[11px] font-mono text-text-muted font-bold tracking-tight">
                                    {{ $barang->kode_barang }}
                                </span>
                                <h3 class="text-sm font-bold text-text-slate tracking-tight line-clamp-2 mt-0.5 group-hover:text-primary transition-colors">
                                    {{ $barang->nama_barang }}
                                </h3>
                                <p class="text-[11px] text-text-muted mt-0.5">
                                    {{ $barang->kategori?->nama ?? '-' }}
                                </p>
                            </div>

                            {{-- Badge Stok & Harga Jual --}}
                            <div class="flex items-center justify-between pt-2 border-t border-surface-border">
                                <div>
                                    @if($isMenipis)
                                        <x-ui.badge variant="kritis" size="sm">
                                            Kritis: {{ $barang->stok }}
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="primary" size="sm">
                                            Stok: {{ $barang->stok }}
                                        </x-ui.badge>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <span class="text-xs font-black text-primary tracking-tight tabular-nums">
                                        {{ rupiah($barang->harga_jual) }}
                                    </span>
                                    <span class="text-[10px] text-text-muted block -mt-0.5">/ {{ $barang->satuan }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Tampilan Tabel Sesuai PDF F08 --}}
            <div class="bg-surface-card border border-surface-border rounded-2xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-text-slate border-collapse">
                        <thead class="bg-surface-canvas text-[11px] uppercase tracking-wider text-text-muted font-bold border-b border-surface-border select-none">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 text-center w-12">No</th>
                                <th scope="col" class="px-4 py-3.5">Kode</th>
                                <th scope="col" class="px-4 py-3.5">Nama Barang</th>
                                <th scope="col" class="px-4 py-3.5">Kategori</th>
                                <th scope="col" class="px-4 py-3.5">Satuan</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Harga Beli</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Harga Jual</th>
                                <th scope="col" class="px-4 py-3.5 text-center">Stok</th>
                                <th scope="col" class="px-4 py-3.5">Tanggal Masuk</th>
                                <th scope="col" class="px-4 py-3.5 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @foreach($barangPaginated as $barang)
                                @php
                                    $isMenipis = $barang->stok <= $barang->stok_minimum;
                                @endphp
                                <tr class="hover:bg-surface-container-low/40 transition-colors">
                                    <td class="px-4 py-3 text-center text-text-muted tabular-nums">
                                        {{ $loop->iteration + ($barangPaginated->currentPage() - 1) * $barangPaginated->perPage() }}
                                    </td>
                                    <td class="px-4 py-3 font-mono font-bold text-primary">
                                        {{ $barang->kode_barang }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-text-slate">
                                        {{ $barang->nama_barang }}
                                    </td>
                                    <td class="px-4 py-3 text-text-muted">
                                        {{ $barang->kategori?->nama ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge variant="unit" size="sm">
                                            {{ $barang->satuan }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-text-muted tabular-nums">
                                        {{ rupiah($barang->harga_beli) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-text-slate tabular-nums">
                                        {{ rupiah($barang->harga_jual) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($isMenipis)
                                            <x-ui.badge variant="kritis" size="sm">
                                                {{ $barang->stok }} · Menipis
                                            </x-ui.badge>
                                        @else
                                            <x-ui.badge variant="primary" size="sm">
                                                {{ $barang->stok }} · Aman
                                            </x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-text-muted text-[11px] whitespace-nowrap">
                                        {{ $barang->tanggal_masuk_terakhir ? tanggal_id($barang->tanggal_masuk_terakhir) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button 
                                                type="button" 
                                                wire:click="openEditModal({{ $barang->id }})"
                                                class="p-1.5 rounded-lg text-text-muted hover:text-primary hover:bg-surface-container transition"
                                                title="Edit / Restock"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>

                                            <button 
                                                type="button" 
                                                wire:click="confirmDelete({{ $barang->id }})"
                                                class="p-1.5 rounded-lg text-text-muted hover:text-status-danger hover:bg-status-danger-bg transition"
                                                title="Hapus"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Navigasi Paginasi --}}
        <div class="mt-4">
            {{ $barangPaginated->links() }}
        </div>
    @endif

    {{-- MODAL TAMBAH BARANG --}}
    <x-ui.modal name="tambah-modal" title="Tambah Barang Baru" maxWidth="lg" wire:model="showTambahModal">
        <form wire:submit="simpanTambah" class="space-y-4">
            @if($errors->has('general'))
                <div class="p-3 rounded-xl bg-status-danger-bg text-status-danger text-xs font-medium">
                    {{ $errors->first('general') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kode Barang --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Kode Barang</label>
                    <input 
                        type="text" 
                        wire:model="kode_barang"
                        placeholder="Contoh: BRG-001"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('kode_barang') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Barang --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Nama Barang</label>
                    <input 
                        type="text" 
                        wire:model="nama_barang"
                        placeholder="mis. Semen Gresik 50kg"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('nama_barang') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kategori (Combobox / Ketik Baru) --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Kategori</label>
                    <input 
                        type="text" 
                        wire:model="kategori_nama"
                        list="kategori-suggestions"
                        placeholder="Pilih atau ketik kategori baru…"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <datalist id="kategori-suggestions">
                        @foreach($allKategori as $katName)
                            <option value="{{ $katName }}"></option>
                        @endforeach
                    </datalist>
                    <p class="text-[10px] text-text-muted mt-0.5">Kategori baru akan dibuat otomatis jika belum ada.</p>
                    @error('kategori_nama') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Satuan</label>
                    <input 
                        type="text" 
                        wire:model="satuan"
                        list="satuan-suggestions"
                        placeholder="Contoh: Sak, Batang, Dus, M3"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <datalist id="satuan-suggestions">
                        <option value="Sak"></option>
                        <option value="Batang"></option>
                        <option value="Dus"></option>
                        <option value="M3"></option>
                        <option value="Pcs"></option>
                        <option value="Kg"></option>
                        <option value="Lembar"></option>
                        <option value="Kaleng"></option>
                    </datalist>
                    @error('satuan') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Harga Beli --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Harga Beli (Rp)</label>
                    <input 
                        type="number" 
                        min="0"
                        wire:model.live="harga_beli"
                        placeholder="mis. 50000"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('harga_beli') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Harga Jual --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Harga Jual (Rp)</label>
                    <input 
                        type="number" 
                        min="0"
                        wire:model.live="harga_jual"
                        placeholder="mis. 65000"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('harga_jual') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Peringatan jika harga jual < harga beli --}}
            @if(!is_null($harga_beli) && !is_null($harga_jual) && $harga_jual < $harga_beli)
                <div class="p-2.5 rounded-xl bg-status-warning-bg text-status-warning text-xs font-semibold flex items-center gap-2 border border-amber-200">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Perhatian: Harga jual lebih rendah dari harga beli (potensi rugi).</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Stok Awal --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Stok Awal</label>
                    <input 
                        type="number" 
                        min="0"
                        wire:model="stok_awal"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('stok_awal') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Batas Stok Menipis --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Batas Stok Menipis (Default 10)</label>
                    <input 
                        type="number" 
                        min="0"
                        wire:model="stok_minimum"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('stok_minimum') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Foto Barang (Opsional) --}}
            <div>
                <label class="block text-xs font-semibold text-text-slate mb-1">Foto Barang (Opsional)</label>
                <input 
                    type="file" 
                    wire:model="foto"
                    accept="image/png,image/jpeg,image/webp"
                    class="block w-full text-xs text-text-muted file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-surface-container file:text-primary hover:file:bg-surface-container-high transition"
                />
                <p class="text-[10px] text-text-muted mt-1">Format: JPG, PNG, WebP (maks. 4 MB).</p>
                @error('foto') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Footer Tombol --}}
            <div class="pt-4 border-t border-surface-border flex items-center justify-end gap-3">
                <x-ui.button type="button" variant="outline" size="sm" wire:click="$set('showTambahModal', false)">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="simpanTambah">Simpan Barang</span>
                    <span wire:loading wire:target="simpanTambah">Menyimpan…</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- MODAL RESTOCK / EDIT BARANG --}}
    <x-ui.modal name="edit-modal" title="Restock / Edit Barang" maxWidth="lg" wire:model="showEditModal">
        <form wire:submit="simpanEdit" class="space-y-4">
            @if($errors->has('general'))
                <div class="p-3 rounded-xl bg-status-danger-bg text-status-danger text-xs font-medium">
                    {{ $errors->first('general') }}
                </div>
            @endif

            {{-- KOTAK KHUSUS RESTOCK (Sesuai F08 & PDF Desain) --}}
            <div class="p-4 rounded-2xl bg-surface-container/60 border border-primary/20 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-primary">
                        Tambah stok barang baru datang
                    </span>
                    <span class="text-xs text-text-muted">
                        Stok saat ini: <strong class="text-text-slate">{{ $edit_stok_saat_ini }}</strong> {{ $edit_satuan }}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <input 
                            type="number" 
                            min="0"
                            wire:model.live="tambah_stok"
                            placeholder="0"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-primary/30 bg-white text-text-slate font-bold tabular-nums focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                    </div>
                    <div class="text-xs font-semibold text-text-muted whitespace-nowrap">
                        Stok setelah disimpan: 
                        <span class="font-extrabold text-primary tabular-nums">
                            {{ $edit_stok_saat_ini + max(0, (int) $tambah_stok) }}
                        </span> 
                        {{ $edit_satuan }}
                    </div>
                </div>
                <p class="text-[10px] text-text-muted">
                    Isi jika ada penambahan stok baru. Otomatis mencatat riwayat restock dan memperbarui tanggal masuk.
                </p>
                @error('tambah_stok') <span class="text-[11px] text-status-danger block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kode Barang --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Kode Barang</label>
                    <input 
                        type="text" 
                        wire:model="edit_kode_barang"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('edit_kode_barang') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Barang --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Nama Barang</label>
                    <input 
                        type="text" 
                        wire:model="edit_nama_barang"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('edit_nama_barang') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Kategori</label>
                    <input 
                        type="text" 
                        wire:model="edit_kategori_nama"
                        list="edit-kategori-suggestions"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <datalist id="edit-kategori-suggestions">
                        @foreach($allKategori as $katName)
                            <option value="{{ $katName }}"></option>
                        @endforeach
                    </datalist>
                    @error('edit_kategori_nama') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Satuan --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Satuan</label>
                    <input 
                        type="text" 
                        wire:model="edit_satuan"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('edit_satuan') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Harga Beli --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Harga Beli (Rp)</label>
                    <input 
                        type="number" 
                        min="0"
                        wire:model.live="edit_harga_beli"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('edit_harga_beli') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Harga Jual --}}
                <div>
                    <label class="block text-xs font-semibold text-text-slate mb-1">Harga Jual (Rp)</label>
                    <input 
                        type="number" 
                        min="0"
                        wire:model.live="edit_harga_jual"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    @error('edit_harga_jual') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Peringatan jika harga jual < harga beli --}}
            @if(!is_null($edit_harga_beli) && !is_null($edit_harga_jual) && $edit_harga_jual < $edit_harga_beli)
                <div class="p-2.5 rounded-xl bg-status-warning-bg text-status-warning text-xs font-semibold flex items-center gap-2 border border-amber-200">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Perhatian: Harga jual lebih rendah dari harga beli (potensi rugi).</span>
                </div>
            @endif

            {{-- Batas Stok Menipis --}}
            <div>
                <label class="block text-xs font-semibold text-text-slate mb-1">Batas Stok Menipis</label>
                <input 
                    type="number" 
                    min="0"
                    wire:model="edit_stok_minimum"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate tabular-nums focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
                @error('edit_stok_minimum') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Ganti Foto Barang --}}
            <div>
                <label class="block text-xs font-semibold text-text-slate mb-1">Ganti Foto Barang</label>
                <div class="flex items-center gap-3">
                    @if($edit_foto_lama)
                        <div class="w-12 h-12 rounded-xl bg-surface-canvas border border-surface-border overflow-hidden shrink-0">
                            <img src="{{ asset('storage/' . $edit_foto_lama) }}" alt="Foto saat ini" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <input 
                        type="file" 
                        wire:model="edit_foto_baru"
                        accept="image/png,image/jpeg,image/webp"
                        class="block w-full text-xs text-text-muted file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-surface-container file:text-primary hover:file:bg-surface-container-high transition"
                    />
                </div>
                @error('edit_foto_baru') <span class="text-[11px] text-status-danger mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Footer Tombol --}}
            <div class="pt-4 border-t border-surface-border flex items-center justify-end gap-3">
                <x-ui.button type="button" variant="outline" size="sm" wire:click="$set('showEditModal', false)">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="simpanEdit">Simpan Perubahan</span>
                    <span wire:loading wire:target="simpanEdit">Menyimpan…</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <x-ui.confirm 
        name="delete-barang-modal"
        title="Hapus Barang"
        :message="'Hapus barang? Barang \'' . $deleteBarangNama . '\' akan dihapus dari master item.'"
        confirmText="Ya, hapus"
        cancelText="Batal"
        confirmAction="deleteBarang"
        variant="danger"
        wire:model="showDeleteModal"
    />
</div>
