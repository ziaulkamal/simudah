<label class="block">
    <span>{{ $label }}</span>
    <select name="{{ $name }}"
        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
               hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
               dark:focus:border-accent">
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected($value == old($name, $selected))>{{ $text }}</option>
        @endforeach
    </select>
</label>
