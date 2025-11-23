@extends('layouts.app')

@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8">
    <x-breadcrumb-header :title="$title" :submenu="$submenu" />

    <div x-data="transactionsTable()" x-init="init()" class="mt-4">
        <div x-data="{isFilterExpanded:false}">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">
                    Data Transaksi
                </h2>
                <div class="flex">
                    <div class="flex items-center" x-data="{isInputActive:false}">
                        <div class="relative">
                            <input
                                x-model="search"
                                x-show="isInputActive"
                                x-transition
                                x-ref="searchInput"
                                @keydown.enter="fetchData()"
                                class="form-input bg-transparent px-2 w-48 text-right transition-all duration-200 placeholder:text-slate-500 dark:placeholder:text-navy-200"
                                placeholder="Cari transaksi..."
                                type="text"
                            />
                        </div>

                        <button
                            @click="
                                isInputActive = !isInputActive;
                                if (isInputActive) $nextTick(() => $refs.searchInput.focus());
                            "
                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 dark:hover:bg-navy-300/20 ml-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    <button
                        @click="isFilterExpanded = !isFilterExpanded"
                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 dark:hover:bg-navy-300/20 ml-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                d="M18 11.5H6M21 4H3m6 15h6" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 🔽 FILTER PANEL -->
            <div x-show="isFilterExpanded" x-collapse>
                <div class="max-w-3xl py-3">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">
                        <label class="block">
                            <span>Status:</span>
                            <select
                                x-model="filter.status"
                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary"
                            >
                                <option value="">Semua</option>
                                <option value="paid">Lunas</option>
                                <option value="pending">Menunggu</option>
                                <option value="cancelled">Dibatalkan</option>
                                <option value="expired">Kadaluarsa</option>
                            </select>
                        </label>

                        <label class="block">
                            <span>Kategori:</span>
                            <select
                                x-model="filter.category_id"
                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary"
                            >
                                <option value="">Semua</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <!-- 🌍 FILTER WILAYAH: hanya kecamatan & desa -->
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="wilayahDropdown()" x-init="init()">
                        <input type="hidden" x-model="selectedProvince" value="11">
                        <input type="hidden" x-model="selectedRegency" value="1112">

                        <label class="block">
                            <span>Kecamatan:</span>
                            <select x-model="selectedDistrict" @change="loadVillages(); $dispatch('setDistrict', selectedDistrict)"
                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
                                <option value="">Semua</option>
                                <template x-for="(name, id) in districts" :key="id">
                                    <option :value="id" x-text="name"></option>
                                </template>
                            </select>
                        </label>

                        <label class="block">
                            <span>Desa:</span>
                            <select x-model="selectedVillage" @change="$dispatch('setVillage', selectedVillage)"
                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
                                <option value="">Semua</option>
                                <template x-for="(name, id) in villages" :key="id">
                                    <option :value="id" x-text="name"></option>
                                </template>
                            </select>
                        </label>
                    </div>

                    <div class="mt-4 text-right space-x-2">
                        <button @click="resetFilter()" class="btn font-medium text-slate-700 hover:bg-slate-300/20 dark:text-navy-100">
                            Reset
                        </button>
                        <button @click="applyFilter()" class="btn bg-primary text-white hover:bg-primary-focus dark:bg-accent">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- 🔹 TABLE DATA -->
            <div class="card mt-3">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                            <tr>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Kode Transaksi</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Kecamatan</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Desa</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Kategori</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Jumlah</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Status</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Dibayar Pada</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="loading">
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-slate-500">Memuat data...</td>
                                </tr>
                            </template>

                            <template x-if="!loading && transactions.length === 0">
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-slate-500">Tidak ada data ditemukan</td>
                                </tr>
                            </template>

                            <template x-for="item in transactions" :key="item.id">
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-3" x-text="item.transaction_code"></td>
                                    <td class="px-4 py-3" x-text="`${item.people.district ?? '-'}`"></td>
                                    <td class="px-4 py-3" x-text="`${item.people.village ?? '-'}`"></td>
                                    <td class="px-4 py-3" x-text="item.category.name"></td>
                                    <td class="px-4 py-3" x-text="formatRupiah(item.amount)"></td>
                                    <td class="px-4 py-3">
                                        <template x-if="item.status === 'paid'">
                                            <div class="badge text-success"><i class="fa-solid fa-check-circle mr-1"></i> Lunas</div>
                                        </template>
                                        <template x-if="item.status === 'pending'">
                                            <div class="badge text-secondary"><i class="fa-solid fa-hourglass-half mr-1"></i> Menunggu</div>
                                        </template>
                                        <template x-if="item.status === 'cancelled'">
                                            <div class="badge text-error"><i class="fa-solid fa-ban mr-1"></i> Dibatalkan</div>
                                        </template>
                                        <template x-if="item.status === 'expired'">
                                            <div class="badge text-slate-500"><i class="fa-solid fa-clock mr-1"></i> Kadaluarsa</div>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3" x-text="item.paid_at"></td>
                                    <td class="px-4 py-3">
                                        <template x-if="getDueStatus(item.due_date).type === 'today'">
                                            <div class="badge text-info"><i class="fa-solid fa-calendar-day mr-1"></i> <span x-text="getDueStatus(item.due_date).label"></span></div>
                                        </template>
                                        <template x-if="getDueStatus(item.due_date).type === 'future'">
                                            <div class="badge text-info"><i class="fa-solid fa-clock mr-1"></i> <span x-text="getDueStatus(item.due_date).label"></span></div>
                                        </template>
                                        <template x-if="getDueStatus(item.due_date).type === 'past'">
                                            <div class="badge text-error"><i class="fa-solid fa-calendar-xmark mr-1"></i> <span x-text="getDueStatus(item.due_date).label"></span></div>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-4 py-4 text-xs-plus space-y-2 sm:space-y-0">
                    <div>
                        Menampilkan <span x-text="transactions.length"></span> dari <span x-text="meta.total"></span> data
                    </div>

                    <div class="flex items-center space-x-1">
                        <!-- Prev -->
                        <button
                            class="px-2 py-1 rounded border bg-white hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="meta.current_page === 1"
                            @click="fetchData(meta.current_page - 1)">
                            <i class="fa-solid fa-angle-left"></i>
                        </button>

                        <!-- Nomor Halaman -->
                        <template x-for="page in Array(meta.last_page).fill().map((_, i) => i + 1)" :key="page">
                            <button
                                class="px-3 py-1 rounded border hover:bg-primary hover:text-white"
                                :class="{'bg-primary text-white': page === meta.current_page, 'bg-white text-slate-700': page !== meta.current_page}"
                                @click="fetchData(page)"
                                x-text="page">
                            </button>
                        </template>

                        <!-- Next -->
                        <button
                            class="px-2 py-1 rounded border bg-white hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="meta.current_page === meta.last_page"
                            @click="fetchData(meta.current_page + 1)">
                            <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
