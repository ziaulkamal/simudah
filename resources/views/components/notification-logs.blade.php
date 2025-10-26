<div
    x-effect="if($store.global.isSearchbarActive) isShowPopper = false"
    x-data="usePopper({ placement: 'bottom-end', offset: 12 })"
    @click.outside="isShowPopper && (isShowPopper = false)"
    class="flex relative"
>

    {{-- 🔔 Tombol Notifikasi --}}
    <button
        @click="isShowPopper = !isShowPopper"
        x-ref="popperRef"
        class="btn relative size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-slate-500 dark:text-navy-100"
            stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M15.375 17.556h-6.75m6.75 0H21l-1.58-1.562a2.254 2.254 0 01-.67-1.596v-3.51a6.612 6.612 0 00-1.238-3.85 6.744 6.744 0 00-3.262-2.437v-.379c0-.59-.237-1.154-.659-1.571A2.265 2.265 0 0012 2c-.597 0-1.169.234-1.591.65a2.208 2.208 0 00-.659 1.572v.38c-2.621.915-4.5 3.385-4.5 6.287v3.51c0 .598-.24 1.172-.67 1.595L3 17.556h12.375zm0 0v1.11c0 .885-.356 1.733-.989 2.358A3.397 3.397 0 0112 22a3.397 3.397 0 01-2.386-.976 3.313 3.313 0 01-.989-2.357v-1.111h6.75z"/>
        </svg>

        {{-- 🔴 Badge --}}
        <span class="absolute -top-px -right-px flex size-3 items-center justify-center">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-secondary opacity-80"></span>
            <span class="inline-flex size-2 rounded-full bg-secondary"></span>
        </span>
    </button>

    {{-- 📦 Popper Dropdown --}}
    <div :class="isShowPopper && 'show'" class="popper-root" x-ref="popperRoot">
        <div
            x-data="{ activeTab: 'tabLogs' }"
            class="popper-box mx-4 mt-1 flex max-h-[calc(100vh-6rem)] w-[calc(100vw-2rem)] flex-col
                   rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-800 dark:bg-navy-700
                   dark:shadow-soft-dark sm:m-0 sm:w-80"
        >

            {{-- Header --}}
            <div class="rounded-t-lg bg-slate-100 text-slate-600 dark:bg-navy-800 dark:text-navy-200">
                <div class="flex items-center justify-between px-4 pt-2">
                    <h3 class="font-medium text-slate-700 dark:text-navy-100">Notifications</h3>
                    <button class="btn -mr-1.5 h-7 w-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="is-scrollbar-hidden flex shrink-0 overflow-x-auto px-3">
                    <button
                        @click="activeTab = 'tabLogs'"
                        :class="activeTab === 'tabLogs' ? 'border-primary dark:border-accent text-primary dark:text-accent-light' : 'border-transparent hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                        class="btn shrink-0 rounded-none border-b-2 px-3.5 py-2.5"
                    >
                        <span>Logs</span>
                    </button>
                </div>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content flex flex-col overflow-hidden">
                <div
                    x-show="activeTab === 'tabLogs'"
                    x-transition:enter="transition-all duration-300 easy-in-out"
                    x-transition:enter-start="opacity-0 [transform:translate3d(1rem,0,0)]"
                    x-transition:enter-end="opacity-100 [transform:translate3d(0,0,0)]"
                    class="is-scrollbar-hidden overflow-y-auto px-4"
                >
                    <div class="mt-8 pb-8 text-center">
                        <img class="mx-auto w-36" src="{{ asset('images/illustrations/empty-girl-box.svg') }}" alt="Empty" />
                        <div class="mt-5">
                            <p class="text-base font-semibold text-slate-700 dark:text-navy-100">No any logs</p>
                            <p class="text-slate-400 dark:text-navy-300">There are no unread logs yet</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
