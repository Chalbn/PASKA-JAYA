@php
    $rolePrefix = auth()->user()?->hasRole('owner') ? 'owner' : 'kasir';
@endphp
<x-layouts.app title="Master Item">
    <div
        class="space-y-6"
        x-data="{
            showTambahModal: false,
            showEditModal: false,
            showDeleteModal: false,
            editBarangId: null,
            edit_kode_barang: '',
            edit_nama_barang: '',
            edit_kategori_nama: '',
            edit_satuan: 'Sak',
            edit_harga_beli: 0,
            edit_harga_jual: 0,
            edit_stok_minimum: 10,
            edit_stok_saat_ini: 0,
            tambah_stok: 0,
            deleteBarangId: null,
            deleteBarangNama: '',
            openEditModal(b) {
                this.editBarangId = b.id;
                this.edit_kode_barang = b.kode_barang;
                this.edit_nama_barang = b.nama_barang;
                this.edit_kategori_nama = b.kategori_nama || '';
                this.edit_satuan = b.satuan;
                this.edit_harga_beli = b.harga_beli;
                this.edit_harga_jual = b.harga_jual;
                this.edit_stok_minimum = b.stok_minimum;
                this.edit_stok_saat_ini = b.stok;
                this.tambah_stok = 0;
                this.showEditModal = true;
            },
            confirmDelete(id, nama) {
                this.deleteBarangId = id;
                this.deleteBarangNama = nama;
                this.showDeleteModal = true;
            }
        }"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">Master Item</h1>
                <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                    Katalog bahan & barang — restock barang baru datang, edit harga, dan pantau stok.
                </p>
            </div>

            <div>
                <x-ui.button variant="primary" type="button" x-on:click="showTambahModal = true" class="w-full sm:w-auto shadow-sm cursor-pointer">
                    <span>+ Tambah Barang</span>
                </x-ui.button>
            </div>
        </div>

        {{-- 4 Kartu Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.stat-card
                title="Total Barang"
                :value="(string) $stats['total_barang']"
                :subtitle="$stats['jumlah_kategori'] . ' kategori terdaftar'"
                accent="primary"
            />
            <x-ui.stat-card
                title="Nilai Stok"
                :value="rupiah($stats['nilai_stok'])"
                subtitle="Berdasarkan harga beli"
                accent="secondary"
            />
            <x-ui.stat-card
                title="Stok Menipis"
                :value="(string) $stats['stok_menipis']"
                :subtitle="$stats['stok_menipis'] > 0 ? 'Segera lakukan restock' : 'Semua stok dalam batas aman'"
                :badge="$stats['stok_menipis'] > 0 ? 'Perlu Restock' : null"
                accent="danger"
            />
            <x-ui.stat-card
                title="Stok Aman"
                :value="(string) $stats['stok_aman']"
                subtitle="Di atas batas minimum"
                accent="success"
            />
        </div>

        {{-- Filter, Pencarian, & Toggle Tampilan --}}
        <form method="GET" action="{{ route($rolePrefix . '.master-item') }}" class="bg-surface-card border border-surface-border rounded-2xl p-4 shadow-xs space-y-3">
            <input type="hidden" name="view" value="{{ $view ?? 'cards' }}" />
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex-1 max-w-md relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari bahan/barang atau kode…"
                        class="w-full px-4 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="relative min-w-[160px]">
                        <select
                            name="kategori"
                            onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition cursor-pointer"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->slug }}" @selected(($kategori ?? '') === $kat->slug)>{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="inline-flex items-center p-1 bg-surface-canvas border border-surface-border rounded-xl shadow-xs">
                        <a
                            href="{{ route($rolePrefix . '.master-item', ['search' => $search ?? '', 'kategori' => $kategori ?? '', 'view' => 'cards']) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition {{ ($view ?? 'cards') === 'cards' ? 'bg-surface-card text-primary font-bold shadow-xs border border-surface-border' : 'text-text-muted hover:text-text-slate font-medium' }}"
                        >
                            <span>Kartu</span>
                        </a>
                        <a
                            href="{{ route($rolePrefix . '.master-item', ['search' => $search ?? '', 'kategori' => $kategori ?? '', 'view' => 'table']) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition {{ ($view ?? 'cards') === 'table' ? 'bg-surface-card text-primary font-bold shadow-xs border border-surface-border' : 'text-text-muted hover:text-text-slate font-medium' }}"
                        >
                            <span>Tabel</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-xs text-text-muted flex items-center gap-2 pt-1 border-t border-surface-border/50">
                <span>{{ $barangPaginated->total() }} barang</span>
                <span>•</span>
                <span class="{{ $stats['stok_menipis'] > 0 ? 'text-status-danger font-semibold' : '' }}">
                    {{ $stats['stok_menipis'] }} stok menipis
                </span>
            </div>
        </form>

        @if($barangPaginated->isEmpty())
            <x-ui.empty-state
                title="Belum ada barang"
                description="Katalog master item masih kosong atau tidak ditemukan barang dengan kata kunci tersebut."
            />
        @else
            @if(($view ?? 'cards') === 'cards')
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($barangPaginated as $barang)
                        @php
                            $isMenipis = $barang->stok <= $barang->stok_minimum;
                            $payload = [
                                'id' => $barang->id,
                                'kode_barang' => $barang->kode_barang,
                                'nama_barang' => $barang->nama_barang,
                                'kategori_nama' => $barang->kategori?->nama ?? '',
                                'satuan' => $barang->satuan,
                                'harga_beli' => (int) $barang->harga_beli,
                                'harga_jual' => (int) $barang->harga_jual,
                                'stok_minimum' => (int) $barang->stok_minimum,
                                'stok' => (int) $barang->stok,
                            ];
                        @endphp
                        <div class="bg-surface-card border border-surface-border rounded-2xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group relative">
                            <div class="absolute top-3 right-3 z-10">
                                <button
                                    type="button"
                                    x-on:click="confirmDelete({{ $barang->id }}, @js($barang->nama_barang))"
                                    class="p-1.5 rounded-lg text-text-muted hover:text-status-danger hover:bg-status-danger-bg transition cursor-pointer"
                                    title="Hapus Barang"
                                >
                                    🗑
                                </button>
                            </div>

                            <div x-on:click="openEditModal(@js($payload))" class="cursor-pointer space-y-3">
                                <div class="w-full h-36 rounded-xl bg-surface-canvas overflow-hidden flex items-center justify-center border border-surface-border">
                                    @if($barang->foto)
                                        <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover" />
                                    @else
                                        <span class="text-[10px] font-medium text-text-muted/60">Tanpa Foto</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="text-[11px] font-mono text-text-muted font-bold tracking-tight">{{ $barang->kode_barang }}</span>
                                    <h3 class="text-sm font-bold text-text-slate tracking-tight line-clamp-2 mt-0.5 group-hover:text-primary transition-colors">{{ $barang->nama_barang }}</h3>
                                    <p class="text-[11px] text-text-muted mt-0.5">{{ $barang->kategori?->nama ?? '-' }}</p>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-surface-border">
                                    <div>
                                        @if($isMenipis)
                                            <x-ui.badge variant="kritis" size="sm">Kritis: {{ $barang->stok }}</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="primary" size="sm">Stok: {{ $barang->stok }}</x-ui.badge>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-black text-primary tracking-tight tabular-nums">{{ rupiah($barang->harga_jual) }}</span>
                                        <span class="text-[10px] text-text-muted block -mt-0.5">/ {{ $barang->satuan }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-surface-card border border-surface-border rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-text-slate border-collapse">
                            <thead class="bg-surface-canvas text-[11px] uppercase tracking-wider text-text-muted font-bold border-b border-surface-border select-none">
                                <tr>
                                    <th class="px-4 py-3.5 text-center w-12">No</th>
                                    <th class="px-4 py-3.5">Kode</th>
                                    <th class="px-4 py-3.5">Nama Barang</th>
                                    <th class="px-4 py-3.5">Kategori</th>
                                    <th class="px-4 py-3.5">Satuan</th>
                                    <th class="px-4 py-3.5 text-right">Harga Beli</th>
                                    <th class="px-4 py-3.5 text-right">Harga Jual</th>
                                    <th class="px-4 py-3.5 text-center">Stok</th>
                                    <th class="px-4 py-3.5 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-border">
                                @foreach($barangPaginated as $barang)
                                    @php
                                        $isMenipis = $barang->stok <= $barang->stok_minimum;
                                        $payload = [
                                            'id' => $barang->id,
                                            'kode_barang' => $barang->kode_barang,
                                            'nama_barang' => $barang->nama_barang,
                                            'kategori_nama' => $barang->kategori?->nama ?? '',
                                            'satuan' => $barang->satuan,
                                            'harga_beli' => (int) $barang->harga_beli,
                                            'harga_jual' => (int) $barang->harga_jual,
                                            'stok_minimum' => (int) $barang->stok_minimum,
                                            'stok' => (int) $barang->stok,
                                        ];
                                    @endphp
                                    <tr class="hover:bg-surface-container-low/40 transition-colors">
                                        <td class="px-4 py-3 text-center text-text-muted tabular-nums">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 font-mono font-bold text-primary">{{ $barang->kode_barang }}</td>
                                        <td class="px-4 py-3 font-semibold text-text-slate">{{ $barang->nama_barang }}</td>
                                        <td class="px-4 py-3 text-text-muted">{{ $barang->kategori?->nama ?? '-' }}</td>
                                        <td class="px-4 py-3"><x-ui.badge variant="unit" size="sm">{{ $barang->satuan }}</x-ui.badge></td>
                                        <td class="px-4 py-3 text-right font-medium text-text-muted tabular-nums">{{ rupiah($barang->harga_beli) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-text-slate tabular-nums">{{ rupiah($barang->harga_jual) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($isMenipis)
                                                <x-ui.badge variant="kritis" size="sm">{{ $barang->stok }} · Menipis</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="primary" size="sm">{{ $barang->stok }} · Aman</x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" x-on:click="openEditModal(@js($payload))" class="p-1.5 rounded-lg text-text-muted hover:text-primary cursor-pointer">✎</button>
                                                <button type="button" x-on:click="confirmDelete({{ $barang->id }}, @js($barang->nama_barang))" class="p-1.5 rounded-lg text-text-muted hover:text-status-danger cursor-pointer">🗑</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                {{ $barangPaginated->links() }}
            </div>
        @endif

        {{-- Modal Tambah Barang --}}
        <div x-show="showTambahModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div x-on:click.outside="showTambahModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-surface-border pb-3">
                    <h3 class="text-base font-bold text-text-slate">Tambah Barang Baru</h3>
                    <button type="button" x-on:click="showTambahModal = false" class="text-text-muted hover:text-text-slate">✕</button>
                </div>

                <form method="POST" action="{{ route($rolePrefix . '.master-item.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Kode Barang</label>
                            <input type="text" name="kode_barang" value="{{ $kodeBerikutnya ?? 'BRG-001' }}" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Nama Barang</label>
                            <input type="text" name="nama_barang" required placeholder="mis. Semen Gresik 50kg" class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Kategori</label>
                            <input type="text" name="kategori_nama" list="kategori-suggestions" required placeholder="Pilih atau ketik kategori baru…" class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                            <datalist id="kategori-suggestions">
                                @foreach($allKategori as $katName)
                                    <option value="{{ $katName }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Satuan</label>
                            <input type="text" name="satuan" value="Sak" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" min="0" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Harga Jual (Rp)</label>
                            <input type="number" name="harga_jual" min="0" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Stok Awal</label>
                            <input type="number" name="stok_awal" min="0" value="0" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Batas Stok Menipis</label>
                            <input type="number" name="stok_minimum" min="0" value="10" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-slate mb-1">Foto Barang (Opsional)</label>
                        <input type="file" name="foto" accept="image/png,image/jpeg,image/webp" class="block w-full text-xs text-text-muted" />
                    </div>

                    <div class="pt-4 border-t border-surface-border flex items-center justify-end gap-3">
                        <x-ui.button type="button" variant="outline" size="sm" x-on:click="showTambahModal = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm">Simpan Barang</x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Restock / Edit Barang --}}
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div x-on:click.outside="showEditModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-surface-border pb-3">
                    <h3 class="text-base font-bold text-text-slate">Restock / Edit Barang</h3>
                    <button type="button" x-on:click="showEditModal = false" class="text-text-muted hover:text-text-slate">✕</button>
                </div>

                <form method="POST" x-bind:action="'{{ url('/' . $rolePrefix . '/master-item') }}/' + editBarangId + '/restock'" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="p-4 rounded-2xl bg-surface-container/60 border border-primary/20 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-primary">Tambah stok barang baru datang</span>
                            <span class="text-text-muted">Stok saat ini: <strong class="text-text-slate" x-text="edit_stok_saat_ini"></strong> <span x-text="edit_satuan"></span></span>
                        </div>
                        <input type="number" name="tambah_stok" min="0" x-model="tambah_stok" class="w-full px-3 py-2 text-xs rounded-xl border border-primary/30 bg-white font-bold" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Kode Barang</label>
                            <input type="text" name="kode_barang" x-model="edit_kode_barang" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Nama Barang</label>
                            <input type="text" name="nama_barang" x-model="edit_nama_barang" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Kategori</label>
                            <input type="text" name="kategori_nama" x-model="edit_kategori_nama" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Satuan</label>
                            <input type="text" name="satuan" x-model="edit_satuan" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" min="0" x-model="edit_harga_beli" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-slate mb-1">Harga Jual (Rp)</label>
                            <input type="number" name="harga_jual" min="0" x-model="edit_harga_jual" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-slate mb-1">Batas Stok Menipis</label>
                        <input type="number" name="stok_minimum" min="0" x-model="edit_stok_minimum" required class="w-full px-3 py-2 text-xs rounded-xl border border-surface-border bg-surface-canvas" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-slate mb-1">Ganti Foto Barang (Opsional)</label>
                        <input type="file" name="foto" accept="image/png,image/jpeg,image/webp" class="block w-full text-xs text-text-muted" />
                    </div>

                    <div class="pt-4 border-t border-surface-border flex items-center justify-end gap-3">
                        <x-ui.button type="button" variant="outline" size="sm" x-on:click="showEditModal = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm">Simpan Perubahan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div x-on:click.outside="showDeleteModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-4">
                <h3 class="text-base font-bold text-text-slate">Hapus Barang</h3>
                <p class="text-xs text-text-body leading-relaxed">
                    Hapus barang <strong x-text="deleteBarangNama"></strong> dari master item?
                </p>

                <form method="POST" x-bind:action="'{{ url('/' . $rolePrefix . '/master-item') }}/' + deleteBarangId" class="flex items-center justify-end gap-2.5 pt-3 border-t border-surface-border">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="showDeleteModal = false">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="danger" size="sm">Ya, hapus</x-ui.button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
