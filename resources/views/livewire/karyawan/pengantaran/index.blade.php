<div class="space-y-6">
    {{-- Subjudul resmi sesuai F14 --}}
    <div>
        <p class="text-xs sm:text-sm text-text-muted">
            Bon yang dikirim kasir, diurutkan berdasarkan jadwal terdekat.
        </p>
    </div>

    {{-- 3 Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <x-ui.stat-card
            title="Pengantaran baru"
            :value="$this->jumlahBaru"
            accent="danger"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        <x-ui.stat-card
            title="Sedang berjalan"
            :value="$this->jumlahBerjalan"
            accent="primary"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>

        <x-ui.stat-card
            title="Selesai"
            :value="$this->jumlahSelesai"
            accent="success"
        >
            <x-slot:iconSlot>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:iconSlot>
        </x-ui.stat-card>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-2 p-1 bg-surface-container-low rounded-2xl border border-surface-border">
        <button
            type="button"
            wire:click="$set('tab', 'akan_diantar')"
            class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 {{ $tab === 'akan_diantar' ? 'bg-primary text-primary-on shadow-xs' : 'text-text-muted hover:text-text-slate' }}"
        >
            <span>Akan diantar</span>
            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'akan_diantar' ? 'bg-white/20 text-white' : 'bg-surface-border text-text-slate' }}">
                {{ $this->jumlahBerjalan }}
            </span>
        </button>

        <button
            type="button"
            wire:click="$set('tab', 'selesai')"
            class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 {{ $tab === 'selesai' ? 'bg-primary text-primary-on shadow-xs' : 'text-text-muted hover:text-text-slate' }}"
        >
            <span>Selesai</span>
            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'selesai' ? 'bg-white/20 text-white' : 'bg-surface-border text-text-slate' }}">
                {{ $this->jumlahSelesai }}
            </span>
        </button>
    </div>

    {{-- Daftar Kartu Pengantaran --}}
    <div class="space-y-4">
        @if($tab === 'akan_diantar')
            @forelse($this->pengirimanAkanDiantar as $item)
                <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs transition hover:shadow-sm" wire:key="akan-diantar-{{ $item->id }}">
                    {{-- Badges --}}
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($this->isJadwalTerdekat($item))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black tracking-wider uppercase bg-[#1E40AF] text-white shadow-xs">
                                BERIKUTNYA
                            </span>
                        @endif

                        @if($item->dibaca_pada === null)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black tracking-wider uppercase bg-status-danger text-white shadow-xs">
                                Baru
                            </span>
                        @endif

                        <x-ui.badge :variant="match($item->status->value) {
                            'sedang_disiapkan' => 'belum-lunas',
                            'siap_berangkat' => 'primary',
                            'dikirim_ke_karyawan' => 'neutral',
                            default => 'neutral'
                        }">
                            {{ $item->status->label() }}
                        </x-ui.badge>
                    </div>

                    {{-- Nama Pelanggan --}}
                    <h3 class="text-base sm:text-lg font-black text-text-slate tracking-tight">
                        {{ $item->bonPiutang?->pelanggan?->nama ?? $item->bonPiutang?->penjualan?->nama_pelanggan ?? 'Pelanggan Bon' }}
                    </h3>

                    {{-- Alamat Pengantaran (Ikon Pin) --}}
                    @if($item->bonPiutang?->alamat_pengantaran)
                        <div class="mt-1 flex items-start gap-1.5 text-xs text-text-muted">
                            <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="leading-relaxed">{{ $item->bonPiutang->alamat_pengantaran }}</span>
                        </div>
                    @endif

                    {{-- Daftar Barang --}}
                    <div class="mt-4 pt-3.5 border-t border-surface-border">
                        <p class="text-[11px] font-bold text-text-muted uppercase tracking-wider mb-2">DAFTAR BARANG</p>
                        <div class="space-y-1.5">
                            @forelse($item->detailPengiriman as $detail)
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="text-text-slate font-medium">{{ $detail->nama_barang }}</span>
                                    <span class="font-bold text-text-on-surface tabular-nums">
                                        {{ $detail->jumlah }} {{ $detail->satuan }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-text-muted italic">Tidak ada rincian barang.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Jadwal & Catatan Kasir --}}
                    <div class="mt-4 pt-3.5 border-t border-surface-border flex flex-col gap-2 text-xs">
                        @if($item->jadwal_kirim)
                            <div class="flex items-center gap-1.5 text-text-muted">
                                <svg class="w-4 h-4 text-text-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Jadwal: <strong class="text-text-slate font-semibold">{{ $item->jadwal_kirim->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</strong></span>
                            </div>
                        @endif

                        @if($item->catatan_kasir)
                            <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80 text-secondary-dark">
                                <span class="font-bold text-[11px] uppercase tracking-wider block mb-0.5">Catatan kasir:</span>
                                <span class="leading-relaxed">{{ $item->catatan_kasir }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Aksi HP-friendly --}}
                    <div class="mt-5 pt-3 border-t border-surface-border">
                        <x-ui.button
                            variant="primary"
                            size="lg"
                            class="w-full justify-center text-sm py-3 font-bold"
                            :href="route('karyawan.pengantaran.detail', $item->id)"
                        >
                            <span>Detail & Ubah Status →</span>
                        </x-ui.button>
                    </div>
                </div>
            @empty
                <x-ui.empty-state
                    title="Tidak ada pengantaran aktif"
                    description="Semua pengantaran tugas Anda sudah selesai atau belum ada pengantaran baru dari kasir."
                >
                    <x-slot:icon>
                        <svg class="h-7 w-7 text-primary/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </x-slot:icon>
                </x-ui.empty-state>
            @endforelse
        @else
            @forelse($this->pengirimanSelesai as $item)
                <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs transition hover:shadow-sm" wire:key="selesai-{{ $item->id }}">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <x-ui.badge variant="selesai">
                            Selesai
                        </x-ui.badge>

                        @if($item->waktu_selesai)
                            <span class="text-xs text-text-muted">
                                Selesai: {{ $item->waktu_selesai->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </span>
                        @endif
                    </div>

                    <h3 class="text-base sm:text-lg font-black text-text-slate tracking-tight">
                        {{ $item->bonPiutang?->pelanggan?->nama ?? $item->bonPiutang?->penjualan?->nama_pelanggan ?? 'Pelanggan Bon' }}
                    </h3>

                    @if($item->bonPiutang?->alamat_pengantaran)
                        <div class="mt-1 flex items-start gap-1.5 text-xs text-text-muted">
                            <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="leading-relaxed">{{ $item->bonPiutang->alamat_pengantaran }}</span>
                        </div>
                    @endif

                    <div class="mt-4 pt-3.5 border-t border-surface-border">
                        <p class="text-[11px] font-bold text-text-muted uppercase tracking-wider mb-2">DAFTAR BARANG</p>
                        <div class="space-y-1.5">
                            @forelse($item->detailPengiriman as $detail)
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="text-text-slate font-medium">{{ $detail->nama_barang }}</span>
                                    <span class="font-bold text-text-on-surface tabular-nums">
                                        {{ $detail->jumlah }} {{ $detail->satuan }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-text-muted italic">Tidak ada rincian barang.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-t border-surface-border">
                        <x-ui.button
                            variant="outline"
                            size="lg"
                            class="w-full justify-center text-sm py-3 font-bold"
                            :href="route('karyawan.pengantaran.detail', $item->id)"
                        >
                            <span>Lihat Detail →</span>
                        </x-ui.button>
                    </div>
                </div>
            @empty
                <x-ui.empty-state
                    title="Belum ada riwayat selesai"
                    description="Pengantaran yang telah diselesaikan akan tercatat rapi di sini."
                >
                    <x-slot:icon>
                        <svg class="h-7 w-7 text-status-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.empty-state>
            @endforelse
        @endif
    </div>
</div>
