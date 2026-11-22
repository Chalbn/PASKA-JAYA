@props([
    'role' => null,
])

@php
    $user = auth()->user();
    $user?->loadMissing('role');
    $resolvedRole = strtolower((string) ($role ?? ($user?->role?->nama ?? '')));
    $canPoll = auth()->check() && $resolvedRole !== '';

    $indexUrl = Route::has('notifications.index') ? route('notifications.index') : url('/notifications');
    $readAllUrl = Route::has('notifications.read-all') ? route('notifications.read-all') : url('/notifications/read-all');
    $readBaseUrl = url('/notifications');
@endphp

<div
    x-data="{
        open: false,
        filter: 'all',
        unreadCount: 0,
        notifications: [],
        pollingId: null,
        canPoll: {{ $canPoll ? 'true' : 'false' }},
        csrf() {
            return document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
        },
        get visibleNotifications() {
            if (this.filter === 'unread') {
                return this.notifications.filter(n => !n.is_read);
            }
            return this.notifications;
        },
        async fetchNotifications() {
            if (!this.canPoll) return;
            try {
                const res = await fetch('{{ $indexUrl }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                this.unreadCount = Number(data.unread_count ?? 0);
                this.notifications = Array.isArray(data.notifications) ? data.notifications : [];
            } catch (e) {
                // Abaikan error jaringan sementara agar UI tetap stabil
            }
        },
        async markAsRead(id, navigateUrl = null) {
            const target = this.notifications.find(n => n.id === id);
            if (target && !target.is_read) {
                target.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                if (this.canPoll) {
                    try {
                        const res = await fetch(`{{ $readBaseUrl }}/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrf(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.unreadCount = Number(data.unread_count ?? this.unreadCount);
                        }
                    } catch (e) {
                    }
                }
            }

            if (navigateUrl) {
                window.location.href = navigateUrl;
            }
        },
        async markAllAsRead() {
            if (this.unreadCount === 0) return;
            this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
            this.unreadCount = 0;

            if (this.canPoll) {
                try {
                    await fetch('{{ $readAllUrl }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });
                } catch (e) {
                }
            }
        },
        init() {
            if (this.canPoll) {
                this.fetchNotifications();
                this.pollingId = setInterval(() => this.fetchNotifications(), 10000);
            }
        }
    }"
    x-on:click.outside="open = false"
    x-on:keydown.escape.window="open = false"
    class="relative shrink-0"
>
    {{-- Tombol Trigger Lonceng (Liquid Glass Style) --}}
    <button
        type="button"
        x-on:click="open = !open; if (open && canPoll) fetchNotifications();"
        aria-label="Notifikasi Aktivitas"
        :aria-expanded="open"
        class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white/12 hover:bg-white/20 backdrop-blur-md border border-white/25 text-white shadow-[inset_0_1px_1px_rgba(255,255,255,0.35),0_4px_12px_rgba(15,23,42,0.12)] transition-all duration-200 cursor-pointer focus:outline-none"
    >
        <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Badge Angka Belum Dibaca --}}
        <span
            x-show="unreadCount > 0"
            x-text="unreadCount > 99 ? '99+' : unreadCount"
            style="display: none;"
            class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1.5 rounded-full bg-rose-500 text-white text-[11px] font-bold leading-none flex items-center justify-center ring-2 ring-[#1D3FA3] shadow-sm tabular-nums"
        ></span>
    </button>

    {{-- Panel Pop-over Daftar Notifikasi --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-180"
        x-transition:enter-start="opacity-0 -translate-y-1.5 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-120"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-1.5 scale-95"
        style="display: none;"
        class="fixed inset-x-3 top-[72px] sm:inset-x-auto sm:top-auto sm:absolute sm:right-0 sm:mt-3 sm:w-96 rounded-2xl bg-white border border-slate-200/80 shadow-[0_18px_40px_-8px_rgba(15,23,42,0.18)] overflow-hidden z-50 text-left"
    >
        {{-- Header Panel --}}
        <div class="px-4 py-3.5 border-b border-slate-100 bg-white flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-slate-800">Notifikasi Aktivitas</h3>
                <span
                    x-show="unreadCount > 0"
                    x-text="unreadCount"
                    class="bg-[#EBF2FF] text-[#1D3FA3] text-xs font-semibold px-2 py-0.5 rounded-full tabular-nums"
                ></span>
            </div>

            <button
                type="button"
                x-show="unreadCount > 0"
                x-on:click="markAllAsRead()"
                class="text-xs font-semibold text-[#1D3FA3] hover:underline cursor-pointer"
            >
                Tandai semua dibaca
            </button>
        </div>

        {{-- Filter Tab Mini --}}
        <div class="px-4 py-2.5 bg-slate-50/70 border-b border-slate-100 flex items-center gap-1.5">
            <button
                type="button"
                x-on:click="filter = 'all'"
                class="px-3 py-1 rounded-full text-xs font-semibold transition cursor-pointer"
                :class="filter === 'all' ? 'bg-[#1D3FA3] text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
            >
                Semua
            </button>
            <button
                type="button"
                x-on:click="filter = 'unread'"
                class="px-3 py-1 rounded-full text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5"
                :class="filter === 'unread' ? 'bg-[#1D3FA3] text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
            >
                <span>Belum Dibaca</span>
                <span
                    x-show="unreadCount > 0"
                    x-text="unreadCount"
                    class="px-1.5 py-0.2 rounded-full text-[10px] font-bold tabular-nums"
                    :class="filter === 'unread' ? 'bg-white/20 text-white' : 'bg-[#EBF2FF] text-[#1D3FA3]'"
                ></span>
            </button>
        </div>

        {{-- Daftar Item Notifikasi --}}
        <div class="max-h-96 overflow-y-auto dropdown-scrollbar divide-y divide-slate-100">
            <template x-for="notif in visibleNotifications" :key="notif.id">
                <div
                    x-on:click="markAsRead(notif.id, notif.url)"
                    class="px-4 py-3.5 transition-colors duration-150 cursor-pointer flex items-start gap-3"
                    :class="notif.is_read ? 'bg-white hover:bg-slate-50' : 'bg-[#F4F8FF] hover:bg-[#EBF2FF]/80'"
                >
                    {{-- Ikon Kategori --}}
                    <div
                        class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                        :class="{
                            'bg-emerald-50 text-emerald-600': notif.type === 'transaksi',
                            'bg-rose-50 text-rose-600': notif.type === 'stok_habis',
                            'bg-amber-50 text-amber-600': notif.type === 'stok_menipis',
                            'bg-indigo-50 text-indigo-600': notif.type === 'pengantaran',
                            'bg-[#EBF2FF] text-[#1D3FA3]': notif.type === 'bon' || notif.type === 'sistem',
                        }"
                    >
                        <template x-if="notif.type === 'transaksi'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="notif.type === 'stok_habis'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </template>
                        <template x-if="notif.type === 'stok_menipis'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </template>
                        <template x-if="notif.type === 'pengantaran'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </template>
                        <template x-if="notif.type === 'bon' || notif.type === 'sistem'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </template>
                    </div>

                    {{-- Konten Notifikasi --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p
                                class="text-xs leading-snug truncate"
                                :class="notif.is_read ? 'font-semibold text-slate-600' : 'font-bold text-slate-900'"
                                x-text="notif.title"
                            ></p>
                            <span
                                x-show="!notif.is_read"
                                class="w-2 h-2 rounded-full bg-[#1D3FA3] shrink-0 mt-1"
                                title="Belum dibaca"
                            ></span>
                        </div>
                        <p
                            class="text-xs mt-1 leading-relaxed line-clamp-2"
                            :class="notif.is_read ? 'text-slate-500' : 'text-slate-600'"
                            x-text="notif.message"
                        ></p>
                        <div class="mt-2 flex items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-400" x-text="notif.time_ago"></span>

                            <button
                                type="button"
                                x-show="!notif.is_read"
                                x-on:click.stop="markAsRead(notif.id)"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#1D3FA3] hover:text-[#16317E] px-1.5 py-0.5 rounded-md hover:bg-blue-100/60 transition cursor-pointer"
                                title="Tandai dibaca"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Tandai dibaca</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="visibleNotifications.length === 0" class="px-6 py-10 text-center">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-600">Belum ada aktivitas terbaru</p>
                <p class="text-[11px] text-slate-400 mt-1" x-text="filter === 'unread' ? 'Semua notifikasi sudah Anda baca.' : 'Notifikasi aktivitas terbaru akan muncul otomatis di sini.'"></p>
            </div>
        </div>
    </div>
</div>
