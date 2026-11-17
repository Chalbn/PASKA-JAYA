@props([
    'steps' => [], // array of ['title' => '...', 'date' => '...', 'status' => 'completed|current|pending', 'note' => '...']
])

<div {{ $attributes->merge(['class' => 'flow-root']) }}>
    <ul role="list" class="-mb-8">
        @if(!empty($steps))
            @foreach($steps as $index => $step)
                @php
                    $isLast = $index === count($steps) - 1;
                    $status = $step['status'] ?? 'pending'; // completed, current, pending
                @endphp
                <li>
                    <div class="relative pb-8">
                        @if(!$isLast)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 {{ $status === 'completed' ? 'bg-primary' : 'bg-surface-border' }}" aria-hidden="true"></span>
                        @endif

                        <div class="relative flex items-start space-x-3.5">
                            {{-- Lingkaran Ikon --}}
                            @if($status === 'completed')
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-primary flex items-center justify-center ring-4 ring-white shadow-xs">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </div>
                            @elseif($status === 'current')
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-secondary-container flex items-center justify-center ring-4 ring-secondary/20 shadow-xs animate-pulse">
                                        <span class="h-3 w-3 rounded-full bg-secondary"></span>
                                    </span>
                                </div>
                            @else
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-surface-canvas border-2 border-surface-border flex items-center justify-center ring-4 ring-white">
                                        <span class="h-2 w-2 rounded-full bg-surface-border-subtle"></span>
                                    </span>
                                </div>
                            @endif

                            {{-- Konten Status + Jam --}}
                            <div class="min-w-0 flex-1 pt-0.5">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-bold {{ $status === 'completed' ? 'text-primary' : ($status === 'current' ? 'text-text-slate' : 'text-text-muted') }}">
                                        {{ $step['title'] ?? '' }}
                                    </p>
                                    @if(!empty($step['date']))
                                        <span class="text-xs text-text-muted font-medium tabular-nums whitespace-nowrap">
                                            {{ $step['date'] }}
                                        </span>
                                    @endif
                                </div>

                                @if(!empty($step['note']))
                                    <p class="mt-1 text-xs text-text-body">
                                        {{ $step['note'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </ul>
</div>
