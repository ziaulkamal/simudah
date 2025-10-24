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
            class="{{ $baseClass }} {{ $disabledClass }} {{ $extraClass }}"
            :disabled="loading"  {{-- ✅ integrasi Alpine --}}
        >
            <template x-if="!loading">
                <span>
                    @if($icon)
                        <i class="{{ $icon }} mr-2"></i>
                    @endif
                    {{ $label }}
                </span>
            </template>

            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </template>
        </button>
    @endif
</div>
