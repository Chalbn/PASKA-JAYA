@props([
    'headers' => [], // array header columns: ['No', 'Nama Barang', ['text' => 'Harga', 'align' => 'right'], ...]
    'striped' => true,
    'density' => 'compact', // compact, normal
])

@php
    $padding = $density === 'compact' ? 'px-4 py-2.5' : 'px-6 py-3.5';
@endphp

<div class="w-full bg-surface-card border border-surface-border rounded-2xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-text-slate border-collapse']) }}>
            @if(!empty($headers) || isset($thead))
                <thead class="bg-surface-container-low/80 border-b border-surface-border text-xs uppercase tracking-wider font-bold text-text-muted select-none">
                    @if(isset($thead))
                        {{ $thead }}
                    @else
                        <tr>
                            @foreach($headers as $header)
                                @php
                                    $isColArray = is_array($header);
                                    $label = $isColArray ? ($header['text'] ?? '') : $header;
                                    $align = $isColArray ? ($header['align'] ?? 'left') : 'left';
                                    $alignClass = match($align) {
                                        'right' => 'text-right',
                                        'center' => 'text-center',
                                        default => 'text-left',
                                    };
                                @endphp
                                <th scope="col" class="{{ $padding }} {{ $alignClass }} font-semibold text-text-slate">
                                    {{ $label }}
                                </th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
            @endif

            <tbody class="divide-y divide-surface-border {{ $striped ? '[&>tr:nth-child(even)]:bg-surface-canvas/50' : '' }} [&>tr:hover]:bg-surface-container-low/60 transition-colors">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if(isset($empty))
        <div class="p-8 text-center">
            {{ $empty }}
        </div>
    @endif

    @if(isset($pagination))
        <div class="px-5 py-3 border-t border-surface-border bg-surface-canvas/40 flex items-center justify-between text-xs text-text-muted">
            {{ $pagination }}
        </div>
    @endif
</div>
