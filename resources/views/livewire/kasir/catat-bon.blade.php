<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Catat Bon & Piutang Baru</h2>

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="simpanBon">
                <!-- Informasi Pelanggan -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                        <input type="text" wire:model="nama_pelanggan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2" required>
                        @error('nama_pelanggan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kontak / No. HP</label>
                        <input type="text" wire:model="kontak" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <input type="text" wire:model="alamat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Bon</label>
                        <input type="date" wire:model="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                        <input type="date" wire:model="jatuh_tempo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2" required>
                    </div>
                </div>

                <hr class="my-6">

                <!-- Bagian Tambah Item Barang -->
                <h3 class="text-lg font-semibold mb-3 text-gray-700">Daftar Barang</h3>
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-end bg-gray-50 p-4 rounded-lg">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Pilih Barang</label>
                        <select wire:model.live="selected_item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                            <option value="">-- Pilih Barang Toko --</option>
                            @foreach($masterItems as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_barang ?? $item->nama }} (Stok: {{ $item->stok }}) - Rp {{ number_format($item->harga_jual ?? 0, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" wire:model="jumlah" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                    </div>
                    <div>
                        <button type="button" wire:click="tambahItem" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Tambah Item</button>
                    </div>
                </div>

                <!-- Tabel Keranjang Item -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($items as $index => $cartItem)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $cartItem['nama_barang'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-center">{{ $cartItem['jumlah'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($cartItem['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($cartItem['subtotal'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-center">
                                        <button type="button" wire:click="hapusItem({{ $index }})" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500 text-sm">Belum ada barang yang ditambahkan ke keranjang bon.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @error('items') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tombol Simpan -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition font-semibold shadow">Simpan Bon & Piutang</button>
                </div>
            </form>
        </div>
    </div>
</div>