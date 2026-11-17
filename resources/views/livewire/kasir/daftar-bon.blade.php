<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Bon Kasir</h2>
            <a href="{{ route('kasir.catat-bon') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-semibold shadow">
                + Catat Bon Baru
            </a>
        </div>

        <!-- Alert Notifikasi -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter & Pencarian -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex space-x-2">
                <button wire:click="$set('filterStatus', 'semua')" class="px-4 py-2 rounded-md text-sm font-medium {{ $filterStatus === 'semua' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">Semua</button>
                <button wire:click="$set('filterStatus', 'belum_lunas')" class="px-4 py-2 rounded-md text-sm font-medium {{ $filterStatus === 'belum_lunas' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }}">Belum Lunas</button>
                <button wire:click="$set('filterStatus', 'lunas')" class="px-4 py-2 rounded-md text-sm font-medium {{ $filterStatus === 'lunas' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">Lunas</button>
            </div>

            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pelanggan / kode bon..." class="border rounded-md p-2 w-64 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Tabel Daftar Bon -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border">
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
                    @forelse($daftarBon as $index => $bon)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900">{{ $bon->kode_bon }}</span>
                                <div class="text-xs text-gray-500">Dibuat: {{ $bon->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-800">{{ $bon->penjualan->pelanggan->nama ?? 'Umum' }}</div>
                                <div class="text-xs text-gray-500">Jatuh Tempo: {{ $bon->jatuh_tempo }}</div>
                                @if(Carbon\Carbon::parse($bon->jatuh_tempo)->isPast() && $bon->status_pembayaran !== 'lunas')
                                    <span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded font-bold">Lewat Tempo</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($bon->penjualan->detailPenjualan->take(3) as $detail)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                                            {{ $detail->masterItem->nama_barang ?? 'Barang' }} ({{ $detail->jumlah }})
                                        </span>
                                    @endforeach
                                    @if($bon->penjualan->detailPenjualan->count() > 3)
                                        <span class="text-xs text-gray-500 self-center">+{{ $bon->penjualan->detailPenjualan->count() - 3 }} lagi</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">Rp {{ number_format($bon->total_nominal, 0, ',', '.') }}</div>
                                @if($bon->status_pembayaran !== 'lunas')
                                    <div class="text-xs text-red-600 font-semibold">Sisa: Rp {{ number_format($bon->total_nominal - $bon->total_terbayar, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($bon->status_pembayaran === 'lunas')
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-bold">✓ Lunas</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full font-bold">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center space-x-2">
                                <button wire:click="bukaModalKirim({{ $bon->id }})" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 shadow">Kirim</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data bon tersimpan di database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $daftarBon->links() }}
        </div>
    </div>
</div>