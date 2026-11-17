@props([
    'variant' => 'neutral', // lunas, belum-lunas, selesai, kritis, menipis, cash, transfer, lewat-tempo, unit, neutral
    'size' => 'md', // sm, md
    'icon' => null,
])

@php
    $normalizedVariant = strtolower(trim($variant));
    
    // Satuan barang yang dikenali
    $unitVariants = ['sak', 'batang', 'kubik', 'pcs', 'kaleng', 'kg', 'meter', 'dus', 'unit', 'lembar', 'roll', 'karung'];

    if (in_array($normalizedVariant, $unitVariants)) {
        $normalizedVariant = 'unit';
    }

    $variantClasses = [
        // Lunas: Emerald #0F766E / #ECFDF5 dengan simbol ✓
        'lunas' => 'bg-status-success-bg text-status-success border border-emerald-200',
        // Belum Lunas: Amber #8A3B08 / #FEF3C7
        'belum-lunas' => 'bg-status-warning-bg text-status-warning border border-amber-200',
        // Selesai: Navy / Emerald dengan simbol ✓
        'selesai' => 'bg-surface-container text-primary border border-surface-border font-semibold',
        // Kritis / Habis: Merah lembut #FEE2E2 dengan teks #B91C1C
        'kritis' => 'bg-status-danger-bg text-status-danger border border-red-200',
        'menipis' => 'bg-status-warning-bg text-status-warning border border-amber-200',
        // CASH: Navy solid (#2E4365) teks putih
        'cash' => 'bg-primary text-primary-on font-bold tracking-wide uppercase',
        // TRANSFER: Biru muda / surface-container teks Police Blue
        'transfer' => 'bg-surface-container text-primary font-bold tracking-wide uppercase border border-surface-border',
        // Lewat tempo: Merah tegas #B91C1C teks putih
        'lewat-tempo' => 'bg-status-danger text-white font-bold',
        // Chip satuan barang (sak, batang, kubik, pcs, kaleng, kg): Pearl (#EBDDC5) teks Police Blue (#2E4365)
        'unit' => 'bg-tertiary-pearl text-primary font-semibold border border-[#D9C4A6]',
        // Generic variants
        'primary' => 'bg-surface-container text-primary border border-surface-border',
        'secondary' => 'bg-secondary-container/40 text-secondary-dark border border-secondary/30',
        'success' => 'bg-status-success-bg text-status-success border border-emerald-200',
        'warning' => 'bg-status-warning-bg text-status-warning border border-amber-200',
        'danger' => 'bg-status-danger-bg text-status-danger border border-red-200',
        'neutral' => 'bg-surface-container-low text-text-body border border-surface-border',
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-2.5 py-1 text-xs',
    ];

    $classes = 'inline-flex items-center gap-1 font-medium rounded-full select-none transition ' 
        . ($variantClasses[$normalizedVariant] ?? $variantClasses['neutral']) 
        . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($normalizedVariant === 'lunas' || $normalizedVariant === 'selesai')
        <svg class="w-3.5 h-3.5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    @elseif($icon)
        <span class="shrink-0">{!! $icon !!}</span>
    @endif

    <span>{{ $slot }}</span>
</span>
