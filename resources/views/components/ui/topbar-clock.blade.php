@props([
    'tanggal' => '',
])

<div
    x-data="{
        timeStr: '',
        isOnline: typeof navigator !== 'undefined' ? navigator.onLine : true,
        justRecovered: false,
        recoverTimer: null,
        init() {
            const updateTime = () => {
                const now = new Date();
                this.timeStr = now.toLocaleTimeString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).replace(/\./g, ':');
            };
            updateTime();
            setInterval(updateTime, 1000);
        },
        handleOffline() {
            this.isOnline = false;
            this.justRecovered = false;
            clearTimeout(this.recoverTimer);
        },
        handleOnline() {
            this.isOnline = true;
            this.justRecovered = true;
            clearTimeout(this.recoverTimer);
            this.recoverTimer = setTimeout(() => {
                this.justRecovered = false;
            }, 3500);
        }
    }"
    x-on:offline.window="handleOffline()"
    x-on:online.window="handleOnline()"
    class="shrink-0"
>
    {{-- Banner Peringatan Tipis di Atas Layar Saat Offline / Pulih --}}
    <div
        x-show="!isOnline || justRecovered"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-full"
        style="display: none;"
        class="fixed top-0 inset-x-0 z-[80] px-4 py-1.5 text-center text-xs font-semibold shadow-md flex items-center justify-center gap-2"
        :class="!isOnline ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white'"
        role="status"
    >
        <template x-if="!isOnline">
            <span class="inline-flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse"></span>
                <span>Koneksi internet terputus (Offline). Pastikan jaringan tersambung sebelum menyimpan transaksi.</span>
            </span>
        </template>
        <template x-if="isOnline && justRecovered">
            <span class="inline-flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Koneksi internet tersambung kembali.</span>
            </span>
        </template>
    </div>

    {{-- Card Tanggal (Bold) & Jam Digital Real-Time + Live Pulse / Status Koneksi (Poppins) --}}
    <div
        class="bg-white/10 backdrop-blur-xs border border-white/15 rounded-2xl px-3.5 sm:px-5 py-1.5 text-center shadow-2xs flex flex-col items-center justify-center font-['Poppins',sans-serif] transition-colors"
        :class="!isOnline ? 'border-amber-300/60 bg-rose-500/20' : ''"
    >
        <span class="text-[11px] sm:text-xs font-bold text-blue-100 leading-tight whitespace-nowrap">{{ $tanggal }}</span>

        <div class="flex items-center justify-center gap-1.5 mt-0.5 flex-wrap">
            <template x-if="isOnline">
                <span class="inline-flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2 shrink-0" aria-hidden="true">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </span>
                    <span class="text-xs sm:text-sm font-normal tracking-wider text-white tabular-nums leading-tight" x-text="timeStr">--:--:--</span>
                    <span class="text-[11px] font-semibold tracking-wider text-blue-200 uppercase leading-tight">WIB</span>
                    <span
                        x-show="justRecovered"
                        style="display: none;"
                        class="ml-1 px-2 py-0.5 rounded-full bg-emerald-500/90 text-white text-[10px] font-semibold leading-none"
                    >
                        Tersambung Kembali
                    </span>
                </span>
            </template>

            <template x-if="!isOnline">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-500/90 text-white text-[10px] sm:text-[11px] font-semibold leading-tight">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                    <span>Koneksi Terputus · Offline</span>
                </span>
            </template>
        </div>
    </div>
</div>
