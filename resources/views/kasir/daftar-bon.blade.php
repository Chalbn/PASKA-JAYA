<x-layouts.app title="Daftar Bon" role="kasir">
    <div
        class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
        x-data="{
            modalKirimOpen: false,
            modalBayarOpen: false,
            selectedBonId: null,
            bukaModalKirim(id) {
                this.selectedBonId = id;
                this.modalKirimOpen = true;
            },
            bukaModalBayar(id) {
                this.selectedBonId = id;
                this.modalBayarOpen = true;
            }
        }"
    >
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-surface-border">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Bon Kasir</h2>
                <a href="{{ route('kasir.catat-bon') }}" class="bg-primary text-white px-4 py-2 rounded-xl hover:bg-primary-deep font-semibold shadow-xs text-sm">
                    + Catat Bon Baru
                </a>
            </div>

            <form method="GET" action="{{ route('kasir.daftar-bon') }}" class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex space-x-2">
                    <a href="{{ route('kasir.daftar-bon', ['filterStatus' => 'semua', 'search' => $search ?? '']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold {{ ($filterStatus ?? 'semua') === 'semua' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700' }}">Semua</a>
                    <a href="{{ route('kasir.daftar-bon', ['filterStatus' => 'belum_lunas', 'search' => $search ?? '']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold {{ ($filterStatus ?? '') === 'belum_lunas' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700' }}">Belum Lunas</a>
                    <a href="{{ route('kasir.daftar-bon', ['filterStatus' => 'lunas', 'search' => $search ?? '']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold {{ ($filterStatus ?? '') === 'lunas' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700' }}">Lunas</a>
                </div>

                <div>
                    <input type="hidden" name="filterStatus" value="{{ $filterStatus ?? 'semua' }}" />
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari pelanggan / kode bon..." class="border border-surface-border rounded-xl p-2 w-64 text-sm shadow-xs" />
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-surface-border rounded-xl overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No / Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan & Jatuh Tempo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ringkasan Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total / Sisa</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status Pembayaran</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse($daftarBon as $bon)
                            @php
                                $statusVal = $bon->status_bayar instanceof \BackedEnum ? $bon->status_bayar->value : (string) ($bon->status_bayar ?? $bon->status_pembayaran ?? 'belum_lunas');
                                $totalTagihan = (int) ($bon->total_tagihan ?? $bon->total_nominal ?? 0);
                                $sisaTagihan = $totalTagihan - (int) $bon->total_terbayar;
                            @endphp
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-bold text-gray-900">{{ $bon->kode_bon }}</span>
                                    <div class="text-xs text-gray-500">Dibuat: {{ $bon->created_at ? $bon->created_at->format('d/m/Y') : '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-800">{{ $bon->pelanggan?->nama ?? $bon->penjualan?->pelanggan?->nama ?? $bon->penjualan?->nama_pelanggan ?? 'Umum' }}</div>
                                    <div class="text-xs text-gray-500">Jatuh Tempo: {{ $bon->jatuh_tempo ? \Carbon\Carbon::parse($bon->jatuh_tempo)->format('d/m/Y') : '-' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(($bon->penjualan?->detailPenjualan ?? collect())->take(3) as $detail)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                                                {{ $detail->nama_barang ?? $detail->masterItem?->nama_barang ?? 'Barang' }} ({{ $detail->jumlah }})
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900">{{ rupiah($totalTagihan) }}</div>
                                    @if($statusVal !== 'lunas')
                                        <div class="text-xs text-red-600 font-semibold">Sisa: {{ rupiah($sisaTagihan) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    @if($statusVal === 'lunas')
                                        <span class="px-2.5 py-1 text-xs bg-emerald-100 text-emerald-800 rounded-full font-bold">✓ Lunas</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs bg-amber-100 text-amber-800 rounded-full font-bold">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center space-x-1.5">
                                    <button type="button" x-on:click="bukaModalKirim({{ $bon->id }})" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-blue-700 shadow-xs cursor-pointer">Kirim</button>
                                    @if($statusVal !== 'lunas')
                                        <button type="button" x-on:click="bukaModalBayar({{ $bon->id }})" class="bg-emerald-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-emerald-700 shadow-xs cursor-pointer">Bayar</button>
                                    @endif
                                    <a
                                        href="{{ route('kasir.bon.faktur', $bon->id) }}"
                                        target="_blank"
                                        class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg text-xs hover:bg-slate-200 font-semibold shadow-xs"
                                    >
                                        Faktur PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data bon tersimpan di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($daftarBon, 'links'))
                <div class="mt-4">
                    {{ $daftarBon->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Kirim ke Karyawan --}}
        <div x-show="modalKirimOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div x-on:click.outside="modalKirimOpen = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900">Kirim Bon ke Karyawan Pengantar</h3>
                <form method="POST" x-bind:action="'{{ url('/kasir/bon') }}/' + selectedBonId + '/kirim'" class="space-y-4">
                    @csrf
                    <div>
                        @php
                            $karyawanOptions = collect($daftarKaryawan ?? [])->mapWithKeys(fn ($k) => [
                                (string) $k->id => $k->nama,
                            ])->all();
                        @endphp
                        <x-ui.select
                            name="karyawan_id"
                            label="Pilih Karyawan"
                            placeholder="-- Pilih Karyawan --"
                            :required="true"
                            :options="$karyawanOptions"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jadwal Kirim (Opsional)</label>
                        <input type="datetime-local" name="jadwal_kirim" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Kasir</label>
                        <textarea name="catatan" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <x-ui.button type="button" variant="outline" size="sm" x-on:click="modalKirimOpen = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm">Kirim Sekarang</x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Bayar Bon --}}
        <div x-show="modalBayarOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div x-on:click.outside="modalBayarOpen = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900">Catat Pembayaran Bon</h3>
                <form method="POST" x-bind:action="'{{ url('/kasir/bon') }}/' + selectedBonId + '/bayar'" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Bayar (Rp) — kosongkan jika lunas penuh</label>
                        <input type="number" name="jumlah" min="1" placeholder="Kosongkan untuk lunas penuh" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <x-ui.select
                            name="metode"
                            label="Metode Pembayaran"
                            :required="true"
                            :searchable="false"
                            value="cash"
                            :options="[
                                'cash' => 'Cash (Tunai)',
                                'transfer' => 'Transfer Bank',
                            ]"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan</label>
                        <input type="text" name="catatan" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <x-ui.button type="button" variant="outline" size="sm" x-on:click="modalBayarOpen = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm">Simpan Pembayaran</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>