<div
    x-data="roleModal()"
    x-init="init()"
>
    <template x-teleport="#x-teleport-target">
        <div
            class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
            x-show="showModal"
            role="dialog"
        >
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                @click="close()"
                x-show="showModal"
                x-transition.opacity
            ></div>

            <!-- Modal Box -->
            <div
                class="relative max-w-lg rounded-lg bg-white px-4 py-10 text-center transition-opacity duration-300 dark:bg-navy-700 sm:px-5"
                x-show="showModal"
                x-transition.opacity
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="inline size-28 text-error"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>

                <div class="mt-4">
                    <h2 class="text-2xl text-slate-700 dark:text-navy-100" x-text="title"></h2>
                    <p class="mt-2" x-text="message"></p>
                    <button
                        @click="close()"
                        class="btn mt-6 bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function roleModal() {
    return {
        showModal: false,
        title: 'Akses Ditolak',
        message: 'Level Anda tidak diperbolehkan mengakses halaman ini.',
        init() {
            window.addEventListener('show-role-modal', (event) => {
                if(event.detail.title) this.title = event.detail.title;
                if(event.detail.message) this.message = event.detail.message;
                this.showModal = true;
            });
        },
        close() {
            this.showModal = false;
        }
    }
}
</script>
