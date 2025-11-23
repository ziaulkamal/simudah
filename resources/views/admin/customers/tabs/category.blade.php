<div
    x-data="categoryTab(
        {{ $people->id }},
        {{ json_encode($categories) }},
        {{ json_encode($categoryHistories, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) }}
    )"
    class="space-y-6"
>
    <!-- Header -->
    <div
        x-data="{ editMode: false }"
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-4 dark:border-navy-500 gap-3"
    >
        <!-- Judul & Info Kategori Aktif -->
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                {{ $title }}
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-navy-200">
                <strong>Kategori Aktif:</strong>
                <span x-text="activeCategory?.name ?? '-'"></span>
                <template x-if="activeCategory">
                    <span>
                        — Rp<span x-text="Number(activeCategory.price).toLocaleString('id-ID')"></span>
                    </span>
                </template>
            </p>
        </div>

        <!-- Bagian kanan header -->
        @if (session()->get('role_level') === 99 || session()->get('role_level') === 1)
        <div class="flex flex-wrap items-center gap-3">
            <!-- Mode Edit -->
            <template x-if="editMode">
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <select
                        x-model="selectedCategory"
                        class="form-select rounded-lg border border-slate-300 bg-transparent px-3 py-2 dark:border-navy-500 w-full sm:w-auto"
                    >
                        <option value="">-- Pilih Kategori Baru --</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option
                                :value="cat.id"
                                x-text="`${cat.name} - Rp${Number(cat.price).toLocaleString('id-ID')}`">
                            </option>
                        </template>
                    </select>

                    <button
                        @click="assignCategory"
                        :disabled="!selectedCategory || loading"
                        class="btn bg-primary text-white hover:bg-primary-focus focus:ring-2 focus:ring-primary/50 rounded-full w-full sm:w-auto text-center"
                    >
                        <template x-if="!loading">
                            <span>Simpan</span>
                        </template>
                        <template x-if="loading">
                            <span>Memproses...</span>
                        </template>
                    </button>
                </div>
            </template>

            <!-- Tombol Edit / Batal -->
            <button
                @click="editMode = !editMode"
                class="btn rounded-full border border-slate-300 text-slate-600 hover:bg-slate-100
                    dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600 w-full sm:w-auto text-center"
            >
                <template x-if="!editMode">
                    <span class="flex items-center justify-center sm:justify-start space-x-2">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <span>Edit</span>
                    </span>
                </template>
                <template x-if="editMode">
                    <span class="flex items-center justify-center sm:justify-start space-x-2 text-primary">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Batal</span>
                    </span>
                </template>
            </button>
        </div>
        @endif
    </div>

    <!-- Daftar Riwayat -->
    <div class="mt-6">
        <h3 class="text-md font-semibold mb-2">Riwayat Perubahan Kategori</h3>

        <!-- Wrapper responsive -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-slate-200 dark:border-navy-500 text-sm table-auto">
                <thead class="bg-slate-50 dark:bg-navy-700">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Kategori</th>
                        <th class="px-4 py-2 text-left">Harga</th>
                        <th class="px-4 py-2 text-left">Tanggal Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="history.length === 0">
                        <tr>
                            <td colspan="4" class="text-center py-3 text-slate-500">
                                Belum ada riwayat kategori
                            </td>
                        </tr>
                    </template>

                    <template x-for="(item, index) in history" :key="item.id ?? index">
                        <tr class="border-t border-slate-200 dark:border-navy-500">
                            <td class="px-4 py-2" x-text="index + 1"></td>
                            <td class="px-4 py-2" x-text="item.category?.name || '-'"></td>
                            <td class="px-4 py-2" x-text="item.category?.price ? 'Rp' + Number(item.category.price).toLocaleString('id-ID') : '-'"></td>
                            <td class="px-4 py-2" x-text="item.changed_at || '-'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('categoryTab', (peopleId, categoryList, initialHistory = []) => ({
        categories: categoryList || [],
        selectedCategory: '',
        history: initialHistory || [],
        loading: false,
        activeCategory: null,
        editMode: false, // pastikan ada editMode

        async init() {
            // Set kategori aktif dari riwayat terakhir
            if (this.history.length > 0) {
                const last = this.history[this.history.length - 1];
                this.activeCategory = last.category ?? {
                    name: last.category_name,
                    price: last.price
                };
            }

            if (this.history.length === 0) {
                await this.loadHistory();
            }
        },

        async loadHistory() {
            try {
                const res = await fetch(`/api/people/${peopleId}/category-change-count`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('Gagal memuat riwayat');
                const data = await res.json();

                this.history = Array.isArray(data.data?.category_history)
                    ? data.data.category_history
                    : [];

                // Perbarui kategori aktif setelah fetch
                if (this.history.length > 0) {
                    const last = this.history[this.history.length - 1];
                    this.activeCategory = last.category ?? {
                        name: last.category_name,
                        price: last.price
                    };
                }
            } catch (e) {
                console.error('Gagal load history:', e);
                this.history = [];
            }
        },

        async assignCategory() {
            if (!this.selectedCategory) return;

            this.loading = true;
            try {
                const res = await fetch(`/api/people/${peopleId}/assign-category`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ category_id: this.selectedCategory })
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memperbarui kategori');
                }

                // Update riwayat
                this.history = Array.isArray(data.data?.category_history)
                    ? data.data.category_history
                    : [];

                // 🔹 Set activeCategory dari people.category_id jika tersedia
                const peopleData = data.data?.people;
                if (peopleData && peopleData.category_id) {
                    const currentCat = this.categories.find(c => c.id === peopleData.category_id);
                    this.activeCategory = currentCat ?? { id: null, name: '-', price: 0 };
                } else if (this.history.length > 0) {
                    // fallback ke riwayat terakhir
                    const last = this.history[this.history.length - 1];
                    this.activeCategory = last.category ?? { name: last.category_name, price: last.price };
                }

                // Tutup mode edit otomatis
                this.editMode = false;
                this.selectedCategory = '';

                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'success',
                        title: 'Kategori Diperbarui',
                        message: data.message || 'Kategori berhasil diubah.'
                    }
                }));

            } catch (error) {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'error',
                        title: 'Gagal',
                        message: error.message || 'Terjadi kesalahan saat memperbarui kategori.'
                    }
                }));
            } finally {
                this.loading = false;
            }
        }

    }));
});
</script>

@endpush
