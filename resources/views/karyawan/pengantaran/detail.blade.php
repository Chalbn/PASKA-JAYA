<x-layouts.karyawan title="Detail Pengantaran">
    <div class="space-y-6" x-data="{ adaFoto: false, previewUrl: null }">
        <div>
            <a
                href="{{ route('karyawan.pengantaran.index') }}"
                class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-primary hover:text-primary-deep transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Daftar Pengantaran</span>
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-surface-border">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-text-slate tracking-tight">
                    Detail Pengantaran — Bon {{ $pengiriman->bonPiutang?->pelanggan?->nama ?? $pengiriman->bonPiutang?->penjualan?->nama_pelanggan ?? 'Pelanggan' }}
                    · <span class="text-primary tabular-nums">{{ rupiah($pengiriman->bonPiutang?->total_tagihan ?? $pengiriman->bonPiutang?->penjualan?->total ?? 0) }}</span>
                </h2>
                <p class="text-xs text-text-muted mt-0.5">
                    Kode Bon: <strong class="font-bold text-text-slate">{{ $pengiriman->bonPiutang?->kode_bon ?? '-' }}</strong>
                </p>
            </div>

            <div class="shrink-0">
                <x-ui.badge :variant="match($pengiriman->status->value) {
                    'selesai' => 'selesai',
                    'siap_berangkat' => 'primary',
                    'sedang_disiapkan' => 'belum-lunas',
                    default => 'neutral'
                }">
                    {{ $pengiriman->status->label() }}
                </x-ui.badge>
            </div>
        </div>

        <x-ui.card title="Informasi Bon" padding="p-5 sm:p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                <div>
                    <dt class="text-text-muted font-medium text-[11px] uppercase tracking-wider">Pelanggan</dt>
                    <dd class="mt-1 font-bold text-text-slate">
                        {{ $pengiriman->bonPiutang?->pelanggan?->nama ?? $pengiriman->bonPiutang?->penjualan?->nama_pelanggan ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-text-muted font-medium text-[11px] uppercase tracking-wider">Jadwal Pengantaran</dt>
                    <dd class="mt-1 font-semibold text-text-slate">
                        {{ $pengiriman->jadwal_kirim ? $pengiriman->jadwal_kirim->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : 'Segera / Fleksibel' }}
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-text-muted font-medium text-[11px] uppercase tracking-wider">Alamat Pengantaran</dt>
                    <dd class="mt-1 font-medium text-text-slate leading-relaxed">
                        {{ $pengiriman->bonPiutang?->alamat_pengantaran ?? 'Alamat toko / Ambil di tempat' }}
                    </dd>
                </div>

                @if($pengiriman->catatan_kasir)
                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/80 text-secondary-dark">
                        <dt class="text-[11px] font-bold uppercase tracking-wider mb-1">Catatan Kasir</dt>
                        <dd class="leading-relaxed text-xs">{{ $pengiriman->catatan_kasir }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 pt-5 border-t border-surface-border">
                <h4 class="text-xs font-bold text-text-muted uppercase tracking-wider mb-3">RINCIAN BARANG PENGANTARAN</h4>
                <div class="overflow-x-auto rounded-xl border border-surface-border">
                    <table class="w-full text-left text-xs sm:text-sm divide-y divide-surface-border">
                        <thead class="bg-surface-container-low font-bold text-text-muted uppercase tracking-wider text-[10px] sm:text-xs">
                            <tr>
                                <th class="px-4 py-3">BARANG</th>
                                <th class="px-4 py-3 text-right">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border bg-surface-card">
                            @forelse($pengiriman->detailPengiriman as $detail)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-text-slate">
                                        {{ $detail->nama_barang }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-text-on-surface tabular-nums">
                                        {{ $detail->jumlah }} {{ $detail->satuan }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-center text-text-muted italic">
                                        Tidak ada rincian barang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Status Pengantaran" padding="p-5 sm:p-6">
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    @foreach($stepper as $index => $step)
                        <div class="flex flex-col items-center flex-1 relative z-10">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full flex items-center justify-center font-black text-xs sm:text-sm transition shadow-xs {{ $step['selesai'] ? 'bg-[#1E40AF] text-white ring-4 ring-blue-100' : 'bg-surface-container-low text-text-muted border-2 border-surface-border' }}">
                                @if($step['selesai'])
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <span>{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <span class="mt-2 text-[10px] sm:text-xs font-bold text-center leading-tight {{ $step['aktif'] ? 'text-primary' : ($step['selesai'] ? 'text-text-slate' : 'text-text-muted') }}">
                                {{ $step['label'] }}
                            </span>
                        </div>

                        @if(!$loop->last)
                            <div class="flex-1 h-1 mx-[-15px] -mt-5 relative z-0 {{ $stepper[$index + 1]['selesai'] ? 'bg-[#1E40AF]' : 'bg-surface-border' }}"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('karyawan.pengantaran.status', $pengiriman->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="p-4 sm:p-5 rounded-2xl bg-surface-canvas/50 border border-surface-border space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-text-slate uppercase tracking-wider">
                                Foto bukti selesai diantar (wajib)
                            </h4>
                            <p class="text-[11px] text-text-muted mt-0.5">
                                Wajib menyertakan foto lokasi/barang sampai di tujuan sebelum menandai Selesai.
                            </p>
                        </div>

                        @if($pengiriman->foto_bukti)
                            <x-ui.badge variant="lunas">Foto Tersimpan</x-ui.badge>
                        @endif
                    </div>

                    @if($pengiriman->foto_bukti)
                        <div class="relative rounded-2xl overflow-hidden border border-surface-border max-w-sm mx-auto shadow-xs">
                            <img
                                src="{{ route('berkas.bukti-pengiriman', $pengiriman->id) }}"
                                alt="Bukti Pengiriman Selesai"
                                class="w-full h-auto object-cover max-h-72"
                            />
                            <div class="p-2.5 bg-surface-card/90 backdrop-blur-xs text-center border-t border-surface-border">
                                <span class="text-xs font-semibold text-text-muted">
                                    Selesai pada: {{ $pengiriman->waktu_selesai?->timezone('Asia/Jakarta')->format('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>
                        </div>
                    @elseif($perluFoto)
                        <div class="space-y-3">
                            <template x-if="previewUrl">
                                <div class="relative inline-block max-w-xs mx-auto overflow-hidden rounded-xl border border-surface-border shadow-xs">
                                    <img x-bind:src="previewUrl" alt="Pratinjau Foto Bukti" class="max-h-48 w-auto object-contain mx-auto" />
                                </div>
                            </template>

                            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 pt-1">
                                <label class="cursor-pointer w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary text-primary-on text-xs font-bold hover:bg-primary-deep transition shadow-xs">
                                    <span>Pilih / ambil foto</span>
                                    <input
                                        type="file"
                                        name="foto_bukti"
                                        accept="image/jpeg,image/png,image/webp"
                                        capture="environment"
                                        class="hidden"
                                        x-on:change="
                                            const f = $event.target.files[0];
                                            adaFoto = !!f;
                                            previewUrl = f ? URL.createObjectURL(f) : null;
                                        "
                                    />
                                </label>
                            </div>

                            @error('foto_bukti')
                                <p class="text-xs text-status-danger font-semibold text-center mt-1.5">{{ $message }}</p>
                            @enderror
                            @error('fotoBukti')
                                <p class="text-xs text-status-danger font-semibold text-center mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <div class="border-2 border-dashed border-surface-border rounded-xl p-5 text-center bg-surface-card/40">
                            <p class="text-xs text-text-muted">
                                Foto bukti akan diminta saat pengantaran siap diselesaikan.
                            </p>
                        </div>
                    @endif
                </div>

                @if($statusBerikutnya)
                    <div class="mt-6 pt-5 border-t border-surface-border">
                        <input type="hidden" name="status_baru" value="{{ $statusBerikutnya->value }}" />
                        <button
                            type="submit"
                            @if($perluFoto) x-bind:disabled="!adaFoto" @endif
                            class="w-full py-4 px-6 rounded-2xl bg-primary text-primary-on font-black text-sm sm:text-base tracking-wide uppercase transition hover:bg-primary-deep active:opacity-95 shadow-md flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed select-none cursor-pointer"
                        >
                            <span>{{ $labelTombol }}</span>
                        </button>
                    </div>
                @else
                    <div class="mt-6 pt-5 border-t border-surface-border text-center">
                        <p class="text-xs sm:text-sm font-bold text-status-success inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Pengantaran ini telah selesai dan tidak dapat diubah lagi.</span>
                        </p>
                    </div>
                @endif
            </form>
        </x-ui.card>
    </div>
</x-layouts.karyawan>
