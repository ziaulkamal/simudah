<div class="rounded-lg bg-gradient-to-br from-{{ $gradientFrom }} to-{{ $gradientTo }} py-6 px-5 text-center">
    <h4 class="text-xl font-semibold text-white">{{ $title }}</h4>
    <p class="pt-2 text-white">{{ $description }}</p>
    <div class="px-5 py-8">
        <img class="w-full" src="{{ asset($image) }}" alt="image" />
    </div>

    <!-- Slot untuk action (bisa tombol tunggal atau dropdown) -->
    <div>
        {{ $slot }}
    </div>
</div>
