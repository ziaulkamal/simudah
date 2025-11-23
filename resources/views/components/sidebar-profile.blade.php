

<div class="flex flex-col items-center space-y-3 py-3">
    <!-- Profile Dropdown -->
    <div x-data="usePopper({placement:'right-end',offset:12})"
         @click.outside="isShowPopper = false"
         class="flex">
        <!-- Avatar Button -->
        <button @click="isShowPopper = !isShowPopper"
                x-ref="popperRef"
                class="relative avatar size-12 cursor-pointer">
            <img class="rounded-full"
                 src="{{ asset('images/200x200.png') }}"
                 alt="Avatar">
            <span class="absolute right-0 size-3.5 rounded-full border-2 border-white bg-success dark:border-navy-700"></span>
        </button>

        <!-- Dropdown -->
        <div :class="isShowPopper && 'show'" class="popper-root fixed" x-ref="popperRoot">
            <div class="popper-box w-64 rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-600 dark:bg-navy-700">
                <!-- Header -->
                <div class="flex items-center space-x-4 rounded-t-lg bg-slate-100 py-4 px-4 dark:bg-navy-800">
                    <div class="avatar size-14">
                        <img class="rounded-full"
                             src="{{ asset('images/200x200.png') }}"
                             alt="Avatar">
                    </div>
                    <div>
                        <p class="text-base font-medium text-slate-700 dark:text-navy-100">
                           {{ strtoupper(session('login_name')) ?? 'Guest Account' }}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            {{ strtoupper(session('role_name')) ?? 'Disable' }}

                        </p>
                    </div>
                </div>

                <!-- Menu -->
                <div class="flex flex-col py-2">
                    <button
                        type="button"
                        @click="window.dispatchEvent(new CustomEvent('show-confirm-modal', {
                            detail: {
                                type: 'warning',
                                title: 'Konfirmasi Logout',
                                message: 'Apakah Anda yakin ingin keluar dari sistem?',
                                onConfirm: () => handleLogout()
                            }
                        }))"
                        class="btn h-9 w-full space-x-2 bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal />
@push('scripts')
<script>
async function handleLogout() {
    try {
        const headers = new Headers({
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        });

        const options = {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin', // pastikan cookie/session ikut terkirim
        };

        const response = await fetch("{{ route('logout') }}", options);
        const result = await response.json();

        if (result.status === 'success') {
            // Tampilkan modal sukses
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: {
                    type: 'success',
                    title: 'Logout Berhasil',
                    message: 'Anda akan dialihkan ke halaman login...'
                }
            }));

            // Tunggu 2 detik lalu redirect
            setTimeout(() => {
                window.location.href = '/auth-login';
            }, 2000);
        } else {
            throw new Error(result.message || 'Gagal logout');
        }
    } catch (err) {
        window.dispatchEvent(new CustomEvent('show-alert', {
            detail: {
                type: 'error',
                title: 'Gagal Logout',
                message: err.message || 'Terjadi kesalahan saat logout.'
            }
        }));
    }
}
</script>

@endpush