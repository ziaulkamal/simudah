<div x-data="{expandedItem:null}" class="h-[calc(100%-4.5rem)] overflow-x-hidden pb-6"
    x-init="$el._x_simplebar = new SimpleBar($el);">
        <ul class="flex flex-1 flex-col px-4 font-inter">
            <li x-data="accordionItem('menu-management-setting')">
                <a :class="expanded ? 'text-slate-800 font-semibold dark:text-navy-50' : 'text-slate-600 dark:text-navy-200 hover:text-slate-800 dark:hover:text-navy-50'"
                    @click="expanded = !expanded"
                    class="flex items-center justify-between py-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out"
                    href="javascript:void(0);">
                    <span>Manajemen Pengaturan</span>
                    <svg :class="expanded && 'rotate-90'" xmlns="http://www.w3.org/2000/svg"
                        class="size-4 text-slate-400 transition-transform ease-in-out" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <ul x-collapse x-show="expanded">
                    <li>
                        <a x-data="navLink" href="{{ route('user.request') }}"
                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                            class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                            <div class="flex items-center space-x-2">
                                <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                <span>Permintaan Aktivasi</span>
                            </div>
                        </a>
                    </li>

                    <li>
                        <a x-data="navLink" href="{{ route('user.role') }}"
                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                            class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                            <div class="flex items-center space-x-2">
                                <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                <span>Rubah Role Akses</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li x-data="accordionItem('menu-accounts-setting')">
                <a :class="expanded ? 'text-slate-800 font-semibold dark:text-navy-50' : 'text-slate-600 dark:text-navy-200 hover:text-slate-800 dark:hover:text-navy-50'"
                    @click="expanded = !expanded"
                    class="flex items-center justify-between py-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out"
                    href="javascript:void(0);">
                    <span>Manajemen Akun</span>
                    <svg :class="expanded && 'rotate-90'" xmlns="http://www.w3.org/2000/svg"
                        class="size-4 text-slate-400 transition-transform ease-in-out" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <ul x-collapse x-show="expanded">
                    <li>
                        <a x-data="navLink" href="{{ route('user.list') }}"
                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                            class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                            <div class="flex items-center space-x-2">
                                <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                <span>Akun Petugas</span>
                            </div>
                        </a>
                    </li>

                    <li>
                        <a x-data="navLink" href="{{ route('user.role') }}"
                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                            class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                            <div class="flex items-center space-x-2">
                                <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                <span>Akun Tertutup (Disabled)</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
</div>