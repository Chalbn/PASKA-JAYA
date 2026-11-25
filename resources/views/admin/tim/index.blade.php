<x-layouts.app title="Tim" role="admin">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Tim Paska Jaya</h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">Anggota tim pengembang dan pengelola sistem Toko Bahan Bangunan PASKA JAYA.</p>
            </div>
            <span class="inline-flex items-center self-start sm:self-auto px-3 py-1 rounded-full border border-primary/20 text-xs font-bold text-primary shadow-2xs" style="background-color: rgba(255, 255, 255, 0.5);">
                {{ count($daftarKartu) }} Anggota Aktif
            </span>
        </div>

        {{-- 5 Kartu Anggota Tim --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5">
            @foreach ($daftarKartu as $kartu)
                <div
                    class="group relative rounded-2xl backdrop-blur-md border shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 px-4 py-6 flex flex-col items-center justify-start text-center"
                    style="background-color: transparent; border-color: rgba(148, 163, 184, 0.35);"
                >
                    @if ($kartu['foto_url'])
                        <div class="shrink-0 overflow-hidden rounded-3xl border-2 border-primary/35 shadow-sm bg-white" style="width: 144px; height: 144px;">
                            <img
                                src="{{ $kartu['foto_url'] }}"
                                alt="{{ $kartu['nama'] }}"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    @else
                        <div class="shrink-0 rounded-3xl border-2 border-primary/35 shadow-sm flex items-center justify-center text-3xl font-extrabold tracking-tight text-text-on-surface select-none" style="width: 144px; height: 144px; background-color: rgba(255, 255, 255, 0.6);">
                            {{ $kartu['inisial'] }}
                        </div>
                    @endif

                    <h2 class="mt-4 text-sm sm:text-base font-bold text-text-slate leading-snug line-clamp-2">
                        {{ $kartu['nama'] }}
                    </h2>
                    <span class="mt-2 inline-block px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-[11px] font-semibold leading-tight">
                        {{ $kartu['jabatan'] }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Bagian Tentang Kami --}}
        <div
            class="rounded-3xl backdrop-blur-md border shadow-sm p-5 sm:p-7 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center"
            style="background-color: transparent; border-color: rgba(148, 163, 184, 0.35);"
        >
            @if ($fotoBersamaUrl)
                <div class="lg:col-span-5">
                    <div class="rounded-2xl overflow-hidden border border-surface-border shadow-xs bg-white">
                        <img
                            src="{{ $fotoBersamaUrl }}"
                            alt="Foto bersama tim PASKA JAYA"
                            class="w-full h-56 sm:h-64 object-cover object-center"
                        />
                    </div>
                </div>
            @endif

            <div class="{{ $fotoBersamaUrl ? 'lg:col-span-7' : 'lg:col-span-12' }} space-y-4">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-primary">Tentang Kami</span>
                    <h2 class="mt-1 text-xl sm:text-2xl font-extrabold text-text-on-surface tracking-tight">
                        Membangun Sistem Operasional Toko Bahan Bangunan yang Rapi &amp; Terintegrasi
                    </h2>
                </div>

                <p class="text-xs sm:text-sm text-text-body leading-relaxed">
                    Kami adalah tim di balik pengembangan sistem informasi manajemen <strong>Toko Bahan Bangunan Paska Jaya</strong>. Aplikasi ini dirancang untuk menyatukan pencatatan penjualan langsung, pengelolaan bon piutang pelanggan, pemantauan stok gudang, kas laci harian, hingga pelacakan pengantaran barang oleh karyawan ke dalam satu basis data yang akurat dan mudah digunakan.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                    <div class="rounded-2xl border border-white/80 px-4 py-3" style="background-color: rgba(255, 255, 255, 0.45);">
                        <p class="text-xs font-extrabold text-primary">4 Peran Terhubung</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Admin, Owner, Kasir, dan Karyawan saling tersinkron.</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 px-4 py-3" style="background-color: rgba(255, 255, 255, 0.45);">
                        <p class="text-xs font-extrabold text-primary">Data Real-Time</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Stok, saldo laci, dan status bon tercatat otomatis.</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 px-4 py-3" style="background-color: rgba(255, 255, 255, 0.45);">
                        <p class="text-xs font-extrabold text-primary">Siap Cetak PDF</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Nota transaksi, faktur bon, dan laporan siap unduh.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>