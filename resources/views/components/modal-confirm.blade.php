<div
    x-data="{
        show: false,
        title: 'Konfirmasi',
        message: '',
        yesCallback: null,
        noCallback: null,
        init() {
            window.addEventListener('show-confirm', (event) => {
                this.title = event.detail.title || 'Konfirmasi';
                this.message = event.detail.message || '';
                this.yesCallback = event.detail.yes || null;
                this.noCallback = event.detail.no || null;
                this.show = true;
            });
        }
    }"
    x-init="init()"
>
    <template x-teleport="#x-teleport-target">
        <div
            class="fixed inset-0 z-[110] flex flex-col items-center justify-center px-4 py-6 sm:px-5"
            x-show="show"
            @keydown.window.escape="show = false; if(noCallback) noCallback()"
        >
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
                @click="show = false; if(noCallback) noCallback()"
                x-show="show"
                x-transition.opacity
            ></div>

            <!-- Modal Content -->
            <div
                class="relative w-[427px] min-w-[310px] flex-none inline-block rounded-lg bg-white px-4 py-10 text-center transition-opacity duration-300 dark:bg-navy-700 sm:px-5"
                x-show="show"
                x-transition.opacity
            >
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z"/>
                </svg>

                <div class="mt-4">
                    <h2 class="text-2xl text-slate-700 dark:text-navy-100" x-text="title"></h2>
                    <p class="mt-2 text-slate-600 dark:text-navy-200" x-text="message"></p>

                    <!-- Buttons -->
                    <div class="flex justify-center gap-2 mt-6">
                        <button
                            class="btn bg-slate-200 text-slate-800 hover:bg-slate-300"
                            @click="show = false; if(noCallback) noCallback()"
                        >Batal</button>
                        <button
                            class="btn bg-primary text-white hover:bg-primary-focus"
                            @click="show = false; if(yesCallback) yesCallback()"
                        >Lanjut</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
