@props(['category'])

@php
    if (!$category) {
        echo '<div class="badge rounded-full bg-slate-100 text-slate-500 dark:bg-navy-600 dark:text-navy-200">-</div>';
        return;
    }

    // Gunakan ID atau nama kategori untuk hasil warna yang stabil
    $key = $category->id ?? crc32($category->name);

    // Hasilkan Hue unik (0–360 derajat)
    $hue = ($key * 47) % 360; // 47 itu angka prima agar distribusi warna merata

    // Saturasi dan lightness tetap seragam agar tampak profesional
    $saturation = 65; // %
    $lightness = 55;  // %

    $hslColor = "hsl($hue, {$saturation}%, {$lightness}%)";
@endphp

<div class="badge rounded-full px-3 py-1 text-white font-medium"
     style="background-color: {{ $hslColor }}">
    {{ $category->name }}
</div>
