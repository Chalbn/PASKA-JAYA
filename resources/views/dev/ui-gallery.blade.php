<x-layouts.app title="Galeri Komponen UI/UX" :role="request('role', 'admin')">
    <div class="max-w-7xl mx-auto space-y-10 pb-16">
        {{-- HEADER BANNER --}}
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
                                class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('role', 'admin') === $rKey ? 'bg-primary text-primary-on shadow-xs' : 'bg-surface-card text-text-body hover:text-text-slate border border-surface-border' }}"
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
                    <span>1. Palet Warna Resmi (Industrial Precision Commerce)</span>
                </h3>
                <span class="text-xs text-text-muted">Token di resources/css/app.css</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#2E4365] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Police Blue</p>
                    <p class="text-[11px] text-text-muted font-mono">#2E4365 (Primary)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#162D4D] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Deep Navy</p>
                    <p class="text-[11px] text-text-muted font-mono">#162D4D (Sidebar)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#E59D2C] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Marigold</p>
                    <p class="text-[11px] text-text-muted font-mono">#E59D2C (Secondary/Action)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#8A3B08] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Citrine Brown</p>
                    <p class="text-[11px] text-text-muted font-mono">#8A3B08 (Tertiary/Warning)</p>
                </div>
                <div class="bg-surface-card border border-surface-border rounded-xl p-3 shadow-xs">
                    <div class="h-14 rounded-lg bg-[#EBDDC5] border border-[#D9C4A6] mb-2 shadow-xs"></div>
                    <p class="text-xs font-bold text-text-slate">Pearl Chip</p>
                    <p class="text-[11px] text-text-muted font-mono">#EBDDC5 (Satuan)</p>
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

                <x-ui.card title="Logo Versi Putih (White)" subtitle="Digunakan pada topbar/sidebar navy & halaman auth">
                    <div class="py-4 flex items-center justify-center bg-[#162D4D] rounded-xl p-4">
                        <x-brand.logo variant="white" class="h-12 w-auto" />
                    </div>
                </x-ui.card>

                <x-ui.card title="Logo Mark / Favicon (PJ)" subtitle="Icon mark tunggal untuk avatar / mobile">
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
                <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                <span>3. Tombol (<code class="text-xs font-mono">x-ui.button</code>)</span>
            </h3>

            <x-ui.card>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-3">Varian Warna Resmi</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="primary">
                                Simpan Transaksi (Primary Marigold)
                            </x-ui.button>
                            <x-ui.button variant="secondary">
                                Cetak Struk (Secondary Police Blue)
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
                            <x-ui.button size="sm" variant="secondary">Kecil (sm)</x-ui.button>
                            <x-ui.button size="md" variant="secondary">Standar (md)</x-ui.button>
                            <x-ui.button size="lg" variant="primary">Tombol Besar POS (lg)</x-ui.button>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-3">Keadaan Loading & Dinonaktifkan (Disabled)</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.button variant="primary" :loading="true">
                                Menyimpan...
                            </x-ui.button>
                            <x-ui.button variant="secondary" :disabled="true">
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
                <x-ui.card title="Status Transaksi & Pembayaran" subtitle="Sesuai warna resmi PDF desain">
                    <div class="flex flex-wrap gap-2.5">
                        <x-ui.badge variant="lunas">Lunas</x-ui.badge>
                        <x-ui.badge variant="belum-lunas">Belum Lunas</x-ui.badge>
                        <x-ui.badge variant="selesai">Selesai</x-ui.badge>
                        <x-ui.badge variant="kritis">Kritis / Habis</x-ui.badge>
                        <x-ui.badge variant="menipis">Menipis</x-ui.badge>
                        <x-ui.badge variant="lewat-tempo">Lewat Tempo</x-ui.badge>
                        <x-ui.badge variant="cash">CASH</x-ui.badge>
                        <x-ui.badge variant="transfer">TRANSFER</x-ui.badge>
                    </div>
                </x-ui.card>

                <x-ui.card title="Chip Satuan Barang (Material Chips)" subtitle="Warna Pearl (#EBDDC5) teks Police Blue (#2E4365)">
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

        {{-- 5. KPI STAT CARDS (Bento Fintech Modern Soft Style) --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                    <span>5. KPI Stat Cards Soft Bento (<code class="text-xs font-mono">x-ui.stat-card</code>)</span>
                </h3>
                <span class="text-xs text-text-muted">Desain bento fintech modern, angka 1 baris utuh tabular-nums</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.stat-card 
                    title="Penjualan Hari Ini"
                    :value="rupiah(1820000)"
                    badge="+14 transaksi"
                    subtitle="Omzet penjualan langsung & kasir"
                    variant="marigold"
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
                    variant="primary"
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
                    variant="pearl"
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
                    subtitle="Segera lakukan pemesanan"
                    variant="danger"
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
                            name="jatuh_tempo"
                            label="Tanggal Jatuh Tempo"
                            hint="Tanggal pembayaran bon maksimal"
                        />

                        <x-ui.input 
                            name="email_contoh"
                            label="Input dengan Pesan Error"
                            value="email-salah@"
                            error="Format alamat email tidak valid."
                        />
                    </div>
                </x-ui.card>

                <div class="space-y-6">
                    <x-ui.card title="Combobox (Bisa Ketik Opsi Baru)">
                        <div class="space-y-4">
                            <x-ui.select 
                                name="kategori_id"
                                label="Kategori Barang (Combobox)"
                                :allowNew="true"
                                placeholder="Ketik atau pilih kategori..."
                                :options="['1' => 'Semen & Pasir', '2' => 'Besi & Baja', '3' => 'Kayu & Triplex', '4' => 'Cat & Pelapis']"
                                hint="Anda dapat mengetik kategori baru jika tidak ada pada daftar."
                            />

                            <x-ui.select 
                                name="metode_bayar"
                                label="Metode Pembayaran (Standar)"
                                :options="['cash' => 'Tunai (Cash)', 'transfer' => 'Transfer Bank']"
                            />
                        </div>
                    </x-ui.card>

                    <x-ui.card title="Upload Berkas / Bukti Pengantaran">
                        <x-ui.upload 
                            name="bukti_pengiriman"
                            label="Foto Bukti Penerimaan Barang"
                            hint="Foto bukti serah terima barang kepada pelanggan."
                            :camera="true"
                        />
                    </x-ui.card>
                </div>
            </div>
        </section>

        {{-- 7. KONTROL FILTER & TOGGLE --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                <span>7. Pill Filter & Segmented Toggle (Kartu | Tabel)</span>
            </h3>

            <x-ui.card>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <x-ui.pill-filter 
                        :options="['semua' => 'Semua Bon', 'belum-lunas' => 'Belum Lunas', 'lunas' => 'Lunas']"
                        active="belum-lunas"
                    />

                    <x-ui.toggle 
                        active="cards"
                        leftLabel="Kartu"
                        rightLabel="Tabel"
                    />
                </div>
            </x-ui.card>
        </section>

        {{-- 8. TABEL KEPADATAN TINGGI (HIGH-DENSITY TABLE) --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <span>8. Tabel Kepadatan Tinggi (<code class="text-xs font-mono">x-ui.table</code>)</span>
                </h3>
                <span class="text-xs text-text-muted">Header kapital kecil, zebra striping, angka rata kanan tabular-nums</span>
            </div>

            <x-ui.table :headers="[
                'No',
                'Barang / Pelanggan',
                'Satuan',
                ['text' => 'Jumlah', 'align' => 'right'],
                ['text' => 'Nominal', 'align' => 'right'],
                'Metode',
                'Status',
                ['text' => 'Aksi', 'align' => 'center']
            ]">
                <tr>
                    <td class="px-4 py-2.5 font-bold text-xs text-text-muted">01</td>
                    <td class="px-4 py-2.5 font-bold text-text-slate">Semen Padang 50kg</td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="sak">sak</x-ui.badge></td>
                    <td class="px-4 py-2.5 text-right font-bold tabular-nums">20</td>
                    <td class="px-4 py-2.5 text-right font-extrabold text-primary tabular-nums">{{ rupiah(1360000) }}</td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="cash">CASH</x-ui.badge></td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="lunas">Lunas</x-ui.badge></td>
                    <td class="px-4 py-2.5 text-center">
                        <button type="button" class="p-1 rounded-lg text-primary hover:bg-surface-container-low transition" title="Lihat">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2.5 font-bold text-xs text-text-muted">02</td>
                    <td class="px-4 py-2.5 font-bold text-text-slate">Besi Beton 10mm SNI</td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="batang">batang</x-ui.badge></td>
                    <td class="px-4 py-2.5 text-right font-bold tabular-nums">50</td>
                    <td class="px-4 py-2.5 text-right font-extrabold text-primary tabular-nums">{{ rupiah(4250000) }}</td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="transfer">TRANSFER</x-ui.badge></td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="belum-lunas">Belum Lunas</x-ui.badge></td>
                    <td class="px-4 py-2.5 text-center">
                        <button type="button" class="p-1 rounded-lg text-primary hover:bg-surface-container-low transition" title="Lihat">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2.5 font-bold text-xs text-text-muted">03</td>
                    <td class="px-4 py-2.5 font-bold text-text-slate">Pasir Pasang Urug</td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="kubik">kubik</x-ui.badge></td>
                    <td class="px-4 py-2.5 text-right font-bold tabular-nums">2</td>
                    <td class="px-4 py-2.5 text-right font-extrabold text-primary tabular-nums">{{ rupiah(550000) }}</td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="cash">CASH</x-ui.badge></td>
                    <td class="px-4 py-2.5"><x-ui.badge variant="selesai">Selesai</x-ui.badge></td>
                    <td class="px-4 py-2.5 text-center">
                        <button type="button" class="p-1 rounded-lg text-primary hover:bg-surface-container-low transition" title="Lihat">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </td>
                </tr>
            </x-ui.table>
        </section>

        {{-- 9. TIMELINE ALUR STATUS PENGIRIMAN --}}
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-text-slate tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span>9. Timeline Alur Status Pengiriman (<code class="text-xs font-mono">x-ui.timeline</code>)</span>
            </h3>

            <x-ui.card title="Pelacakan Pengantaran Bon #BON-202611-001" subtitle="Dipantau oleh Kasir & Karyawan Pengantar">
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
                    <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                    <span>10. Grafik Chart.js Alpine (Police Blue & Marigold, Tooltip Rupiah)</span>
                </h3>
                <span class="text-xs text-text-muted">Data kosong tetap menggambar sumbu bernilai 0</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Bar Chart: Penjualan 7 Hari --}}
                <x-ui.card title="Penjualan 7 Hari Terakhir (Bar Chart)" subtitle="Warna Police Blue (#2E4365) dengan tooltip format Rupiah">
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
                <x-ui.card title="Grafik Saldo Laci Mingguan (Stacked Bar)" subtitle="Cash (#2E4365) + Transfer (#E59D2C)">
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

                {{-- Contoh Grafik Saat Data Kosong (0 Sumbu Tetap Digambar) --}}
                <x-ui.card title="Grafik Saat Database Kosong (Nilai 0)" subtitle="ADR-012: Sumbu tetap digambar rapi pada angka 0">
                    <div 
                        x-data="paskaChartBar({
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                            values: [0, 0, 0, 0, 0, 0, 0],
                            title: 'Penjualan'
                        })" 
                        class="h-64 w-full pt-2"
                    >
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </x-ui.card>

                {{-- Contoh Empty State Komponen --}}
                <x-ui.card title="Komponen Empty State Default" subtitle="Ditampilkan saat tabel/daftar tidak memiliki data">
                    <x-ui.empty-state 
                        title="Belum ada transaksi hari ini"
                        description="Mulai catat transaksi penjualan langsung atau buat bon kredit baru."
                        actionLabel="+ Tambah Transaksi"
                    />
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
                        <x-ui.button variant="secondary" x-on:click="$dispatch('open-modal', 'modal-contoh')">
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

        {{-- 12. TAUTAN KE PREVIEW AUTH SPLIT-SCREEN --}}
        <section class="p-6 bg-gradient-to-r from-primary-deep to-primary rounded-2xl text-white shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="text-base font-bold">Pratinjau Layout Auth Split-Screen</h4>
                    <p class="text-xs text-[#D6E3FF] mt-1">
                        Halaman Login & Register dengan foto toko asli, overlay #162D4D/85, headline resmi, dan form tanpa demo account.
                    </p>
                </div>
                <x-ui.button variant="primary" href="/dev/auth">
                    Buka Pratinjau Auth
                </x-ui.button>
            </div>
        </section>
    </div>
</x-layouts.app>
