<div x-data="peopleForm({{ json_encode($people) }})" class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4 dark:border-navy-500">
        <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">{{ $title }}</h2>
        <button
            @click="editMode = !editMode"
            class="btn rounded-full border border-slate-300 text-slate-600 hover:bg-slate-100
                   dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600">
            <template x-if="!editMode">
                <span class="flex items-center space-x-2">
                    <i class="fa-regular fa-pen-to-square"></i>
                    <span>Edit</span>
                </span>
            </template>
            <template x-if="editMode">
                <span class="flex items-center space-x-2 text-primary">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Batal</span>
                </span>
            </template>
        </button>
    </div>

    <!-- Form Edit -->
    <form x-show="editMode" x-transition
      @submit.prevent="saveData"
      class="space-y-6" id="peopleForm">
        @csrf
        @method('PUT')

        <!-- Informasi Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-form-input name="fullName" label="Nama Lengkap"
            icon="fa-regular fa-user"
            :value="strtoupper($people->fullName)" class="uppercase" />

            <x-form-input name="email" label="Email"
                icon="fa-regular fa-envelope" :value="$people->email" />

            <x-form-input name="phoneNumber" label="Nomor Telepon"
                icon="fa-solid fa-phone" :value="$people->phoneNumber" />

            <x-form-input name="identityNumber" label="Nomor Identitas (NIK)"
                readonly :value="$people->identityNumber" />

            <x-form-input name="familyIdentityNumber" label="Nomor KK (Kartu Keluarga)"
                icon="fa-solid fa-id-card" :value="$people->familyIdentityNumber" />

            <!-- Jenis Kelamin -->
            <x-select-dropdown
                name="gender"
                label="Jenis Kelamin"
                :options="['male' => 'Laki-laki', 'female' => 'Perempuan']"
                :selected="old('gender', $people->gender ?? null)" />

            <x-form-input
                name="birthdate"
                label="Tanggal Lahir"
                type="date"
                icon="fa-regular fa-calendar"
                :value="$people->birthdate" />

            <!-- Agama -->
            <x-select-dropdown
                name="religion"
                label="Agama"
                :options="[
                    '1' => 'Islam',
                    '2' => 'Kristen',
                    '3' => 'Katolik',
                    '4' => 'Hindu',
                    '5' => 'Buddha',
                    '6' => 'Konghucu'
                ]"
                :selected="old('religion', $people->religion ?? null)" />
        </div>

        <!-- Wilayah dan Alamat -->
        <div
            x-data="wilayahDropdown({
                province: '{{ $people->provinceId ?? '' }}',
                regency: '{{ $people->regencieId ?? '' }}',
                district: '{{ $people->districtId ?? '' }}',
                village: '{{ $people->villageId ?? '' }}',
                line: '{{ $people->streetAddress ?? '' }}'
            })"
            x-init="initWilayah()"
            class="space-y-4">

            <input type="hidden" name="provinceId" x-model="selectedProvince">
            <input type="hidden" name="regencieId" x-model="selectedRegency">
            <input type="hidden" name="districtId" x-model="selectedDistrict">
            <input type="hidden" name="villageId" x-model="selectedVillage">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Provinsi -->
                <label class="block">
                    <span>Provinsi</span>
                    <select
                        x-model="selectedProvince"
                        @change="onProvinceChange"
                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                        <option value="">-- Pilih Provinsi --</option>
                        <template x-for="(name, id) in provinces" :key="id">
                            <option :value="id" x-text="name"></option>
                        </template>
                    </select>
                </label>

                <!-- Kabupaten -->
                <label class="block">
                    <span>Kabupaten / Kota</span>
                    <select
                        x-model="selectedRegency"
                        @change="onRegencyChange"
                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        <template x-for="(name, id) in regencies" :key="id">
                            <option :value="id" x-text="name"></option>
                        </template>
                    </select>
                </label>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Kecamatan -->
                <label class="block">
                    <span>Kecamatan</span>
                    <select
                        x-model="selectedDistrict"
                        @change="onDistrictChange"
                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                        <option value="">-- Pilih Kecamatan --</option>
                        <template x-for="(name, id) in districts" :key="id">
                            <option :value="id" x-text="name"></option>
                        </template>
                    </select>
                </label>

                <!-- Desa -->
                <label class="block">
                    <span>Desa / Kelurahan</span>
                    <select
                        x-model="selectedVillage"
                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                        <option value="">-- Pilih Desa/Kelurahan --</option>
                        <template x-for="(name, id) in villages" :key="id">
                            <option :value="id" x-text="name"></option>
                        </template>
                    </select>
                </label>
            </div>

            <!-- Alamat Lengkap -->
            <label class="block">
                <span>Alamat Lengkap</span>
                <textarea name="streetAddress" x-model="streetAddress" rows="3"
                    placeholder="Nama jalan, RT/RW, dan area sekitar"
                    class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent p-2.5"></textarea>
            </label>
        </div>

        <!-- Tombol Simpan -->
        <div class="flex justify-end space-x-3 border-t border-slate-200 pt-4 dark:border-navy-500">
            <button type="button"
                @click="editMode = false"
                class="btn rounded-full border border-slate-300 text-slate-600 hover:bg-slate-100 dark:border-navy-500 dark:text-navy-100">
                Batal
            </button>
            <button type="submit"
                class="btn rounded-full bg-primary text-white hover:bg-primary-focus focus:ring-2 focus:ring-primary/50">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <!-- Detail View -->
    <div x-show="!editMode" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Nama Lengkap -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Nama Lengkap</label>
            <p x-text="person.fullName?.toUpperCase()"
            class="mt-1 text-slate-700 dark:text-navy-100 uppercase"></p>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Email</label>
            <p x-text="person.email || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Nomor Telepon -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Nomor Telepon</label>
            <p x-text="person.phoneNumber || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- NIK -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Nomor Identitas (NIK)</label>
            <p class="mt-1 text-slate-700 dark:text-navy-100">{{ $people->identityNumber ?? '-' }}</p>
        </div>

        <!-- Nomor KK -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Nomor KK</label>
            <p class="mt-1 text-slate-700 dark:text-navy-100">{{ $people->familyIdentityNumber ?? '-' }}</p>
        </div>

        <!-- Jenis Kelamin -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Jenis Kelamin</label>
            <p x-text="person.gender === 'male' ? 'Laki-laki' : (person.gender === 'female' ? 'Perempuan' : '-')"
            class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Tanggal Lahir -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Tanggal Lahir</label>
            <p x-text="person.birthdate ? new Date(person.birthdate).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'"
            class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Usia -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Usia</label>
            <p x-text="person.birthdate ? Math.floor((new Date() - new Date(person.birthdate)) / (365.25 * 24 * 60 * 60 * 1000)) + ' tahun' : '-'"
            class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Agama -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Agama</label>
            <p x-text="({ '1':'Islam','2':'Kristen','3':'Katolik','4':'Hindu','5':'Buddha','6':'Konghucu' }[person.religion] || '-')"></p>
        </div>

        <!-- Alamat -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Alamat Lengkap</label>
            <p x-text="person.streetAddress || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Provinsi -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Provinsi</label>
            <p x-text="person.province?.name || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Kabupaten -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Kabupaten / Kota</label>
            <p x-text="person.regencie?.name || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Kecamatan -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Kecamatan</label>
            <p x-text="person.district?.name || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>

        <!-- Desa -->
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-navy-100">Desa / Kelurahan</label>
            <p x-text="person.village?.name || '-'" class="mt-1 text-slate-700 dark:text-navy-100"></p>
        </div>
    </div>

