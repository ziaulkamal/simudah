<div class="text-center space-y-4"
     x-data="closeAccountHandler({{ $people->id }})">
    <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">
        Tutup Akun
    </h2>
    <p class="text-slate-500">
        Apakah Anda yakin ingin menutup akun pelanggan ini? Tindakan ini tidak dapat dibatalkan.
    </p>

    <button
        @click="openConfirmModal()"
        class="btn rounded-full bg-error text-white hover:bg-error-focus focus:ring-2 focus:ring-error/50">
        Tutup Akun Sekarang
    </button>

    <!-- Modal Konfirmasi -->
    <template x-if="showModal">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center"
            x-transition
        >
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
                @click="showModal = false"
                x-transition.opacity
            ></div>

            <!-- Modal Box -->
            <div
                class="relative z-50 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-navy-700"
                x-transition.scale.origin.center
            >
                <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                    Konfirmasi Tutup Akun
                </h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-navy-200">
                    Setelah akun ditutup, semua transaksi pending akan dibatalkan,
                    dan pelanggan tidak dapat login kembali.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        @click="showModal = false"
                        class="btn rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600
                               hover:bg-slate-100 dark:border-navy-500 dark:text-navy-100">
                        Batal
                    </button>

                    <button
                        @click="closeAccount()"
                        class="btn rounded-full bg-error text-white hover:bg-error-focus focus:ring-2 focus:ring-error/50">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function closeAccountHandler(peopleId) {
        return {
            showModal: false,
            loading: false,

            openConfirmModal() {
                this.showModal = true;
            },

            async closeAccount() {
                this.loading = true;
                try {
                    const response = await fetch(`/api/people/${peopleId}/close-account`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });

                    const data = await response.json();

                    this.showModal = false;

                    if (response.ok && data.success) {
                        // ✅ Tampilkan notifikasi sukses
                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                type: 'success',
                                title: 'Akun Ditutup',
                                message: data.message || 'Akun pelanggan berhasil dinonaktifkan.'
                            }
                        }));
                    } else {
                        // ⚠️ Tampilkan notifikasi error
                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                type: 'error',
                                title: 'Gagal Menutup Akun',
                                message: data.message || 'Terjadi kesalahan saat menonaktifkan akun.'
                            }
                        }));
                    }
                } catch (error) {
                    // 🚨 Error jaringan/server
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'error',
                            title: 'Kesalahan Server',
                            message: 'Tidak dapat terhubung ke server. Silakan coba lagi.'
                        }
                    }));
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
