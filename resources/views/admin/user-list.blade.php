@extends('layouts.app')

@section('content')
<main
    x-data="userListHandler()"
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
                        @forelse ($users as $user)
                            <tr id="user-row-{{ $user->id }}" class="border-b border-slate-200 dark:border-navy-500">
                                <td class="px-4 py-3">{{ $users->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700 dark:text-navy-50">
                                    {{ $user->username }}
                                </td>
                                <td class="px-4 py-3">{{ $user->role->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$user->status" />
                                </td>
                                <td class="px-4 py-3">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center space-x-2">


                                    <button type="button"
                                            @click="confirmDelete({{ $user->id }}, '{{ $user->username }}')"
                                            class="btn space-x-2 bg-error font-medium text-white hover:bg-error-focus hover:shadow-lg">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-500">Tidak ada data ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="text-xs-plus flex justify-center sm:justify-end text-slate-600 dark:text-navy-100 sm:pr-[calc(var(--margin-x)*-1)]">
                {{ $users->links('components.paginations') }}
            </div>
        </div>
    </div>

    {{-- Komponen Modal Global --}}
    <x-modal />
</main>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userListHandler', () => ({
        async confirmDelete(id, username) {
            // Tampilkan modal konfirmasi
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
                const response = await fetch(`/api/secure-users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Hapus baris dari tabel
                    document.getElementById(`user-row-${id}`)?.remove();

                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'success',
                            title: 'Berhasil!',
                            message: `User "${username}" berhasil dihapus.`
                        }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'error',
                            title: 'Gagal!',
                            message: data.message || 'Gagal menghapus user.'
                        }
                    }));
                }
            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'error',
                        title: 'Error',
                        message: 'Terjadi kesalahan saat menghapus data.'
                    }
                }));
            }
        }
    }));
});
</script>
@endpush
@endsection
