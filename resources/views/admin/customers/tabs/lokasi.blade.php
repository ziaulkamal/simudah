<div x-data="lokasiPelanggan({{
    json_encode([
        'latitude' => optional($people->location)->latitude ?? 3.8167,
        'longitude' => optional($people->location)->longitude ?? 96.8333,
        'alamat' => optional($people->location)->address ?? 'Aceh Barat Daya',
        'province' => optional($people->location)->province_name ?? '',
        'regency' => optional($people->location)->regency_name ?? '',
        'district' => optional($people->location)->district_name ?? '',
        'village' => optional($people->location)->village_name ?? '',
        'lokasiSet' => $people->location ? true : false,
        'peopleId' => $people->id ?? null,
    ])
}})" x-cloak class="space-y-4">
  <div class="flex items-center justify-between border-b border-slate-200 pb-4 dark:border-navy-500">
    <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Lokasi Pelanggan</h2>
    <button @click="bukaModal"
      class="btn rounded-full border border-slate-300 text-slate-600 hover:bg-slate-100
             dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600">
      <span class="flex items-center space-x-2">
        <i class="fa-solid fa-map-location-dot"></i>
        <span x-text="lokasiSet ? 'Ubah Lokasi' : 'Atur Lokasi'"></span>
      </span>
    </button>
  </div>

  <template x-if="lokasiSet">
    <div class="space-y-2">
      <div id="map" class="h-80 w-full rounded-lg border border-slate-200 dark:border-navy-500 overflow-hidden"></div>
      <p class="text-sm text-slate-600 dark:text-navy-100">
        📍 <span x-text="alamat"></span>
      </p>
    </div>
  </template>

  <!-- Modal -->
  <div x-show="showModal"
       x-transition
       class="fixed inset-0 flex items-center justify-center z-50"
       style="backdrop-filter: blur(6px);"
       @keydown.window.escape="tutupModal">
    <div class="absolute inset-0 bg-black/30" @click="tutupModal"></div>

    <div @click.stop
         class="relative bg-white dark:bg-navy-700 rounded-2xl p-5 w-full max-w-lg shadow-2xl space-y-4 transform transition-all z-60">
      <h3 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Atur Lokasi</h3>

      <!-- Tombol GPS / Manual -->
      <div class="flex gap-2">
        <button @click="deteksiGPS"
          class="flex-1 btn rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100
                 dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600">
          <i class="fa-solid fa-location-crosshairs mr-2"></i> Deteksi Otomatis
        </button>
        <button @click="modeManual"
          class="flex-1 btn rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100
                 dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600">
          <i class="fa-regular fa-map mr-2"></i> Susun Manual
        </button>
      </div>

      <!-- Mode Manual -->
      <div x-show="manualMode" x-transition class="mt-3 space-y-2">
        <!-- Group Input + Tombol Cari -->
        <div class="flex items-center gap-2">
          <input type="text" x-model="searchQuery" placeholder="Cari alamat..."
            @keydown.enter.prevent="geocodeAlamat"
            class="flex-1 rounded-lg border border-slate-300 dark:border-navy-500 px-3 py-2
                   focus:outline-none focus:ring focus:ring-blue-200 dark:bg-navy-600 dark:text-navy-100
                   sm:text-base text-sm min-w-0 w-[80%] xs:w-[85%]">
          <button @click="geocodeAlamat"
            class="btn rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100
                   dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600
                   px-3 sm:px-4 text-sm sm:text-base shrink-0">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </div>

        <!-- Peta Manual -->
        <div id="mapManual" class="h-64 w-full rounded-lg border border-slate-300 dark:border-navy-500 overflow-hidden"></div>

        <!-- Tombol Simpan Manual (lokal) -->
        {{-- <button @click="simpanManual"
          class="btn w-full rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100
                 dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600 mb-2">
          <i class="fa-solid fa-check mr-2"></i> Simpan Lokasi
        </button> --}}

        <!-- Tombol Kirim ke Server -->
        <button @click="simpanKeServer"
          class="btn w-full rounded-lg bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
          <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan ke Server
        </button>
      </div>

      <button @click="tutupModal"
        class="btn w-full rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100
               dark:border-navy-500 dark:text-navy-100 dark:hover:bg-navy-600">
        <i class="fa-solid fa-xmark mr-2"></i> Tutup
      </button>
    </div>
  </div>
</div>

