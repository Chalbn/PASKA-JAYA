<x-layouts.app title="Kehadiran" role="kasir">
    <div
        class="space-y-6"
        x-data="{
            modalOpen: false,
            modalHapusOpen: false,
            editingId: null,
            hapusId: null,
            karyawan_id: '',
            tanggal: '{{ now()->toDateString() }}',
            shift: '',
            pekerjaan: [''],
            bukaModalTambah() {
                this.editingId = null;
                this.karyawan_id = '';
                this.tanggal = '{{ now()->toDateString() }}';
                this.shift = '';
                this.pekerjaan = [''];
                this.modalOpen = true;
            },
            bukaModalEdit(item) {
                this.editingId = item.id;
                this.karyawan_id = item.karyawan_id;
                this.tanggal = item.tanggal;
                this.shift = item.shift || '';
                this.pekerjaan = item.pekerjaan && item.pekerjaan.length ? [...item.pekerjaan] : [''];
                this.modalOpen = true;
            },
            tambahPekerjaan() {
                this.pekerjaan.push('');
            },
            hapusPekerjaan(idx) {
                if (this.pekerjaan.length > 1) {
                    this.pekerjaan.splice(idx, 1);
                }
            },
            konfirmasiHapus(id) {
                this.hapusId = id;
                this.modalHapusOpen = true;
            }
        }"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kehadiran Karyawan</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Catat kehadiran dan pekerjaan yang dilakukan setiap karyawan.
                </p>
            </div>

            <button
                type="button"
                x-on:click="bukaModalTambah()"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 cursor-pointer"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kehadiran</span>
            </button>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('kasir.kehadiran') }}" class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama atau pekerjaan..."
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm"
                />

                @php
                    $filterKaryawanOptions = ['' => 'Semua karyawan'];
                    foreach ($daftarKaryawan as $karyawan) {
                        $filterKaryawanOptions[(string) $karyawan->id] = $karyawan->nama;
                    }
                @endphp
                <x-ui.select
                    name="filterKaryawan"
                    :compact="true"
                    :auto-submit="true"
                    placeholder="Semua karyawan"
                    :value="(string) ($filterKaryawan ?? '')"
                    :options="$filterKaryawanOptions"
                />

                <input
                    type="date"
                    name="filterTanggal"
                    value="{{ $filterTanggal ?? '' }}"
                    onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm"
                />
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 p-4">
                <h2 class="font-semibold text-gray-900">Daftar Kehadiran</h2>
                <span class="text-sm text-gray-500">{{ $daftarAbsensi->total() }} catatan</span>
            </div>

            @if ($daftarAbsensi->isEmpty())
                <div class="p-10 text-center">
                    <h3 class="font-semibold text-gray-700">Belum ada catatan kehadiran</h3>
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
                                    <td class="px-5 py-4">{{ $daftarAbsensi->firstItem() + $index }}</td>
                                    <td class="px-5 py-4 font-medium text-gray-900">{{ $absensi->karyawan->nama ?? '-' }}</td>
                                    <td class="px-5 py-4 text-gray-600">{{ $absensi->tanggal->format('d M Y') }}</td>
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
                                                type="button"
                                                x-on:click="bukaModalEdit(@js([
                                                    'id' => $absensi->id,
                                                    'karyawan_id' => $absensi->karyawan_id,
                                                    'tanggal' => $absensi->tanggal->format('Y-m-d'),
                                                    'shift' => $absensi->shift ?? '',
                                                    'pekerjaan' => $absensi->detailPekerjaan->sortBy('urutan')->pluck('deskripsi')->values()->all(),
                                                ]))"
                                                class="rounded-lg px-3 py-1.5 text-blue-600 hover:bg-blue-50 cursor-pointer"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="konfirmasiHapus({{ $absensi->id }})"
                                                class="rounded-lg px-3 py-1.5 text-red-600 hover:bg-red-50 cursor-pointer"
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
        <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div x-on:click.outside="modalOpen = false" class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-900" x-text="editingId ? 'Edit Kehadiran' : 'Tambah Kehadiran'"></h2>

                <form method="POST" x-bind:action="editingId ? '{{ url('/kasir/kehadiran') }}/' + editingId : '{{ route('kasir.kehadiran.store') }}'" class="mt-5 space-y-4">
                    @csrf
                    <template x-if="editingId">
                        <input type="hidden" name="_method" value="PUT" />
                    </template>

                    <div>
                        @php
                            $modalKaryawanOptions = collect($daftarKaryawan)->mapWithKeys(fn ($karyawan) => [
                                (string) $karyawan->id => $karyawan->nama,
                            ])->all();
                        @endphp
                        <x-ui.select
                            name="karyawan_id"
                            label="Nama Karyawan"
                            placeholder="Pilih karyawan"
                            :required="true"
                            x-model="karyawan_id"
                            :options="$modalKaryawanOptions"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Tanggal</label>
                        <input type="date" name="tanggal" x-model="tanggal" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                    </div>

                    <div>
                        <x-ui.select
                            name="shift"
                            label="Shift"
                            placeholder="Tidak ditentukan"
                            :searchable="false"
                            x-model="shift"
                            :options="[
                                '' => 'Tidak ditentukan',
                                'pagi' => 'Pagi',
                                'siang' => 'Siang',
                                'full' => 'Full',
                            ]"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">Pekerjaan yang Dilakukan</label>
                        <template x-for="(pek, idx) in pekerjaan" :key="idx">
                            <div class="mb-2 flex gap-2">
                                <input
                                    type="text"
                                    name="pekerjaan[]"
                                    x-model="pekerjaan[idx]"
                                    x-bind:placeholder="'Pekerjaan ' + (idx + 1)"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                />
                                <template x-if="pekerjaan.length > 1">
                                    <button type="button" x-on:click="hapusPekerjaan(idx)" class="rounded-lg px-3 text-red-600 hover:bg-red-50 cursor-pointer">×</button>
                                </template>
                            </div>
                        </template>

                        <button type="button" x-on:click="tambahPekerjaan()" class="text-sm font-medium text-blue-600 hover:text-blue-800 cursor-pointer">
                            + Tambah pekerjaan
                        </button>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-4">
                        <button type="button" x-on:click="modalOpen = false" class="rounded-lg border px-4 py-2 text-sm cursor-pointer">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 cursor-pointer">
                            <span x-text="editingId ? 'Simpan Perubahan' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div x-show="modalHapusOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div x-on:click.outside="modalHapusOpen = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-900">Hapus Kehadiran?</h2>
                <p class="mt-3 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus catatan kehadiran ini beserta seluruh pekerjaan yang tercatat?
                </p>

                <form method="POST" x-bind:action="'{{ url('/kasir/kehadiran') }}/' + hapusId" class="mt-6 flex justify-end gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" x-on:click="modalHapusOpen = false" class="rounded-lg border px-4 py-2 text-sm cursor-pointer">Batal</button>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 cursor-pointer">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
