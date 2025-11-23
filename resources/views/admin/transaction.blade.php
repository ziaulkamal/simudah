@extends('layouts.app')

@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8">
    <x-breadcrumb-header :title="$title" :submenu="$submenu" />

    <div x-data="transactionsTable()" x-init="init()" class="mt-4">

        <!-- 🔹 Filter & Search (tetap dari kode awal) -->
        <div x-data="{isFilterExpanded:false}">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">
                    Data Transaksi
                </h2>
                <div class="flex">
                    <!-- Search & Filter button tetap sama -->
                </div>
            </div>

            <!-- Filter panel tetap sama -->

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
                                    <td colspan="8" class="text-center py-6 text-slate-500">Memuat data...</td>
                                </tr>
                            </template>

                            <template x-if="!loading && transactions.length === 0">
                                <tr>
                                    <td colspan="8" class="text-center py-6 text-slate-500">Tidak ada data ditemukan</td>
                                </tr>
                            </template>

                            <template x-for="item in transactions" :key="item.id">
                                <tr class="border-b border-gray-200 cursor-pointer hover:bg-gray-50"
                                    @click="openInvoiceModal(item)">
                                    <td class="px-4 py-3" x-text="item.transaction_code"></td>
                                    <td class="px-4 py-3" x-text="item.people.district ?? '-'"></td>
                                    <td class="px-4 py-3" x-text="item.people.village ?? '-'"></td>
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
                                    <td class="px-4 py-3" x-text="item.due_date"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination tetap sama -->
            </div>
        </div>

<x-invoice />
    </div>
</main>
@endsection
@push('scripts')

<script>
function transactionsTable() {
    return {
        loading: true,
        transactions: [],
        meta: {},
        search: '',
        filter: { status: '', category_id: '', provinceId: 11, regencieId: 1112, districtId: '', villageId: '' },

        // 🔹 Modal state
        openInvoice: false,
        invoiceData: null,

        init() {
            this.fetchData();
            window.addEventListener('setDistrict', e => this.filter.districtId = e.detail);
            window.addEventListener('setVillage', e => this.filter.villageId = e.detail);
        },

        fetchData(page = 1) {
            this.loading = true;
            fetch(`/api/transactions/all?page=${page}&status=${this.filter.status}&category_id=${this.filter.category_id}&provinceId=11&regencieId=1112&districtId=${this.filter.districtId}&villageId=${this.filter.villageId}&search=${this.search}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        this.transactions = data.data;
                        this.meta = data.meta;
                    }
                })
                .finally(() => this.loading = false);
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value || 0);
        },

        // 🔹 Modal functions
        openInvoiceModal(item) {
            this.invoiceData = null;
            this.invoiceLoading = true;
            this.openInvoice = true;

            // Fetch invoice dari route berdasarkan transaction_code
            fetch(`/api/transactions/${item.transaction_code}/invoice`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success && data.data) {
                    this.invoiceData = data.data.transaction; // pastikan struktur sesuai API
                } else {
                    this.invoiceData = null;
                }
            })
            .catch(() => {
                this.invoiceData = null;
            })
            .finally(() => {
                this.invoiceLoading = false;
            });
        },

        closeInvoiceModal() {
            this.invoiceData = null;
            this.openInvoice = false;
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
@endpush

