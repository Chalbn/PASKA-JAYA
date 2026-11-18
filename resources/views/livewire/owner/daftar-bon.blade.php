<div class="space-y-6">
    {{-- 1. HEADER HALAMAN & BANNER INFORMASI --}}
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                    Daftar Bon
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Pantau pembayaran dan status pengantaran setiap bon.
                </p>
            </div>

            {{-- Badge Kanan Atas: Hanya Lihat --}}
            <div class="shrink-0">
                <span class="bg-slate-100 border border-slate-200 text-slate-600 text-xs font-semibold rounded-full px-3.5 py-1.5 inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Hanya Lihat</span>
                </span>
            </div>
        </div>

        {{-- 2 Banner Pemberitahuan Sejajar --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
            <div class="bg-blue-50/70 border border-blue-200/70 rounded-xl px-4 py-2.5 text-xs text-slate-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#1E3F99] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Mode baca saja: pembayaran dan pengiriman bon hanya dapat dilakukan oleh Kasir.</span>
            </div>

            <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-xs text-slate-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                </svg>
                <span>Perubahan status dari Karyawan otomatis tampil di kolom Status Pengantaran.</span>
            </div>
        </div>
    </div>

    {{-- 2. KARTU UTAMA TABEL BON & TAB FILTER STATUS --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
        {{-- Tab Filter di Pojok Kanan Atas Tabel --}}
        <div class="flex justify-end gap-2 mb-4">
            <button
                type="button"
                wire:click="setFilter('semua')"
                class="rounded-xl px-4 py-2 text-xs transition cursor-pointer select-none {{ $filterStatus === 'semua' ? 'bg-[#1E3F99] text-white font-semibold' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 font-medium' }}"
            >
                Semua
            </button>
            <button
                type="button"
                wire:click="setFilter('belum_lunas')"
                class="rounded-xl px-4 py-2 text-xs transition cursor-pointer select-none {{ $filterStatus === 'belum_lunas' ? 'bg-[#1E3F99] text-white font-semibold' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 font-medium' }}"
            >
                Belum lunas
            </button>
            <button
                type="button"
                wire:click="setFilter('lunas')"
                class="rounded-xl px-4 py-2 text-xs transition cursor-pointer select-none {{ $filterStatus === 'lunas' ? 'bg-[#1E3F99] text-white font-semibold' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 font-medium' }}"
            >
                Lunas
            </button>
        </div>

        {{-- Struktur Tabel --}}
        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-left text-xs sm:text-sm border-collapse">
                <thead class="bg-slate-50/90 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3.5 w-14">NO</th>
                        <th class="px-4 py-3.5">PELANGGAN</th>
                        <th class="px-4 py-3.5">RINGKASAN BARANG</th>
                        <th class="px-4 py-3.5 text-right">TOTAL</th>
                        <th class="px-4 py-3.5 text-center">STATUS PEMBAYARAN</th>
                        <th class="px-4 py-3.5">STATUS PENGANTARAN</th>
                        <th class="px-4 py-3.5 text-right">DETAIL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($daftarBon as $bon)
                        @php
                            $nomorUrut = ($daftarBon->firstItem() ?? 1) + $loop->index;
                            $items = $itemsByPenjualanId->get($bon->penjualan_id, collect());
                            $isLunas = $bon->status_bayar === 'lunas';
                            $lewatTempo = (bool) $bon->lewat_tempo;
                            $statusKirim = (string) ($bon->status_pengiriman ?? 'belum_dikirim');

                            $badgeKirimClass = match ($statusKirim) {
                                'selesai' => 'bg-[#1E3F99] text-white',
                                'dikirim_ke_karyawan', 'sedang_disiapkan', 'siap_berangkat' => 'bg-blue-100 text-blue-800',
                                default => 'bg-slate-100 text-slate-600',
                            };

                            $labelKirim = match ($statusKirim) {
                                'selesai' => '✓ Selesai',
                                'siap_berangkat' => 'Siap Berangkat/Sedang Diantar',
                                'sedang_disiapkan' => 'Sedang Disiapkan',
                                'dikirim_ke_karyawan' => 'Dikirim ke Karyawan',
                                default => 'Belum Dikirim',
                            };
                        @endphp
                        <tr wire:key="owner-bon-{{ $bon->bon_id }}" class="hover:bg-slate-50/60 transition align-top">
                            {{-- NO --}}
                            <td class="px-4 py-4 font-semibold text-slate-500 tabular-nums">
                                {{ $nomorUrut }}
                            </td>

                            {{-- PELANGGAN --}}
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-900 leading-snug">
                                    {{ $bon->nama_pelanggan }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Jatuh tempo {{ tanggal_id($bon->jatuh_tempo) }}
                                </p>
                                @if($lewatTempo)
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full px-2.5 py-0.5 mt-1 inline-block">
                                        Lewat tempo
                                    </span>
                                @endif
                            </td>

                            {{-- RINGKASAN BARANG --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @foreach($items->take(3) as $item)
                                        <span class="bg-blue-50 border border-blue-200/70 text-[#1D3FA3] text-xs font-medium rounded-full px-3 py-1 inline-block">
                                            {{ $item->nama_barang }} ×{{ $item->jumlah }}
                                        </span>
                                    @endforeach
                                    @if($items->count() > 3)
                                        <span class="bg-slate-100 text-slate-600 text-xs font-medium rounded-full px-2.5 py-1 inline-block">
                                            +{{ $items->count() - 3 }}
                                        </span>
                                    @endif
                                    @if($items->isEmpty())
                                        <span class="text-xs text-slate-400">{{ $bon->ringkasan_barang ?? '-' }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- TOTAL --}}
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <p class="font-bold text-slate-900 tabular-nums">
                                    {{ rupiah($bon->total_tagihan) }}
                                </p>
                                @if(! $isLunas)
                                    <p class="text-[11px] text-slate-500 tabular-nums mt-0.5">
                                        Sisa {{ rupiah($bon->sisa) }}
                                    </p>
                                @endif
                            </td>

                            {{-- STATUS PEMBAYARAN --}}
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($isLunas)
                                    <span class="inline-flex items-center bg-[#1E3F99] text-white text-xs font-semibold rounded-full px-3 py-1">
                                        ✓ Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-amber-100 text-amber-800 text-xs font-semibold rounded-full px-3 py-1">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>

                            {{-- STATUS PENGANTARAN --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center text-xs font-semibold rounded-full px-3 py-1 whitespace-nowrap {{ $badgeKirimClass }}">
                                    {{ $labelKirim }}
                                </span>
                                @if(! empty($bon->nama_karyawan))
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        {{ $bon->nama_karyawan }}
                                    </p>
                                @endif
                            </td>

                            {{-- DETAIL --}}
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <button
                                    type="button"
                                    wire:click="bukaDetail({{ $bon->bon_id }})"
                                    class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl px-3.5 py-2 inline-flex items-center gap-1.5 transition cursor-pointer select-none"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Lihat Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Belum ada data bon</p>
                                <p class="text-xs text-slate-400 mt-1">
                                    Data bon piutang pelanggan yang dicatat oleh Kasir akan tampil di sini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftarBon->hasPages())
            <div class="mt-4">
                {{ $daftarBon->links() }}
            </div>
        @endif
    </div>

    {{-- 3. MODAL POP-UP "DETAIL BON" --}}
    @if($modalDetailOpen && $detailBon)
        @php
            $bonInfo = $detailBon['bon'];
            $detailItems = $detailBon['items'];
            $timeline = $detailBon['timeline'];
            $statusKirimModal = (string) ($bonInfo->status_pengiriman ?? 'belum_dikirim');
            $badgeModalClass = match ($statusKirimModal) {
                'selesai' => 'bg-[#1E3F99] text-white',
                'dikirim_ke_karyawan', 'sedang_disiapkan', 'siap_berangkat' => 'bg-blue-100 text-blue-800',
                default => 'bg-slate-100 text-slate-600',
            };
            $labelModalKirim = match ($statusKirimModal) {
                'selesai' => '✓ Selesai',
                'siap_berangkat' => 'Siap Berangkat/Sedang Diantar',
                'sedang_disiapkan' => 'Sedang Disiapkan',
                'dikirim_ke_karyawan' => 'Dikirim ke Karyawan',
                default => 'Belum Dikirim',
            };
            $jadwalStr = ! empty($bonInfo->jadwal_kirim)
                ? tanggal_id($bonInfo->jadwal_kirim).', '.\Carbon\Carbon::parse($bonInfo->jadwal_kirim)->timezone('Asia/Jakarta')->format('H.i')
                : '-';
        @endphp

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 my-8">
                {{-- Header Modal --}}
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Detail Bon</h3>
                    <button
                        type="button"
                        wire:click="tutupDetail"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                        aria-label="Tutup modal"
                    >
                        <span class="text-xl leading-none">×</span>
                    </button>
                </div>

                {{-- Bagian Atas Modal: Nama Pelanggan, Alamat & Status Pengantaran --}}
                <div class="mt-4 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div>
                        <p class="text-base font-bold text-slate-900">{{ $bonInfo->nama_pelanggan }}</p>
                        <p class="mt-1 text-xs text-slate-500 flex items-start gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $bonInfo->alamat_pengantaran ?: 'Alamat tidak dicantumkan' }}</span>
                        </p>
                    </div>

                    <span class="inline-flex items-center text-xs font-semibold rounded-full px-3 py-1 whitespace-nowrap shrink-0 {{ $badgeModalClass }}">
                        {{ $labelModalKirim }}
                    </span>
                </div>

                {{-- Layout 2 Kolom di Dalam Modal --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mt-5 pt-4 border-t border-slate-100">
                    {{-- Kolom Kiri (md:col-span-7): Rincian Barang, Total & Bukti --}}
                    <div class="md:col-span-7 space-y-4">
                        <div class="rounded-xl border border-slate-200/80 overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                                    <tr>
                                        <th class="px-3.5 py-2.5">BARANG</th>
                                        <th class="px-3.5 py-2.5 text-center">JML</th>
                                        <th class="px-3.5 py-2.5 text-right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($detailItems as $item)
                                        <tr>
                                            <td class="px-3.5 py-2.5 font-medium text-slate-800">
                                                {{ $item->nama_barang }}
                                            </td>
                                            <td class="px-3.5 py-2.5 text-center text-slate-600 tabular-nums whitespace-nowrap">
                                                {{ $item->jumlah }} {{ $item->satuan }}
                                            </td>
                                            <td class="px-3.5 py-2.5 text-right font-semibold text-slate-900 tabular-nums whitespace-nowrap">
                                                {{ rupiah($item->subtotal) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Ringkasan Biaya --}}
                        <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200/70 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-700">Total bon</span>
                                <span class="font-bold text-slate-900 tabular-nums">{{ rupiah($bonInfo->total_tagihan) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-700">Terbayar</span>
                                <span class="font-bold text-slate-900 tabular-nums">{{ rupiah($bonInfo->total_terbayar) }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/80">
                                <span class="font-bold text-slate-800">Sisa</span>
                                <span class="font-bold text-[#1D3FA3] tabular-nums">{{ rupiah($bonInfo->sisa) }}</span>
                            </div>
                        </div>

                        {{-- Info Pengiriman --}}
                        <div class="text-xs text-slate-500 space-y-1">
                            <p>Karyawan: <span class="text-slate-700 font-medium">{{ $bonInfo->nama_karyawan ?: '-' }}</span></p>
                            <p>Jadwal: <span class="text-slate-700 font-medium">{{ $jadwalStr }}</span></p>
                            <p>Catatan: <span class="text-slate-700 font-medium">{{ $bonInfo->catatan_kasir ?: '-' }}</span></p>
                        </div>

                        {{-- Kondisional Foto Bukti Selesai Diantar --}}
                        @if($statusKirimModal === 'selesai' && ! empty($bonInfo->foto_bukti) && ! empty($bonInfo->pengiriman_id))
                            <div class="pt-2">
                                <p class="text-xs font-bold text-slate-700 mb-2">Foto bukti selesai diantar</p>
                                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                    <img
                                        src="{{ route('berkas.bukti-pengiriman', $bonInfo->pengiriman_id) }}"
                                        alt="Foto bukti pengiriman {{ $bonInfo->kode_bon }}"
                                        class="w-full h-44 object-cover"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Kolom Kanan (md:col-span-5): Timeline ALUR STATUS --}}
                    <div class="md:col-span-5">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-4">
                            ALUR STATUS
                        </h4>

                        <div class="space-y-0">
                            @foreach($timeline as $step)
                                @php
                                    $isLast = $loop->last;
                                    $isCompleted = $step['status'] === 'completed';
                                @endphp
                                <div class="relative flex items-start gap-3 {{ $isLast ? '' : 'pb-5' }}">
                                    @if(! $isLast)
                                        <span class="absolute left-3 top-6 -ml-px h-full w-0.5 {{ $isCompleted ? 'bg-[#1E3F99]' : 'bg-slate-200' }}" aria-hidden="true"></span>
                                    @endif

                                    <div class="relative z-10 shrink-0">
                                        @if($isCompleted)
                                            <span class="w-6 h-6 rounded-full bg-[#1E3F99] text-white flex items-center justify-center shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center">
                                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="min-w-0 pt-0.5">
                                        <p class="text-xs font-bold leading-snug {{ $isCompleted ? 'text-slate-900' : 'text-slate-400 font-medium' }}">
                                            {{ $step['title'] }}
                                        </p>
                                        @if(! empty($step['date']))
                                            <p class="text-[11px] text-slate-400 tabular-nums mt-0.5">
                                                {{ $step['date'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
