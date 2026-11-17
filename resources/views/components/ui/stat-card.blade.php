@props([
    'title' => '',
    'value' => '0',
    'subtitle' => null,
    'badge' => null,
    'variant' => null,
    'accent' => 'primary', // primary (Cobalt Blue), secondary (Marigold), tertiary (Citrine Brown), danger, success, neutral
    'icon' => null,
    'href' => null,
    'actionText' => 'Rincian selengkapnya',
])

@php
    // Normalisasi varian / aksen sesuai token resmi Paska Jaya
    $colorKey = $variant ?? $accent ?? 'primary';
    if ($colorKey === 'marigold') $colorKey = 'secondary';
    if ($colorKey === 'pearl') $colorKey = 'tertiary';

    $accentStyles = [
        // Primary: Cobalt Blue (#1D4ED8) & Sky Tint (#E0F2FE) - Sesuai Referensi Gambar
        'primary' => [
            'icon_bg' => 'bg-surface-container text-primary',
            'badge' => 'bg-surface-container text-primary border border-blue-200',
            'link' => 'text-primary hover:text-primary-container',
        ],
        // Secondary: Marigold (#E59D2C / #835500)
        'secondary' => [
            'icon_bg' => 'bg-amber-100/70 text-secondary-dark',
            'badge' => 'bg-amber-50 text-secondary-dark border border-amber-200/80',
            'link' => 'text-secondary-dark hover:text-amber-900',
        ],
        // Tertiary: Citrine Brown / Pearl (#8A3B08 / #EBDDC5)
        'tertiary' => [
            'icon_bg' => 'bg-tertiary-pearl/60 text-tertiary',
            'badge' => 'bg-tertiary-pearl/80 text-tertiary border border-[#D9C4A6]',
            'link' => 'text-tertiary hover:text-tertiary-container',
        ],
        // Danger: Merah (#B91C1C) - Stok Menipis / Kritis
        'danger' => [
            'icon_bg' => 'bg-status-danger-bg text-status-danger',
            'badge' => 'bg-red-50 text-status-danger border border-red-200',
            'link' => 'text-status-danger hover:text-red-800',
        ],
        // Success: Emerald (#0F766E) - Selesai / Aman
        'success' => [
            'icon_bg' => 'bg-status-success-bg text-status-success',
            'badge' => 'bg-emerald-50 text-status-success border border-emerald-200',
            'link' => 'text-status-success hover:text-emerald-800',
        ],
        // Neutral
        'neutral' => [
            'icon_bg' => 'bg-surface-container-low text-text-slate',
            'badge' => 'bg-surface-container-low text-text-body border border-surface-border',
            'link' => 'text-primary hover:text-primary-container',
        ],
    ];

    $style = $accentStyles[$colorKey] ?? $accentStyles['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs transition hover:shadow-sm flex flex-col justify-between']) }}>
    <div>
        {{-- Header Kartu: Label Judul di Kiri + Ikon Kategori di Kanan --}}
        <div class="flex items-center justify-between gap-3">
            <p class="text-xs font-semibold text-text-muted truncate">
                {{ $title }}
            </p>

            @if($icon || isset($iconSlot))
                <div class="p-2.5 rounded-xl shrink-0 {{ $style['icon_bg'] }}">
                    @if(isset($iconSlot))
                        {{ $iconSlot }}
                    @else
                        {!! $icon !!}
                    @endif
                </div>
            @endif
        </div>

        {{-- Nilai Utama (Angka / Rupiah Wajib 1 Baris Utuh dengan tabular-nums) --}}
        <div class="mt-2 flex flex-wrap items-baseline gap-2">
            <span class="whitespace-nowrap tabular-nums tracking-tight text-2xl sm:text-[26px] font-black text-text-on-surface leading-tight">
                {{ $value }}
            </span>

            @if($badge)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $style['badge'] }}">
                    {{ $badge }}
                </span>
            @endif
        </div>

        {{-- Subtitle / Catatan Keterangan --}}
        @if($subtitle)
            <p class="mt-1 text-xs text-text-muted font-normal leading-relaxed">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    {{-- Tautan Aksi Rincian (Jika Ada) --}}
    @if($href)
        <div class="mt-3.5 pt-3 border-t border-surface-border text-xs flex items-center justify-between">
            <a href="{{ $href }}" class="font-semibold inline-flex items-center gap-1 transition-colors {{ $style['link'] }}">
                <span>{{ $actionText }}</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif
</div>
