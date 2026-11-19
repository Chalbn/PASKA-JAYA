<x-layouts.app title="Kehadiran" role="owner">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Kehadiran Karyawan</h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">Pantau catatan kehadiran dan pekerjaan harian karyawan.</p>
            </div>
        </div>

        <x-ui.card padding="p-0">
            @if(empty($kehadiran) || count($kehadiran) === 0)
                <div class="p-8">
                    <x-ui.empty-state title="Belum ada catatan kehadiran" description="Catatan kehadiran karyawan yang diinput kasir akan tampil di sini." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-surface-canvas border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                            <tr>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Karyawan</th>
                                <th class="py-3 px-4">Shift</th>
                                <th class="py-3 px-4">Pekerjaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @foreach($kehadiran as $row)
                                <tr>
                                    <td class="py-3 px-4 text-text-muted whitespace-nowrap">{{ tanggal_id($row->tanggal) }}</td>
                                    <td class="py-3 px-4 font-semibold text-text-slate">{{ $row->nama_karyawan ?? $row->karyawan?->nama ?? '-' }}</td>
                                    <td class="py-3 px-4 capitalize">{{ $row->shift ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $row->daftar_pekerjaan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