<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<!-- Responsif input kecil di layar mobile -->
<style>
  @media (max-width: 310px) {
    input[type="text"][x-model="searchQuery"] {
      width: 75% !important;
      font-size: 0.8rem !important;
      padding-left: 0.5rem !important;
      padding-right: 0.5rem !important;
    }
    button.btn {
      padding-left: 0.5rem !important;
      padding-right: 0.5rem !important;
    }
  }

  /* pastikan layer leaflet tidak meng-overlap modal z-index */
  .leaflet-container { z-index: 0; }
  .leaflet-pane { z-index: 0; }
</style>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('lokasiPelanggan', (initData = {}) => ({
    lokasiSet: initData.lokasiSet || false,
    showModal: false,
    manualMode: false,
    latitude: parseFloat(initData.latitude) || 3.8167,
    longitude: parseFloat(initData.longitude) || 96.8333,
    alamat: initData.alamat || 'Aceh Barat Daya',
    searchQuery: '',
    province: initData.province || '',
    regency: initData.regency || '',
    district: initData.district || '',
    village: initData.village || '',
    map: null,
    mapManual: null,
    mainMarker: null,
    manualMarker: null,
    peopleId: initData.peopleId || null,

    // --------------------------
    init() {
        // Simpan instance global untuk dipanggil dari luar (tab switch)
        window.lokasiPelangganInstance = this;

        // Jika lokasi sudah ada, inisialisasi map
        if(this.lokasiSet && document.querySelector('#map')) {
            this.$nextTick(() => this.initMainMap());
        }
    },
    // Map Utama
    initMainMap() {
        const mapContainer = document.getElementById('map');
        if (!mapContainer) return;

        // Gunakan delay untuk pastikan DOM sudah terlihat
        setTimeout(() => {
            if(!this.map) {
                this.map = L.map('map', { zoomControl: true }).setView([this.latitude, this.longitude], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);
                this.mainMarker = L.marker([this.latitude, this.longitude]).addTo(this.map);
            } else {
                this.updateMainMarker();
                this.map.invalidateSize(); // penting, supaya map render penuh
            }
        }, 200);
    },

    async geocodeAlamat() {
        if (!this.searchQuery || this.searchQuery.trim() === '') {
            this.showAlert('warning', 'Cari Alamat', 'Masukkan alamat untuk mencari.');
            return;
        }

        try {
            const query = encodeURIComponent(this.searchQuery);
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${query}`;
            const res = await fetch(url);
            const results = await res.json();

            if (results.length === 0) {
                this.showAlert('warning', 'Alamat Tidak Ditemukan', 'Coba gunakan kata kunci lain.');
                return;
            }

            const loc = results[0];
            this.latitude = parseFloat(loc.lat);
            this.longitude = parseFloat(loc.lon);
            this.alamat = loc.display_name;

            // Perbarui marker di mapManual
            if (this.mapManual) {
                if (this.manualMarker) this.mapManual.removeLayer(this.manualMarker);
                this.manualMarker = L.marker([this.latitude, this.longitude], { draggable: true }).addTo(this.mapManual);
                this.mapManual.setView([this.latitude, this.longitude], 16);

                // Event dragend untuk marker baru
                this.manualMarker.on('dragend', async (e) => {
                    const pos = e.target.getLatLng();
                    this.latitude = pos.lat;
                    this.longitude = pos.lng;
                    await this.reverseGeocode(pos.lat, pos.lng);
                });
            }

            // Update alamat dan reverse geocode agar field lainnya terisi
            await this.reverseGeocode(this.latitude, this.longitude);

        } catch (error) {
            console.error(error);
            this.showAlert('error', 'Kesalahan Sistem', 'Terjadi kesalahan saat mencari alamat.');
        }
    },


    updateMainMarker() {
      if (!this.map) return;
      if (this.mainMarker) this.map.removeLayer(this.mainMarker);
      this.mainMarker = L.marker([this.latitude, this.longitude]).addTo(this.map);
      this.map.setView([this.latitude, this.longitude], 12);
    },

    // --------------------------
    // Modal
    bukaModal() {
      this.showModal = true;
      this.$nextTick(() => {
        setTimeout(() => this.initMainMap(), 200);
      });
    },

    tutupModal() {
      this.showModal = false;
      this.manualMode = false;
      setTimeout(() => this.map?.invalidateSize(), 300);
    },

    // --------------------------
    // Mode Manual
    modeManual() {
      this.manualMode = true;
      this.$nextTick(() => {
        setTimeout(() => {
          const mapContainer = document.getElementById('mapManual');
          if (!mapContainer) return;

          if (!this.mapManual) {
            this.mapManual = L.map('mapManual', { zoomControl: true }).setView([this.latitude, this.longitude], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; OpenStreetMap'
            }).addTo(this.mapManual);

            this.manualMarker = L.marker([this.latitude, this.longitude], { draggable: true }).addTo(this.mapManual);
            this.manualMarker.on('dragend', async (e) => {
              const pos = e.target.getLatLng();
              this.latitude = pos.lat;
              this.longitude = pos.lng;
              await this.reverseGeocode(pos.lat, pos.lng);
            });

            this.mapManual.on('click', async (e) => {
              if (this.manualMarker) this.mapManual.removeLayer(this.manualMarker);
              this.manualMarker = L.marker(e.latlng, { draggable: true }).addTo(this.mapManual);
              this.latitude = e.latlng.lat;
              this.longitude = e.latlng.lng;
              await this.reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
          } else {
            this.mapManual.setView([this.latitude, this.longitude], 12);
            if (this.manualMarker) this.mapManual.removeLayer(this.manualMarker);
            this.manualMarker = L.marker([this.latitude, this.longitude], { draggable: true }).addTo(this.mapManual);
          }

          setTimeout(() => this.mapManual.invalidateSize(), 200);
        }, 200);
      });
    },

    // --------------------------
    // Deteksi GPS
    deteksiGPS() {
      if (!navigator.geolocation) {
        this.modeManual();
        this.showAlert('warning', 'GPS Tidak Didukung', 'Browser tidak mendukung deteksi lokasi otomatis.');
        return;
      }
      navigator.geolocation.getCurrentPosition(
        async (pos) => {
          this.latitude = pos.coords.latitude;
          this.longitude = pos.coords.longitude;
          await this.reverseGeocode(this.latitude, this.longitude);
          this.lokasiSet = true;
          this.updateMainMarker();
          this.tutupModal();
        },
        (err) => {
          this.modeManual();
          this.showAlert('error', 'Gagal Deteksi Lokasi', err.message || 'Terjadi kesalahan saat mengakses lokasi.');
        }
      );
    },

    // --------------------------
    // Reverse Geocode
    async reverseGeocode(lat, lon) {
      try {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;
        const res = await fetch(url);
        const data = await res.json();

        this.alamat = data.display_name || `Lat: ${lat.toFixed(4)}, Lng: ${lon.toFixed(4)}`;
        const addr = data.address || {};
        this.province = addr.state || '';
        this.regency = addr.county || '';
        this.district = addr.municipality || '';
        this.village = addr.village || '';

      } catch (error) {
        this.alamat = `Lat: ${lat.toFixed(4)}, Lng: ${lon.toFixed(4)}`;
        this.province = this.regency = this.district = this.village = '';
      }
    },

    // --------------------------
    // Simpan Lokal & Server
    simpanManual() {
      if (!this.latitude || !this.longitude) {
        this.showAlert('warning', 'Belum Ada Lokasi', 'Silakan pilih lokasi terlebih dahulu di peta.');
        return;
      }
      this.lokasiSet = true;
      this.initMainMap();
      this.updateMainMarker();
      this.showAlert('success', 'Lokasi Tersimpan (Sementara)', 'Lokasi telah diperbarui secara lokal. Klik "Simpan ke Server" untuk menyimpan permanen.');
    },

    async simpanKeServer() {
      if (!this.peopleId) {
        this.showAlert('warning', 'Data Tidak Lengkap', 'ID pelanggan tidak ditemukan.');
        return;
      }
      try {
        const res = await fetch(`/api/people/${this.peopleId}/location`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({
            latitude: this.latitude,
            longitude: this.longitude,
            address: this.alamat,
            province: this.province,
            regency: this.regency,
            district: this.district,
            village: this.village,
          }),
        });
        let data = {};
        try { data = await res.json(); } catch(e) { data = {}; }

        if (res.ok) {
          this.lokasiSet = true;
          this.$nextTick(() => this.initMainMap());
          this.updateMainMarker();

          this.tutupModal();
          this.showAlert('success', 'Lokasi Disimpan', data.message || 'Data lokasi berhasil disimpan ke server.');
        } else {
          this.showAlert('error', 'Gagal Simpan', data.message || 'Terjadi kesalahan saat menyimpan data lokasi.');
        }
      } catch (error) {
        this.showAlert('error', 'Kesalahan Sistem', 'Tidak dapat terhubung ke server. Silakan coba lagi.');
      }
    },

    showAlert(type, title, message) {
      console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
      window.dispatchEvent(new CustomEvent('show-alert', { detail: { type, title, message } }));
    }

  }));
});
</script>


