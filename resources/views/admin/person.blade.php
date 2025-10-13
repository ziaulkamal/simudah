@extends('layouts.app')

@section('content')

<main class="main-content w-full px-[var(--margin-x)] pb-8">
    <x-breadcrumb-header title="{{ $title }}" submenu="{{ $submenu }}" />

    <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
        <div x-data="{isFilterExpanded:false}">
        <x-filter-card title="Filter Pelanggan" action="{{ route('customer.index') }}">
            <!-- NIK -->
            <label class="block">
                <span>NIK:</span>
                <input name="nik" value="{{ request('nik') }}"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary"
                    placeholder="Cari NIK..." type="text" />
            </label>

            <!-- Kategori -->
            <label class="block">
                <span>Kategori:</span>
                <select name="category_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </label>

            <!-- Kecamatan & Desa -->
            <label class="block" x-data="districtVillage()">
                <span>Kecamatan:</span>
                <select x-model="selectedDistrict" name="district_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary">
                    <option value="">Semua</option>
                    @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected(request('district_id')==$district->id)>
                        {{ $district->name }}
                    </option>
                    @endforeach
                </select>

                <span class="mt-4 block">Desa:</span>
                <select x-model="selectedVillage" name="village_id" class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary">
                    <option value="">Semua</option>
                    <template x-for="village in villages" :key="village.id">
                        <option :value="village.id" x-text="village.name" :selected="village.id == '{{ request('village_id') }}'"></option>
                    </template>
                </select>
            </label>
        </x-filter-card>



            {{-- TABLE --}}
            <div class="card mt-3">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                            <tr>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">#</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Nama Lengkap
                                </th>

                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Alamat</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Kecamatan</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Desa</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Telepon</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Kategori</th>
                                <th class="bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peoples as $person)
                            <tr class="border-b border-slate-200 dark:border-navy-500">
                                {{-- Nomor urut berlanjut antar halaman --}}
                                <td class="px-4 py-3">
                                    {{ $peoples->firstItem() + $loop->index }}
                                </td>
                                <td
                                    class="px-4 py-3 font-medium text-slate-700 dark:text-navy-50 hover:text-primary dark:hover:text-accent">
                                    {{ $person->fullName }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $person->streetAddress }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $person->village->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $person->district->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $person->phoneNumber }}
                                </td>
                                {{-- Kategori --}}
                                <td class="px-4 py-3">
                                    <x-category-badge :category="$person->category" />
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <a href="#" class="btn space-x-2 bg-info font-medium text-white hover:bg-info-focus hover:shadow-lg hover:shadow-info/50 focus:bg-info-focus focus:shadow-lg focus:shadow-info/50 active:bg-info-focus/90" x-tooltip.placement.right="'Selengkapnya'">
                                        <span class="flex items-center justify-center w-full h-full bg-purple text-pink-500 rounded-[inherit]">
                                            <i class="fa-solid fa-eye"></i>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-slate-500">
                                    Tidak ada data ditemukan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                {{-- Pagination --}}
                <div
                    class="text-xs-plus flex justify-center sm:justify-end text-center sm:text-right text-slate-600 dark:text-navy-100 sm:pr-[calc(var(--margin-x)*-1)]">
                    {{ $peoples->links('components.paginations') }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

<script>
    function districtVillage() {
        return {
            selectedDistrict: "{{ request('district_id') }}",
            selectedVillage: "{{ request('village_id') }}",
            villages: [],
            async fetchVillages() {
                if (!this.selectedDistrict) {
                    this.villages = [];
                    return;
                }
                try {
                    const res = await fetch(`/villages/${this.selectedDistrict}`);
                    const data = await res.json();
                    // Konversi data menjadi array objek {id, name}
                    this.villages = Object.entries(data).map(([name, id]) => ({
                        id,
                        name
                    }));
                } catch (e) {
                    this.villages = [];
                }
            },
            init() {
                if (this.selectedDistrict) {
                    this.fetchVillages();
                }
                this.$watch('selectedDistrict', () => {
                    this.selectedVillage = ''; // Reset pilihan desa saat kecamatan berubah
                    this.fetchVillages();
                });
            }
        }
    }
</script>