<label class="block">
    <span>{{ $label }}</span>

    @if ($type === 'textarea')
        <textarea
            name="{{ $name }}"
            placeholder="{{ $placeholder ?? '' }}"
            @if($readonly) readonly @endif
            rows="3"
            class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent p-2.5
                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">{{ old($name, $value) }}</textarea>
    @else
        <span class="relative mt-1.5 flex">
            <input

                name="{{ $name }}"
                type="{{ $type }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder ?? '' }}"
                @if($readonly) readonly @endif
                class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                       @if($icon) pl-9 @endif
                       placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                       dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
            autocomplete="off">

            @if($icon)
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                <i class="{{ $icon }} text-base"></i>
            </span>
            @endif
        </span>
    @endif
</label>
