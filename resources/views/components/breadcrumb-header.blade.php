<div class="flex flex-wrap items-center justify-between space-y-2 sm:space-y-0 py-5 lg:py-6">
    {{-- Bagian kiri: Title + Breadcrumb --}}
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-1">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                {{ $title ?? env('APP_NAME') }}
            </h2>

            {{-- Dropdown menu --}}
            @if(!empty($menuItems))
            <div
                x-data="usePopper({placement:'bottom-start', offset:4})"
                @click.outside="isShowPopper && (isShowPopper = false)"
                class="inline-flex"
            >
                <button
                    x-ref="popperRef"
                    @click="isShowPopper = !isShowPopper"
                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20
                           focus:bg-slate-300/20 active:bg-slate-300/25
                           dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20
                           dark:active:bg-navy-300/25"
                >
                    <i class="fas fa-chevron-down"></i>
                </button>

                <div
                    x-ref="popperRoot"
                    class="popper-root"
                    :class="isShowPopper && 'show'"
                >
                    <div
                        class="popper-box rounded-md border border-slate-150 bg-white py-1.5
                               font-inter dark:border-navy-500 dark:bg-navy-700"
                    >
                        <ul>
                            @foreach($menuItems as $item)
                                <li>
                                    <a href="{{ $item['url'] ?? '#' }}"
                                        class="flex h-8 items-center space-x-3 px-3 pr-8 font-medium tracking-wide
                                               outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800
                                               focus:bg-slate-100 focus:text-slate-800
                                               dark:hover:bg-navy-600 dark:hover:text-navy-100
                                               dark:focus:bg-navy-600 dark:focus:text-navy-100">
                                        {!! $item['icon'] ?? '' !!}
                                        <span>{{ $item['label'] ?? 'Menu' }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Separator --}}
        <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
        </div>

        {{-- Breadcrumb --}}
        <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
                <a class="text-primary transition-colors hover:text-primary-focus
                         dark:text-accent-light dark:hover:text-accent"
                    href="{{ route($routeName) }}">
                    {{ $routeLabel }}
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li>{{ $submenu ?? '' }}</li>
        </ul>
    </div>
</div>
