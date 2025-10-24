@props(['active' => 'detail'])

@php
    $menus = [
        [
            'id' => 'detail',
            'label' => 'Detail Informasi',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
        [
            'id' => 'transaksi',
            'label' => 'Transaksi Pembayaran',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
    d="M3 8h18M3 12h18m-1 4H4a2 2 0 01-2-2V8a2 2 0 012-2h16a2 2 0 012 2v6a2 2 0 01-2 2z" />',
        ],
        [
            'id' => 'lokasi',
            'label' => 'Lokasi',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 21c4.418-4.418 7-8.418 7-11a7 7 0 10-14 0c0 2.582 2.582 6.582 7 11z" />
            <circle cx="12" cy="10" r="2.5" stroke-width="1.5" stroke="currentColor" fill="none" />',
        ],
        [
            'id' => 'kategori',
            'label' => 'Kategori',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />',
        ],
        [
            'id' => 'tutup',
            'label' => 'Tutup Akun',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21H13.5a2.25 2.25 0 002.25-2.25V15M9 12h9m0 0l-3-3m3 3l-3 3" />',
        ],
    ];
@endphp

<ul class="mt-6 space-y-1.5 font-inter font-medium" x-data="{ active: '{{ $active }}' }" @change-tab.window="active = $event.detail">
    @foreach ($menus as $menu)
        <li>
            <button
                x-on:click="$dispatch('change-tab', '{{ $menu['id'] }}')"
                class="flex w-full items-center space-x-2 rounded-lg px-4 py-2.5 tracking-wide outline-hidden transition-all"
                :class="active === '{{ $menu['id'] }}'
                    ? 'bg-primary text-white dark:bg-accent'
                    : 'group hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100'">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="size-5"
                    :class="active === '{{ $menu['id'] }}'
                        ? 'text-white'
                        : 'text-slate-400 transition-colors group-hover:text-slate-500 dark:text-navy-300 dark:group-hover:text-navy-200'"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $menu['icon'] !!}
                </svg>
                <span>{{ $menu['label'] }}</span>
            </button>
        </li>
    @endforeach
</ul>
