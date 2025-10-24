<label class="block">
    <span>{{ $label ?? 'Cari NIK / Nama' }}</span>
    <input
        name="{{ $name ?? 'identityNumber' }}"
        class="mt-1.5 w-full"
        x-init="$el._x_tom = new Tom($el, {
    create: false,
    plugins: ['caret_position', 'input_autogrow'],
    maxItems: 1,
    valueField: 'id',
    labelField: 'label',
    searchField: ['label'],
    placeholder: '{{ $placeholder ?? 'Ketik NIK atau Nama...' }}',
    options: {{ Js::from($options ?? []) }}
})"
        placeholder="{{ $placeholder ?? 'Ketik NIK atau Nama...' }}"
        type="text"
    />
</label>
