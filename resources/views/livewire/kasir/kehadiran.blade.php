<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Kehadiran Karyawan
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Catat kehadiran dan pekerjaan yang dilakukan setiap karyawan.
            </p>
        </div>

        <button
            wire:click="bukaModalTambah"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
        >
            + Tambah Kehadiran
        </button>
    </div>

    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau pekerjaan..."
                class="rounded-lg border-gray-300 text-sm"
            >

            <select
                wire:model.live="filterKaryawan"
                class="rounded-lg border-gray-300 text-sm"
            >
                <option value="">Semua karyawan</option>
                @foreach ($daftarKaryawan as $karyawan)
                    <option value="{{ $karyawan->id }}">
                        {{ $karyawan->nama }}
                    </option>
                @endforeach
            </select>

            <input
                type="date"
                wire:model.live="filterTanggal"
                class="rounded-lg border-gray-300 text-sm"
            >

        </div>
    </div>

    {{-- Tabel --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

        <div class="flex items-center justify-between border-b border-gray-200 p-4">
            <h2 class="font-semibold text-gray-900">
                Daftar Kehadiran
            </h2>

            <span class="text-sm text-gray-500">
                {{ $daftarAbsensi->total() }} catatan
            </span>
        </div>

        @if ($daftarAbsensi->isEmpty())

            <div class="p-10 text-center">
                <h3 class="font-semibold text-gray-700">
                    Belum ada catatan kehadiran
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan catatan kehadiran karyawan untuk mulai mencatat aktivitas.
                </p>
            </div>

        @else

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-4">No</th>
                            <th class="px-5 py-4">Nama Karyawan</th>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Pekerjaan yang Dilakukan</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @foreach ($daftarAbsensi as $index => $absensi)
                            <tr>
                                <td class="px-5 py-4">
                                    {{ $daftarAbsensi->firstItem() + $index }}
                                </td>

                                <td class="px-5 py-4 font-medium text-gray-900">
                                    {{ $absensi->karyawan->nama ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-gray-600">
                                    {{ $absensi->tanggal->format('d M Y') }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($absensi->detailPekerjaan->sortBy('urutan') as $detail)
                                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                                                {{ $detail->deskripsi }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            wire:click="bukaModalEdit({{ $absensi->id }})"
                                            class="rounded-lg px-3 py-1.5 text-blue-600 hover:bg-blue-50"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            wire:click="konfirmasiHapus({{ $absensi->id }})"
                                            class="rounded-lg px-3 py-1.5 text-red-600 hover:bg-red-50"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 p-4">
                {{ $daftarAbsensi->links() }}
            </div>

        @endif
    </div>

    {{-- Modal Tambah/Edit --}}
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl">

                <h2 class="text-lg font-bold text-gray-900">
                    {{ $editingId ? 'Edit Kehadiran' : 'Tambah Kehadiran' }}
                </h2>

                <form wire:submit="simpan" class="mt-5 space-y-4">

                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Nama Karyawan
                        </label>

                        <select wire:model="karyawan_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Pilih karyawan</option>
                            @foreach ($daftarKaryawan as $karyawan)
                                <option value="{{ $karyawan->id }}">
                                    {{ $karyawan->nama }}
                                </option>
                            @endforeach
                        </select>

                        @error('karyawan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Tanggal
                        </label>

                        <input type="date" wire:model="tanggal" class="w-full rounded-lg border-gray-300">

                        @error('tanggal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Shift
                        </label>

                        <select wire:model="shift" class="w-full rounded-lg border-gray-300">
                            <option value="">Tidak ditentukan</option>
                            <option value="pagi">Pagi</option>
                            <option value="siang">Siang</option>
                            <option value="full">Full</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Pekerjaan yang Dilakukan
                        </label>

                        @foreach ($pekerjaan as $index => $item)
                            <div class="mb-2 flex gap-2">
                                <input
                                    type="text"
                                    wire:model="pekerjaan.{{ $index }}"
                                    placeholder="Pekerjaan {{ $index + 1 }}"
                                    class="w-full rounded-lg border-gray-300"
                                >

                                @if (count($pekerjaan) > 1)
                                    <button
                                        type="button"
                                        wire:click="hapusPekerjaan({{ $index }})"
                                        class="rounded-lg px-3 text-red-600 hover:bg-red-50"
                                    >
                                        ×
                                    </button>
                                @endif
                            </div>

                            @error("pekerjaan.$index")
                                <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endforeach

                        <button
                            type="button"
                            wire:click="tambahPekerjaan"
                            class="text-sm font-medium text-blue-600 hover:text-blue-800"
                        >
                            + Tambah pekerjaan
                        </button>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-4">
                        <button
                            type="button"
                            wire:click="tutupModal"
                            class="rounded-lg border px-4 py-2 text-sm"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            <span wire:loading.remove wire:target="simpan">
                                {{ $editingId ? 'Simpan Perubahan' : 'Simpan' }}
                            </span>
                            <span wire:loading wire:target="simpan">
                                Menyimpan...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($modalHapusOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">

                <h2 class="text-lg font-bold text-gray-900">
                    Hapus Kehadiran?
                </h2>

                <p class="mt-3 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus catatan kehadiran ini beserta seluruh pekerjaan yang tercatat?
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="$set('modalHapusOpen', false)"
                        class="rounded-lg border px-4 py-2 text-sm"
                    >
                        Batal
                    </button>

                    <button
                        wire:click="hapus"
                        wire:loading.attr="disabled"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        Hapus
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>