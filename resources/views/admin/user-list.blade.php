@extends('layouts.app')

@section('content')
<main
    x-data="userListHandler()"
    x-init="loadUsers()"
    class="main-content w-full px-[var(--margin-x)] pb-8"
>
    <x-breadcrumb-header
        :title="$title"
        :submenu="$submenu"
        route-name="addons"
        route-label="Addons"
        :menu-items="[
            [
                'label' => 'Buat User Baru',
                'url' => route('user.create'),
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'mt-px size-4.5\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\' stroke-width=\'2\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 4v16m8-8H4\'/></svg>',
            ],
        ]"
    />

    <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
        <div class="card mt-3">
            <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                <table class="is-hoverable w-full text-left">
                    <thead>
                        <tr>
                            <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">#</th>
                            <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Username</th>
                            <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Role</th>
                            <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Status</th>
                            <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Dibuat</th>
                            <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="users.length > 0">
                            <template x-for="(user, index) in users" :key="user.id">
                                <tr :id="`user-row-${user.id}`" class="border-b border-slate-200 dark:border-navy-500">
                                    <td class="px-4 py-3" x-text="index + 1"></td>
                                    <td class="px-4 py-3 font-medium text-slate-700 dark:text-navy-50" x-text="user.username"></td>
                                    <td class="px-4 py-3" x-text="user.role?.name ?? '-'"></td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="''" x-bind:status="user.status"></x-status-badge>
                                    </td>
                                    <td class="px-4 py-3" x-text="new Date(user.created_at).toLocaleDateString('id-ID')"></td>
                                    <td class="px-4 py-3 text-center space-x-2">
                                        <button type="button"
                                            @click="confirmDelete(user.id, user.username)"
                                            class="btn space-x-2 bg-error font-medium text-white hover:bg-error-focus hover:shadow-lg">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="users.length === 0">
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-500">Tidak ada data ditemukan</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userListHandler', () => ({
        users: [],
        async loadUsers() {
            try {
                const response = await fetch("/api/accounts/users", { headers: { "Accept": "application/json" } });
                const data = await response.json();
                this.users = data.data ?? data;
            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', title: 'Error', message: 'Gagal memuat data pengguna.' }
                }));
            }
        },
        async confirmDelete(id, username) {
            window.dispatchEvent(new CustomEvent('show-confirm-modal', {
                detail: {
                    type: 'warning',
                    title: 'Hapus User?',
                    message: `Apakah kamu yakin ingin menghapus user "${username}"?`,
                    onConfirm: () => this.deleteUser(id, username)
                }
            }));
        },
        async deleteUser(id, username) {
            try {
                const response = await fetch(`/api/accounts/users/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (response.ok) {
                    this.users = this.users.filter(u => u.id !== id);
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: { type: 'success', title: 'Berhasil!', message: `User "${username}" berhasil dihapus.` }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: { type: 'error', title: 'Gagal!', message: data.message || 'Gagal menghapus user.' }
                    }));
                }
            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', title: 'Error', message: 'Terjadi kesalahan saat menghapus data.' }
                }));
            }
        }
    }));
});
</script>
@endpush
@endsection
