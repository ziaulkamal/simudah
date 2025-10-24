@props(['status'])

@php
    $status = strtolower($status ?? 'inactive');

    $classes = match ($status) {
        'active' => 'bg-success text-white',
        'inactive' => 'bg-error text-white',
        default => 'bg-slate-400 text-white',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full $classes"]) }}>
    {{ ucfirst($status) }}
</span>
