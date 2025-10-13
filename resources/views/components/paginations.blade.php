@if ($paginator->hasPages())
<div class="grid grid-cols-1 gap-4 px-4 py-4 sm:grid-cols-3 sm:items-center sm:px-5">

    {{-- === KIRI: SHOW ENTRIES === --}}
    <div class="flex items-center justify-center sm:justify-start space-x-2 text-xs-plus">
        <span>Tampilkan</span>
        <label class="block">
            <select
                onchange="window.location.href='{{ request()->url() }}?per_page=' + this.value"
                class="form-select rounded-full border border-slate-300 bg-white px-2 py-1 pr-6 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                @foreach ([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </label>
        <span>Data</span>
    </div>

    {{-- === TENGAH: PAGINATION === --}}
    <div class="flex justify-center">
        {{-- ========== MOBILE PAGINATION (3 angka + elipsis) ========== --}}
        <ol class="pagination flex items-center gap-1 sm:hidden">
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $visible = 3;
                $start = max($current - floor($visible / 2), 1);
                $end = min($start + $visible - 1, $last);

                if ($end - $start < $visible - 1) {
                    $start = max($end - $visible + 1, 1);
                }
            @endphp

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="opacity-50 cursor-not-allowed">
                    <span class="flex size-8 items-center justify-center text-slate-400 dark:text-navy-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="flex size-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-300 dark:hover:bg-navy-450">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Show first page if needed --}}
            @if ($start > 1)
                <li>
                    <a href="{{ $paginator->url(1) }}"
                        class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-3 hover:bg-slate-300 dark:hover:bg-navy-450">
                        1
                    </a>
                </li>
                @if ($start > 2)
                    <li>
                        <span class="flex h-8 min-w-[2rem] items-center justify-center text-slate-400">…</span>
                    </li>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <li>
                        <span
                            class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg bg-primary px-3 text-white dark:bg-accent">{{ $i }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->url($i) }}"
                            class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-3 hover:bg-slate-300 dark:hover:bg-navy-450">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Elipsis dan Halaman Terakhir --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <li>
                        <span class="flex h-8 min-w-[2rem] items-center justify-center text-slate-400">…</span>
                    </li>
                @endif
                <li>
                    <a href="{{ $paginator->url($last) }}"
                        class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-3 hover:bg-slate-300 dark:hover:bg-navy-450">
                        {{ $last }}
                    </a>
                </li>
            @endif

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="flex size-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-300 dark:hover:bg-navy-450">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
            @else
                <li class="opacity-50 cursor-not-allowed">
                    <span class="flex size-8 items-center justify-center text-slate-400 dark:text-navy-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </li>
            @endif
        </ol>

        {{-- ========== DESKTOP PAGINATION (5 angka + elipsis) ========== --}}
        <ol class="pagination hidden sm:flex items-center gap-1">
            @php
                $visible = 5;
                $start = max($current - floor($visible / 2), 1);
                $end = min($start + $visible - 1, $last);
                if ($end - $start < $visible - 1) {
                    $start = max($end - $visible + 1, 1);
                }
            @endphp

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="opacity-50 cursor-not-allowed">
                    <span class="flex size-8 items-center justify-center text-slate-400 dark:text-navy-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="flex size-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-300 dark:hover:bg-navy-450">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                </li>
            @endif

            {{-- First page --}}
            @if ($start > 1)
                <li>
                    <a href="{{ $paginator->url(1) }}"
                        class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-3 hover:bg-slate-300 dark:hover:bg-navy-450">1</a>
                </li>
                @if ($start > 2)
                    <li>
                        <span class="flex h-8 min-w-[2rem] items-center justify-center text-slate-400">…</span>
                    </li>
                @endif
            @endif

            {{-- Numbered pages --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <li>
                        <span
                            class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg bg-primary px-3 text-white dark:bg-accent">{{ $i }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->url($i) }}"
                            class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-3 hover:bg-slate-300 dark:hover:bg-navy-450">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last page --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <li>
                        <span class="flex h-8 min-w-[2rem] items-center justify-center text-slate-400">…</span>
                    </li>
                @endif
                <li>
                    <a href="{{ $paginator->url($last) }}"
                        class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-3 hover:bg-slate-300 dark:hover:bg-navy-450">
                        {{ $last }}
                    </a>
                </li>
            @endif

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="flex size-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-300 dark:hover:bg-navy-450">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
            @else
                <li class="opacity-50 cursor-not-allowed">
                    <span class="flex size-8 items-center justify-center text-slate-400 dark:text-navy-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </li>
            @endif
        </ol>
    </div>

    {{-- === KANAN: INFO TOTAL DATA === --}}
    <div class="text-xs-plus flex justify-center sm:justify-end text-center sm:text-right text-slate-600 dark:text-navy-100">
        <div class="w-full sm:w-auto">
            {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}
            dari {{ $paginator->total() }} data
        </div>
    </div>
</div>
@endif
