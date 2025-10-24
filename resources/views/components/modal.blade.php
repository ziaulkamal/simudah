<div
    x-data="{
        showModal: false,
        type: '{{ $type ?? 'success' }}',
        title: '{{ $title ?? 'Informasi' }}',
        message: '',
        isConfirm: false,
        onConfirm: null,
        init() {
            // Alert biasa
            window.addEventListener('show-alert', (event) => {
                this.type = event.detail.type || 'success';
                this.title = event.detail.title || 'Informasi';
                this.message = event.detail.message || '';
                this.isConfirm = false;
                this.showModal = true;
            });

            // Confirm modal
            window.addEventListener('show-confirm-modal', (event) => {
                this.type = event.detail.type || 'warning';
                this.title = event.detail.title || 'Konfirmasi';
                this.message = event.detail.message || '';
                this.isConfirm = true;
                this.onConfirm = event.detail.onConfirm || null;
                this.showModal = true;
            });
        }
    }"
    x-init="init()"
>
    <template x-teleport="#x-teleport-target">
        <div
            class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
            x-show="showModal"
            @keydown.window.escape="showModal = false"
        >
            <div
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
                @click="showModal = false"
                x-show="showModal"
                x-transition.opacity
            ></div>

            <div
                class="relative w-[427px] min-w-[310px] flex-none inline-block rounded-lg bg-white px-4 py-10 text-center transition-opacity duration-300 dark:bg-navy-700 sm:px-5"
                x-show="showModal"
                x-transition.opacity
            >
                @php
                    $colors = [
                        'success' => ['bg'=>'bg-success text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90','icon'=>'text-success','svg'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                        'warning' => ['bg'=>'bg-amber-500 text-white hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-600/90','icon'=>'text-amber-500','svg'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>'],
                        'error' => ['bg'=>'bg-error text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90','icon'=>'text-error','svg'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'],
                    ];
                @endphp

                <!-- Icon -->
                <div x-show="type === 'success'" x-cloak>
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $colors['success']['svg'] !!}
                    </svg>
                </div>
                <div x-show="type === 'warning'" x-cloak>
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $colors['warning']['svg'] !!}
                    </svg>
                </div>
                <div x-show="type === 'error'" x-cloak>
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $colors['error']['svg'] !!}
                    </svg>
                </div>

                <!-- Konten -->
                <div class="mt-4">
                    <h2 class="text-2xl text-slate-700 dark:text-navy-100" x-text="title"></h2>
                    <p class="mt-2 text-slate-600 dark:text-navy-200" x-text="message"></p>

                    <div class="mt-6 flex justify-center gap-3" x-show="isConfirm">
                        <button
                            class="btn border border-primary font-medium text-primary hover:bg-primary hover:text-white focus:bg-primary focus:text-white active:bg-primary/90 dark:border-accent dark:text-accent-light dark:hover:bg-accent dark:hover:text-white dark:focus:bg-accent dark:focus:text-white dark:active:bg-accent/90"
                            @click="onConfirm(); showModal = false"
                        >
                            Yes
                        </button>
                        <button
                            class="btn border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90"
                            @click="showModal = false"
                        >
                            No
                        </button>
                    </div>

                    <button
                        class="btn mt-6 font-medium text-white"
                        :class="{
                            'bg-success hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90': type === 'success',
                            'bg-amber-500 hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-600/90': type === 'warning',
                            'bg-error hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90': type === 'error'
                        }"
                        @click="showModal = false"
                        x-show="!isConfirm"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
