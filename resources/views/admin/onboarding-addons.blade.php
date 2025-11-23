@extends('layouts.app')


@section('content')
{{-- @php
    dd(auth()->user());
@endphp --}}

<main class="main-content w-full place-items-center px-[var(--margin-x)] pb-6">
    <div class="py-5 text-center lg:py-6">
        <p class="text-sm uppercase">Apa yang ingin anda lakukan?</p>
        <h3 class="mt-1 text-xl font-semibold text-slate-600 dark:text-navy-100">
            Fitur ini tersedia untuk kebutuhan admin dan untuk beberapa addon tertentu.
        </h3>
    </div>
    <div class="grid max-w-4xl grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-5 lg:gap-6">
        <x-feature-card
            title="Pengaturan"
            description="Saat ini tersedia untuk mengatur kategori, dan beberapa improvement lainnya akan segera tersedia."
            image="images/illustrations/responsive-rose.svg"
            gradientFrom="pink-500"
            gradientTo="rose-500"
        >
            <a href="{{ route('category.index') }}" class="btn w-full border border-white/10 bg-white/20 text-white hover:bg-white/30 focus:bg-white/30">
            Lanjutkan
            </a>
        </x-feature-card>
        <x-feature-card
            title=" Pengguna"
            description="Fitur ini tersedia untuk manajemen Pelanggan yang mendaftar dan kelola akun petugas hingga pelanggan."
            image="images/illustrations/performance-indigo.svg"
            gradientFrom="purple-500"
            gradientTo="indigo-600"
        >
       <div x-data="usePopper({placement:'bottom-start',offset:4})"
     @click.outside="isShowPopper && (isShowPopper = false)"
     class="inline-flex relative w-full">

    <!-- Button full-width -->
    <button
        class="btn w-full border border-white/10 bg-white/20 text-white hover:bg-white/30 focus:bg-white/30 flex justify-between items-center px-4 py-2"
        x-ref="popperRef"
        @click="isShowPopper = !isShowPopper"
    >
        <span>Pilih Salah Satu</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200"
             :class="isShowPopper && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Popper full-width -->
    <div x-ref="popperRoot"
         class="popper-root relative mt-1 z-10"
         :class="isShowPopper && 'show'">
        <div class="popper-box rounded-md border border-slate-150 bg-white py-1.5 font-inter dark:border-navy-500 dark:bg-navy-700 w-full">
            <ul>
                <li>
                    <a href="{{ route('activation.index') }}"
                       class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-primary hover:text-white focus:bg-primary focus:text-white dark:hover:bg-accent dark:focus:bg-accent">
                        Permintaan Aktivasi Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.list') }}"
                       class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-primary hover:text-white focus:bg-primary focus:text-white dark:hover:bg-accent dark:focus:bg-accent">
                        Daftar Akun Management
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

    </div>
  </div>

        </x-feature-card>


    </div>
</main>
@endsection