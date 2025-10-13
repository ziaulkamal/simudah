<div
    x-data="wilayahDropdown()"
    x-init="init()"
    class="space-y-4">

    <input type="hidden" name="provinceId" x-model="selectedProvince">
    <input type="hidden" name="regencieId" x-model="selectedRegency">
    <input type="hidden" name="districtId" x-model="selectedDistrict">
    <input type="hidden" name="villageId" x-model="selectedVillage">

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Provinsi -->
        <label class="block">
            <span>Provinsi</span>
            <select x-model="selectedProvince" @change="loadRegencies()"
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
            <select x-model="selectedRegency" @change="loadDistricts()"
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
            <select x-model="selectedDistrict" @change="loadVillages()"
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
            <select x-model="selectedVillage"
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

<script>
function wilayahDropdown() {
    return {
        provinces: {},
        regencies: {},
        districts: {},
        villages: {},
        selectedProvince: '',
        selectedRegency: '',
        selectedDistrict: '',
        selectedVillage: '',
        streetAddress: '',

        async init() {
            const res = await fetch('/api/provinces');
            this.provinces = await res.json();

            document.addEventListener('updateWilayah', async (event) => {
                const data = event.detail;
                if (!data) {
                    // reset semua
                    this.selectedProvince = '';
                    this.selectedRegency = '';
                    this.selectedDistrict = '';
                    this.selectedVillage = '';
                    this.streetAddress = '';
                    return;
                }

                try {
                    if (data.province) {
                        this.selectedProvince = data.province;
                        await this.loadRegencies();
                    }
                    if (data.regency) {
                        this.selectedRegency = data.regency;
                        await this.loadDistricts();
                    }
                    if (data.district) {
                        this.selectedDistrict = data.district;
                        await this.loadVillages();
                    }
                    if (data.village) this.selectedVillage = data.village;

                    this.streetAddress = data.line || '';
                } catch (err) {
                    console.error('Gagal set data wilayah:', err);
                }
            });
        },

        async loadRegencies() {
            if (!this.selectedProvince) return;
            const res = await fetch(`/api/regencies/${this.selectedProvince}`);
            this.regencies = await res.json();
            this.districts = {};
            this.villages = {};
        },

        async loadDistricts() {
            if (!this.selectedRegency) return;
            const res = await fetch(`/api/districts/${this.selectedRegency}`);
            this.districts = await res.json();
            this.villages = {};
        },

        async loadVillages() {
            if (!this.selectedDistrict) return;
            const res = await fetch(`/api/villages/${this.selectedDistrict}`);
            this.villages = await res.json();
        }
    };
}
</script>