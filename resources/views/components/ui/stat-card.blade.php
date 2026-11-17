@props([
    'title' => '',
    'value' => '0',
    'subtitle' => null,
    'badge' => null,
    'variant' => null,
    'accent' => null, // backward-compatibility
    'icon' => null,
    'href' => null,
    'actionText' => 'Rincian',
])

@php
    // Petakan variant/accent ke palet soft Stitch Paska Jaya
    $var = $variant ?? $accent ?? 'neutral';
    if ($var === 'secondary') $var = 'marigold';
    if ($var === 'tertiary') $var = 'pearl';
    if ($var === 'success') $var = 'primary';

    $styles = [
        // primary (Saldo / Kas): Kartu putih dengan ikon pill bg-[#e7eeff] text-[#2E4365], pill keterangan bg-[#f0f3ff] text-[#162d4d]
        'primary' => [
            'card' => 'bg-white border-[#E2E8F0]/90 hover:border-[#2E4365]/30',
            'icon_bg' => 'bg-[#e7eeff] text-[#2E4365]',
            'badge' => 'bg-[#f0f3ff] text-[#162D4D] border border-[#d6e3ff]/60',
            'note' => 'text-[#64748B]',
            'link' => 'text-[#2E4365] hover:text-[#162D4D]',
        ],
        // marigold (Penjualan / Omzet): Latar lembut bg-[#ffddb5]/25, panel ikon bg-[#F3D58D]/40 text-[#835500], pill tren bg-[#ffddb5]/70 text-[#633f00]
        'marigold' => [
            'card' => 'bg-[#ffddb5]/25 border-[#ffddb5]/80 hover:border-[#E59D2C]/40',
            'icon_bg' => 'bg-[#F3D58D]/45 text-[#835500]',
            'badge' => 'bg-[#ffddb5]/70 text-[#633f00] border border-[#f3d58d]',
            'note' => 'text-[#835500]/80',
            'link' => 'text-[#835500] hover:text-[#523300]',
        ],
        // pearl (Bon / Piutang): Latar lembut bg-[#EBDDC5]/30, panel ikon bg-[#EBDDC5]/65 text-[#8A3B08], pill keterangan text-[#4f1d00]
        'pearl' => [
            'card' => 'bg-[#EBDDC5]/30 border-[#D9C4A6]/80 hover:border-[#8A3B08]/30',
            'icon_bg' => 'bg-[#EBDDC5]/65 text-[#8A3B08]',
            'badge' => 'bg-[#EBDDC5]/80 text-[#4f1d00] border border-[#d9c4a6]',
            'note' => 'text-[#8A3B08]/80',
            'link' => 'text-[#8A3B08] hover:text-[#4f1d00]',
        ],
        // danger (Stok Menipis / Kritis): Latar lembut bg-[#ffdad6]/25, panel ikon bg-[#ffdad6]/60 text-[#ba1a1a], pill keterangan text-[#93000a]
        'danger' => [
            'card' => 'bg-[#ffdad6]/25 border-[#ffdad6]/80 hover:border-[#ba1a1a]/30',
            'icon_bg' => 'bg-[#ffdad6]/60 text-[#ba1a1a]',
            'badge' => 'bg-[#ffdad6]/70 text-[#93000a] border border-[#ffb4ab]',
            'note' => 'text-[#ba1a1a]/80',
            'link' => 'text-[#ba1a1a] hover:text-[#7a0007]',
        ],
        // neutral (Default)
        'neutral' => [
            'card' => 'bg-white border-[#E2E8F0]/90 hover:border-slate-300',
            'icon_bg' => 'bg-[#f0f3ff] text-[#2E4365]',
            'badge' => 'bg-[#f0f3ff] text-[#475569] border border-slate-200',
            'note' => 'text-[#64748B]',
            'link' => 'text-[#2E4365] hover:text-[#162D4D]',
        ],
    ];

    $s = $styles[$var] ?? $styles['neutral'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl p-5 border shadow-xs hover:shadow-sm transition-all duration-200 flex flex-col justify-between {$s['card']}"]) }}>
    {{-- BAGIAN ATAS: Label Kategori di Kiri + Ikon Bersih di Kanan --}}
    <div class="flex items-center justify-between gap-3">
        <p class="text-xs font-medium text-[#64748B] truncate tracking-normal">
            {{ $title }}
        </p>

        @if($icon || isset($iconSlot))
            <div class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0 {{ $s['icon_bg'] }}">
                @if(isset($iconSlot))
                    {{ $iconSlot }}
                @else
                    {!! $icon !!}
                @endif
            </div>
        @endif
    </div>

    {{-- BAGIAN TENGAH: Nilai Utama 1 Baris Utuh + Pill Status di sampingnya --}}
    <div class="my-3 flex flex-wrap items-baseline gap-2.5">
        <span class="whitespace-nowrap tabular-nums tracking-tight text-2xl xl:text-[26px] font-bold text-[#111c2d] leading-none">
            {{ $value }}
        </span>

        @if($badge)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $s['badge'] }}">
                {{ $badge }}
            </span>
        @endif
    </div>

    {{-- BAGIAN BAWAH: Sub-keterangan Singkat & Tautan Aksi Minimal --}}
    <div class="flex items-center justify-between gap-2 text-xs pt-1">
        @if($subtitle)
            <p class="truncate font-normal {{ $s['note'] }}">
                {{ $subtitle }}
            </p>
        @else
            <div></div>
        @endif

        @if($href)
            <a href="{{ $href }}" class="inline-flex items-center gap-1 font-semibold shrink-0 transition-colors {{ $s['link'] }}">
                <span>{{ $actionText }}</span>
                <span class="text-xs font-bold">&rarr;</span>
            </a>
        @endif
    </div>
</div>
