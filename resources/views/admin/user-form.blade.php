@extends('layouts.app')

@section('content')
<main
    x-data="userFormHandler()"
    class="main-content w-full px-[var(--margin-x)] pb-8"
>
    <form id="userForm" @submit.prevent="submitForm">
        @csrf

        {{-- Header Title --}}
        <div class="flex flex-col items-center justify-between space-y-4 py-5 sm:flex-row sm:space-y-0 lg:py-6">
            <div class="flex items-center space-x-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h2 class="text-xl font-medium text-slate-700 line-clamp-1 dark:text-navy-50">
                    Tambah User Baru
                </h2>
            </div>
            <div class="flex justify-center space-x-2">
                <x-button
                    label="Batal"
                    color="slate-300"
                    href="{{ route('user.list') }}"
                />

                <x-button
                    type="submit"
                    label="Simpan"
                    color="primary"
                />
            </div>
        </div>

        {{-- Main Content --}}
        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            {{-- Kolom Kiri --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="card">
                    <div class="p-4 sm:p-5 space-y-5">
                        <label class="block">
                            <span class="font-medium text-slate-600 dark:text-navy-100">Username</span>
                            <input name="username"
                                value="{{ old('username') }}"
                                required
                                pattern="^[^\s]+$"
                                oninput="this.value = this.value.replace(/\s/g, '')"
                                title="Username tidak boleh mengandung spasi"
                                class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                        hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:focus:border-accent"
                                placeholder="Masukkan username tanpa spasi" autocomplete="off">
                        </label>

                        <label class="block">
                            <span class="font-medium text-slate-600 dark:text-navy-100">Password</span>
                            <input name="password" type="password" required
                                   class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:focus:border-accent"
                                   placeholder="Masukkan password">
                        </label>

                        <label class="block">
                            <span class="font-medium text-slate-600 dark:text-navy-100">Konfirmasi Password</span>
                            <input name="password_confirmation" type="password" required
                                   class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:focus:border-accent"
                                   placeholder="Ulangi password">
                        </label>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="col-span-12 lg:col-span-4">
                <div class="card space-y-5 p-4 sm:p-5">
                    <x-select-dropdown
                        name="role_id"
                        label="Status Akses"
                        :options="$roles"
                        :selected="old('role_id')"
                    />
                    <x-nik-search
                        name="people_id"
                        label="Pilih Orang (berdasarkan NIK)"
                        :options="$peoples"
                    />
                    <x-select-dropdown
                        name="status"
                        label="Status Akun"
                        :options="$statuses"
                        :selected="old('status', 'active')"
                    />
                </div>
            </div>
        </div>
    </form>
</main>
{{-- === KOMONEN MODAL GLOBAL === --}}
<x-modal />
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userFormHandler', () => ({
        loading: false,
        async submitForm() {
            this.loading = true;
            const form = document.getElementById('userForm');
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('secureuser.store') }}", {
                    method: "POST",
                    headers: { "Accept": "application/json" },
                    body: formData
                });

                const data = await response.json();
                this.loading = false;

                if (response.ok) {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'success',
                            title: 'Berhasil!',
                            message: `User ${data.user.username} berhasil disimpan.`
                        }
                    }));
                    form.reset();
                } else {
                    // Ambil pesan error utama
                    let messages = [];

                    // Pesan utama dari server
                    if (data.message) {
                        messages.push(data.message);
                    }

                    // Pesan detail per field (jika ada)
                    if (data.errors) {
                        for (const field in data.errors) {
                            // Gabung semua error field (biasanya 1, tapi aman kalau lebih)
                            messages.push(...data.errors[field]);
                        }
                    }

                    // Satukan jadi 1 string dengan baris baru
                    const finalMessage = messages.join('\n');

                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'error',
                            title: 'Gagal!',
                            message: finalMessage || 'Terjadi kesalahan tak dikenal.'
                        }
                    }));
                }
            } catch (e) {
                this.loading = false;
                console.error(e);
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', title: 'Error', message: 'Gagal mengirim data ke server.' }
                }));
            }
        }
    }));
});
</script>

@endsection
