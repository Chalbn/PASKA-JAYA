<x-layouts.app title="Tim" role="admin">
    <div class="-m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 relative min-h-[calc(100vh-5rem)] overflow-hidden bg-gradient-to-br from-[#F4F7FE] via-[#EDF3FF] to-[#DBE8FF] flex flex-col justify-between">
        {{-- Lapisan Latar Belakang Transparan untuk Foto Bersama Tim --}}
        <div class="pointer-events-none select-none absolute inset-0 overflow-hidden" aria-hidden="true">
            @if ($fotoBersamaUrl)
                <img
                    src="{{ $fotoBersamaUrl }}"
                    alt="Foto bersama tim PASKA JAYA"
                    class="h-full w-full object-cover object-center opacity-35"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-[#F4F7FE]/70 via-[#EEF4FF]/35 to-[#D9E6FF]/65"></div>
            @else
                <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/4 h-80 w-80 rounded-full bg-sky-300/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -right-12 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
            @endif
        </div>

        {{-- Konten Utama --}}
        <div class="relative z-10 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Tim</h1>
                    <p class="mt-1 text-xs sm:text-sm text-text-muted">Anggota tim PASKA JAYA.</p>
                </div>
            </div>

            {{-- Kartu Anggota Tim dari Database --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5">
                @foreach ($daftarKartu as $kartu)
                    @php
                        $tints = [
                            1 => 'from-sky-100/75 via-blue-50/45 to-white/70',
                            2 => 'from-amber-50/75 via-orange-50/40 to-white/70',
                            3 => 'from-teal-50/75 via-cyan-50/45 to-white/70',
                            4 => 'from-rose-50/70 via-purple-50/40 to-white/70',
                            5 => 'from-indigo-100/70 via-sky-50/45 to-white/70',
                        ];
                        $cardTint = $tints[(($loop->index % 5) + 1)] ?? $tints[1];
                    @endphp
                    <div class="group relative rounded-2xl bg-gradient-to-b {{ $cardTint }} backdrop-blur-md border border-white/85 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 px-5 py-7 flex flex-col items-center justify-center text-center">
                        @if ($kartu['foto_url'])
                            <img
                                src="{{ $kartu['foto_url'] }}"
                                alt="{{ $kartu['nama'] }}"
                                class="h-24 w-24 rounded-3xl object-cover object-top border-2 border-primary/35 shadow-xs bg-white/85"
                            />
                        @else
                            <div class="h-24 w-24 rounded-3xl bg-white/85 backdrop-blur-xs border-2 border-primary/35 shadow-xs flex items-center justify-center text-2xl font-extrabold tracking-tight text-text-on-surface select-none">
                                {{ $kartu['inisial'] }}
                            </div>
                        @endif

                        <h2 class="mt-4 text-sm sm:text-base font-bold text-text-slate leading-snug line-clamp-2">
                            {{ $kartu['nama'] }}
                        </h2>
                        <p class="mt-1 text-xs font-medium text-text-muted leading-snug line-clamp-2">
                            {{ $kartu['jabatan'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>