<div x-data="loginModal()" x-init="init()">
    <template x-teleport="#x-teleport-target">
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/60" @click="close()" x-show="showModal" x-transition.opacity></div>

            <!-- Modal Box -->
            <div class="relative max-w-lg rounded-lg bg-white px-4 py-10 text-center" x-show="showModal" x-transition.opacity>
                <svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>

                <div class="mt-4">
                    <h2 class="text-2xl text-slate-700" x-text="title"></h2>
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
function loginModal() {
    return {
        showModal: false,
        title: 'Login Required',
        message: 'Silakan login untuk mengakses halaman ini.',
        init() {
            window.addEventListener('show-login-modal', (event) => {
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
