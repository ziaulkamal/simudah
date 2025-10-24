@extends('layouts.app')

{{-- @dd($people) --}}

@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8">
    <x-breadcrumb-header
    title="{{ $title }}"
    submenu="{{ $submenu }}"
    route-name="customer.index"
    route-label="Pelanggan"
/>


    <div x-data="{ activeTab: 'detail' }" @change-tab.window="activeTab = $event.detail">
        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            <!-- Sidebar -->
            <div class="col-span-12 lg:col-span-4">
                <div class="card p-4 sm:p-5">
                    <div class="flex items-center space-x-4">
                        <div class="avatar size-14">
                            <img class="rounded-full" src="{{ asset('images/200x200.png') }}" alt="avatar" />
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                {{ strtoupper($people->fullName) ?? 'Customer Name' }}
                            </h3>
                            <p class="text-xs-plus text-slate-500">Pelanggan</p>
                        </div>
                    </div>

                    <x-customer.menu />
                </div>
            </div>

            <!-- Main content -->
            <div class="col-span-12 lg:col-span-8">
                <div class="card">

                    <!-- DETAIL INFORMASI -->
                    <div x-show="activeTab === 'detail'" class="p-4 sm:p-5">
                        @include('admin.customers.tabs.detail')
                    </div>

                    <!-- TRANSAKSI -->
                    <div x-show="activeTab === 'transaksi'" class="p-4 sm:p-5">
                        @include('admin.customers.tabs.transaksi')
                    </div>

                    <!-- LOKASI -->
                    <div x-show="activeTab === 'lokasi'"
                        x-init="
                            $watch('activeTab', value => {
                                if(value === 'lokasi' && window.lokasiPelangganInstance?.lokasiSet) {
                                    $nextTick(() => window.lokasiPelangganInstance.initMainMap());
                                }
                            })
                        "
                        class="p-4 sm:p-5">
                        @include('admin.customers.tabs.lokasi')
                    </div>


                    <!-- Atur Kategori -->
                    <div x-show="activeTab === 'kategori'" class="p-4 sm:p-5">
                        @include('admin.customers.tabs.category')
                    </div>

                    <!-- TUTUP AKUN -->
                    <div x-show="activeTab === 'tutup'" class="p-4 sm:p-5">
                        @include('admin.customers.tabs.tutup')
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection