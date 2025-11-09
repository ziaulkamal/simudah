@extends('layouts.app')

@section('content')
<main
    x-data="kebutuhanFormHandler()"
    class="main-content w-full px-[var(--margin-x)] pb-8"
>
    <form id="kebutuhanForm" @submit.prevent="submitForm">
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
                    Tambah Kebutuhan Baru
                </h2>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            <div class="col-span-12 lg:col-span-8">
                <div class="card">
                    <div class="p-4 sm:p-5 space-y-5">
                        {{-- Nama Kategori --}}
                        <x-form-input
                            name="name"
                            label="Nama Kategori"
                            type="text"
                            placeholder="Masukkan nama kategori"
                            value="{{ old('nama_kategori') }}"
                            required
                        />

                        {{-- Harga --}}
                        <x-form-input
                            name="price"
                            label="Harga"
                            type="number"
                            placeholder="###"
                            value="{{ old('harga') }}"
                            required
                        />

                        {{-- Status --}}
                        <x-select-dropdown
                            name="status"
                            label="Status"
                            :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']"
                            :selected="old('status', 'aktif')"
                        />

                        {{-- Tombol Aksi --}}
                        <div class="flex flex-col sm:flex-row justify-end sm:space-x-3 space-y-2 sm:space-y-0 pt-2">
                            <x-button
                                label="Batal"
                                color="slate-300"
                                href="{{ route('category.list') }}"
                            />
                            <x-button
                                type="submit"
                                label="Simpan"
                                color="primary"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kebutuhanFormHandler', () => ({
        loading: false,
        async submitForm() {
            this.loading = true;
            const form = document.getElementById('kebutuhanForm');
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('categories.store') }}", {
                    method: "POST",
                    headers: { "Accept": "application/json" },
                    body: formData
                });

                const data = await response.json();
                this.loading = false;

                if (response.ok && data.success) {
                    const namaKategori = data.data?.name || 'kategori baru';
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'success',
                            title: 'Berhasil!',
                            message: `Kategori "${namaKategori}" berhasil disimpan.`
                        }
                    }));
                    form.reset();
                } else {
                    // 🔹 Tampilkan pesan error
                    let messages = [];
                    if (data.message) messages.push(data.message);
                    if (data.errors) {
                        for (const field in data.errors) {
                            messages.push(...data.errors[field]);
                        }
                    }

                    const finalMessage = messages.join('\n') || 'Terjadi kesalahan tak dikenal.';
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'error',
                            title: 'Gagal!',
                            message: finalMessage
                        }
                    }));
                }
            } catch (e) {
                this.loading = false;
                console.error(e);
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'error',
                        title: 'Error',
                        message: 'Gagal mengirim data ke server.'
                    }
                }));
            }
        }
    }));
});
</script>

@endsection
