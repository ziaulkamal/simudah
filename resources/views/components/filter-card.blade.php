<div class="flex items-center justify-between">
    <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">
        {{ $title }}
    </h2>

    {{-- Search & Toggle --}}
    <form method="GET" action="{{ $action }}" class="flex items-center space-x-2">
        <div class="flex items-center" x-data="{isInputActive:false}">
            <label class="block">
                <input name="search" value="{{ request('search') }}"
                    x-effect="isInputActive === true && $nextTick(() => { $el.focus() });"
                    :class="isInputActive ? 'w-32 lg:w-48' : 'w-0'"
                    class="form-input bg-transparent px-1 text-right transition-all duration-100 placeholder:text-slate-500 dark:placeholder:text-navy-200"
                    placeholder="Cari nama pelanggan..." type="text"
                    @keyup.enter="$root.querySelector('form').submit()" />
            </label>
            <button type="button" @click="isInputActive = !isInputActive"
                class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>

        <button type="button" @click="isFilterExpanded = !isFilterExpanded"
            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="M18 11.5H6M21 4H3m6 15h6" />
            </svg>
        </button>
    </form>
</div>

{{-- FILTER SECTION --}}
<div x-show="isFilterExpanded" x-collapse>
    <div class="max-w-2xl py-3">
        <form method="GET" action="{{ $action }}">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">
                {{-- Slot untuk form fields --}}
                {{ $slot }}
            </div>

            <div class="mt-4 text-right space-x-2">
                <a href="{{ url()->current() }}"
                    class="btn font-medium text-slate-700 hover:bg-slate-300/20">
                    Reset
                </a>
                <button type="submit" class="btn bg-primary font-medium text-white hover:bg-primary-focus">
                    Terapkan
                </button>
            </div>
        </form>
    </div>
</div>