</div>
@push('scripts')
<x-modal />
<script>
function wilayahDropdown(initial) {
    return {
        provinces: {},
        regencies: {},
        districts: {},
        villages: {},
        selectedProvince: '',
        selectedRegency: '',
        selectedDistrict: '',
        selectedVillage: '',
        streetAddress: initial.line || '',

        async initWilayah() {
            const resProv = await fetch('/api/provinces', {
                headers: {
                    'Accept': 'application/json',
                },
            });
            this.provinces = await resProv.json();

            this.selectedProvince = initial.province || '';
            if (this.selectedProvince) {
                await this.loadRegencies(true);
                this.selectedRegency = initial.regency || '';
            }
            if (this.selectedRegency) {
                await this.loadDistricts(true);
                this.selectedDistrict = initial.district || '';
            }
            if (this.selectedDistrict) {
                await this.loadVillages(true);
                this.selectedVillage = initial.village || '';
            }
        },

        async onProvinceChange() {
            this.selectedRegency = '';
            this.selectedDistrict = '';
            this.selectedVillage = '';
            this.regencies = {};
            this.districts = {};
            this.villages = {};
            if (this.selectedProvince) await this.loadRegencies();
        },
        async onRegencyChange() {
            this.selectedDistrict = '';
            this.selectedVillage = '';
            this.districts = {};
            this.villages = {};
            if (this.selectedRegency) await this.loadDistricts();
        },
        async onDistrictChange() {
            this.selectedVillage = '';
            this.villages = {};
            if (this.selectedDistrict) await this.loadVillages();
        },

        async loadRegencies(isInit = false) {
            if (!this.selectedProvince) return;
            const res = await fetch(`/api/regencies/${this.selectedProvince}`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
            this.regencies = await res.json();
            if (!isInit) this.selectedRegency = '';
        },
        async loadDistricts(isInit = false) {
            if (!this.selectedRegency) return;
            const res = await fetch(`/api/districts/${this.selectedRegency}`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
            this.districts = await res.json();
            if (!isInit) this.selectedDistrict = '';
        },
        async loadVillages(isInit = false) {
            if (!this.selectedDistrict) return;
            const res = await fetch(`/api/villages/${this.selectedDistrict}`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
            this.villages = await res.json();
            if (!isInit) this.selectedVillage = '';
        },
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('peopleForm', (initial) => ({
        editMode: false,
        isSaving: false,
        person: { ...initial },

        async saveData() {
            this.isSaving = true;
            const form = document.getElementById('peopleForm');
            const formData = new FormData(form);

            try {
                const res = await fetch(`{{ route('people.update', $people->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok) {
                    // ❌ Tangani validasi 422 khusus
                    if (res.status === 422 && data.errors) {
                        // Gabungkan semua error menjadi string
                        let messages = Object.values(data.errors)
                            .flat() // flatten array
                            .join(' • '); // pisahkan dengan bullet

                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                type: 'error',
                                title: 'Validasi Gagal',
                                message: messages
                            }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                type: 'error',
                                title: data.message || 'Gagal Menyimpan Data',
                                message: data.message || 'Terjadi kesalahan.'
                            }
                        }));
                    }

                    return;
                }

                // ✅ Update objek person di tampilan
                this.person = { ...this.person, ...data };

                this.editMode = false;

                // ✅ Tampilkan modal sukses
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'success',
                        title: 'Sukses',
                        message: 'Data berhasil disimpan!'
                    }
                }));

            } catch (err) {
                console.error(err);

                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'error',
                        title: 'Kesalahan Sistem',
                        message: 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi nanti.'
                    }
                }));
            } finally {
                this.isSaving = false;
            }
        }

    }));
});

</script>
@endpush

