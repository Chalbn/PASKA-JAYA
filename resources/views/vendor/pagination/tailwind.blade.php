@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        {{-- Mobile: Prev & Next + Ringkasan --}}
        <div class="flex items-center justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/60 text-slate-300 cursor-not-allowed flex items-center justify-center" aria-disabled="true" aria-label="Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-600 flex items-center justify-center shadow-2xs transition-all duration-150 hover:border-[#1D3FA3]/40 hover:bg-[#EBF2FF] hover:text-[#1D3FA3]" aria-label="Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            <p class="text-xs text-slate-500">
                Menampilkan <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span> – <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> data
            </p>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-600 flex items-center justify-center shadow-2xs transition-all duration-150 hover:border-[#1D3FA3]/40 hover:bg-[#EBF2FF] hover:text-[#1D3FA3]" aria-label="Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/60 text-slate-300 cursor-not-allowed flex items-center justify-center" aria-disabled="true" aria-label="Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop: Info Kiri + Pill Buttons Kanan --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs sm:text-sm text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span> – <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div class="flex items-center gap-1.5">
                {{-- Tombol Sebelumnya (<) --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Sebelumnya" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/60 text-slate-300 cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-600 flex items-center justify-center shadow-2xs transition-all duration-150 hover:border-[#1D3FA3]/40 hover:bg-[#EBF2FF] hover:text-[#1D3FA3]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($elements as $element)
                    {{-- Tanda Elipsis (...) --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="w-9 h-9 flex items-center justify-center text-sm text-slate-400 select-none">{{ $element }}</span>
                    @endif

                    {{-- Array Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="w-9 h-9 rounded-xl bg-[#1D3FA3] text-white font-semibold text-sm flex items-center justify-center shadow-sm shadow-[#1D3FA3]/25">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-600 font-medium text-sm flex items-center justify-center transition-all duration-150 hover:border-[#1D3FA3]/40 hover:bg-[#EBF2FF] hover:text-[#1D3FA3]">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Berikutnya (>) --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya" class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-600 flex items-center justify-center shadow-2xs transition-all duration-150 hover:border-[#1D3FA3]/40 hover:bg-[#EBF2FF] hover:text-[#1D3FA3]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Berikutnya" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/60 text-slate-300 cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