function transactionsTable() {
    return {
        loading: true,
        transactions: [],
        meta: { total: 0, per_page: 0, current_page: 1, last_page: 1 },
        search: '',
        filter: { status: '', category_id: '', provinceId: 11, regencieId: 1112, districtId: '', villageId: '' },

        init() {
            this.fetchData();
            window.addEventListener('setDistrict', e => this.filter.districtId = e.detail);
            window.addEventListener('setVillage', e => this.filter.villageId = e.detail);
        },

        async fetchData(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    status: this.filter.status,
                    category_id: this.filter.category_id,
                    provinceId: 11,
                    regencieId: 1112,
                    districtId: this.filter.districtId,
                    villageId: this.filter.villageId,
                    search: this.search
                });
                const res = await fetch(`/api/transactions/all?${params.toString()}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.success) {
                    this.transactions = data.data;
                    this.meta = data.meta;
                }
            } catch (err) {
                console.error('Gagal memuat data transaksi:', err);
            } finally {
                this.loading = false;
            }
        },

        applyFilter() { this.fetchData(1); },
        resetFilter() {
            this.filter = { status: '', category_id: '', provinceId: 11, regencieId: 1112, districtId: '', villageId: '' };
            this.search = '';
            this.fetchData(1);
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value || 0);
        },

        getDueStatus(due_date) {
            if (!due_date || due_date === '-') return { label: '-', type: 'none' };
            const parts = due_date.split(/[\/\s:]/);
            if (parts.length < 3) return { label: '-', type: 'none' };
            const day = parseInt(parts[0]), month = parseInt(parts[1]) - 1, year = parseInt(parts[2]);
            const hour = parseInt(parts[3] || '0'), minute = parseInt(parts[4] || '0');
            const due = new Date(year, month, day, hour, minute);
            const now = new Date();
            const diff = Math.floor((due.setHours(0,0,0,0) - now.setHours(0,0,0,0)) / 86400000);
            if (diff === 0) return { label: 'Jatuh tempo hari ini', type: 'today' };
            if (diff > 0) return { label: `Jatuh tempo dalam ${diff} hari lagi`, type: 'future' };
            return { label: `Jatuh tempo ${Math.abs(diff)} hari yang lalu`, type: 'past' };
        }
    }
}

function wilayahDropdown() {
    return {
        districts: {}, villages: {},
        selectedProvince: 11, selectedRegency: 1112,
        selectedDistrict: '', selectedVillage: '',

        async init() {
            await this.loadDistricts();
        },
        async loadDistricts() {
            const res = await fetch(`/api/districts/${this.selectedRegency}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });
            this.districts = await res.json();
        },
        async loadVillages() {
            if (!this.selectedDistrict) return this.villages = {};
            const res = await fetch(`/api/villages/${this.selectedDistrict}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });
            this.villages = await res.json();
        }
    };
}
</script>
@endsection
