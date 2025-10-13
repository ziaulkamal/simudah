@php
$baseClass = "btn font-medium text-white
              bg-{$color}
              hover:bg-{$color}-focus
              focus:bg-{$color}-focus
              active:bg-{$color}-focus/90
              dark:bg-accent dark:hover:bg-accent-focus
              dark:focus:bg-accent-focus dark:active:bg-accent/90";

$extraClass = $class ? " $class" : '';

$disabledClass = $disabled ? 'opacity-60 cursor-not-allowed' : '';
$wrapperClass = $full ? 'flex justify-center w-full' : 'flex justify-end pt-4';
@endphp

<div class="{{ $wrapperClass }}">
    @if ($back)
        {{-- Tombol Kembali --}}
        <button
            type="button"
            onclick="window.history.back()"
            class="{{ $baseClass }} {{ $disabledClass }} {{ $extraClass }}">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $label }}
        </button>

    @elseif ($href)
        {{-- Tombol Link --}}
        <a
            href="{{ $href }}"
            class="{{ $baseClass }} {{ $disabledClass }} {{ $extraClass }}">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $label }}
        </a>

    @else
        {{-- Tombol Biasa atau Submit --}}
        <button
            type="{{ $type }}"
            @if($disabled) disabled @endif
            class="{{ $baseClass }} {{ $disabledClass }} {{ $extraClass }}">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $label }}
        </button>
    @endif
</div>
