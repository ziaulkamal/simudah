@extends('layouts.auth')

@section('pages')
<div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak>

    <main class="grid w-full grow grid-cols-1 place-items-center">
        <div class="w-full max-w-[26rem] p-4 sm:px-5">

            <!-- LOGO -->
            <div class="text-center">
                <img class="mx-auto size-16" src="{{ asset('images/logo-simudah.png') }}" alt="logo" />
                <div class="mt-4">
                    <h2 class="text-2xl font-semibold text-slate-600 dark:text-navy-100">SIMUDAH</h2>
                    <p class="mt-2 text-base text-slate-500 dark:text-slate-300">
                        Sistem Informasi Manajemen Persampahan
                        <span class="text-green-600 font-medium">Mudah</span> &amp;
                        <span class="text-teal-500 font-medium">Terarah</span>
                    </p>
                </div>
            </div>

            <!-- CARD FORM -->
            <div class="card mt-10 w-full max-w-xl p-6 sm:p-8 bg-white dark:bg-navy-800 shadow-2xl rounded-2xl"
                 x-data="registerForm()" x-cloak>

                <!-- PREVIEW NAMA & NIK -->
                <div class="relative mx-auto -mt-16 h-40 w-72 rounded-xl text-white shadow-lg transition-transform hover:scale-105 lg:h-48 lg:w-80">
                    <div class="h-full w-full rounded-xl bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-400 flex flex-col justify-between p-4">
                        <div>
                            <p class="text-xs font-light">Nama</p>
                            <p class="font-semibold uppercase tracking-wide" x-text="form.name || 'Nama Anda'"></p>
                        </div>
                        <div>
                            <p class="text-xs font-light">NIK</p>
                            <p class="font-semibold tracking-wide" x-text="form.nik || '-'"></p>
                        </div>
                    </div>
                </div>

                <!-- HEADER FORM -->
                <div class="flex items-center justify-between py-5">
                    <p class="text-xl font-semibold text-slate-700 dark:text-slate-100">Pendaftaran Mandiri</p>
                    <div class="badge rounded-full border border-primary text-primary px-3 py-1 text-sm dark:border-accent-light dark:text-accent-light">
                        KTP
                    </div>
                </div>

                <!-- ERROR / SUCCESS MESSAGES -->
                @if ($errors->any())
                    <div class="p-3 mb-3 text-red-700 bg-red-100 border border-red-300 rounded-lg">
                        @foreach ($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="p-3 mb-3 text-green-700 bg-green-100 border border-green-300 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- FORM -->
                <form id="registerForm" @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">

                    @csrf

                    <!-- NAMA -->
                    <label class="block">
                        <span class="text-slate-700 dark:text-slate-100">Nama Lengkap</span>
                        <input type="text" name="fullName" x-model="form.name"
                               class="form-input mt-1.5 w-full rounded-lg bg-slate-150 px-3 py-2 ring-primary/50
                                      placeholder:text-slate-400 hover:bg-slate-200 focus:ring-2
                                      dark:bg-navy-900 dark:ring-accent/50 dark:placeholder:text-navy-300
                                      dark:hover:bg-navy-800 dark:focus:bg-navy-900"
                               placeholder="Nama Lengkap Sesuai KTP" required />
                    </label>

                    <!-- NIK -->
                    <label class="block">
                        <span class="text-slate-700 dark:text-slate-100">Nomor Induk Kependudukan (NIK)</span>
                        <input type="tel" name="identityNumber" x-model="form.nik"
                               @input="form.nik = form.nik.replace(/[^0-9]/g, '').slice(0,16)"
                               maxlength="16"
                               placeholder="Masukkan NIK"
                               class="form-input mt-1.5 w-full rounded-lg bg-slate-150 px-3 py-2 ring-primary/50
                                      placeholder:text-slate-400 hover:bg-slate-200 focus:ring-2
                                      dark:bg-navy-900 dark:ring-accent/50 dark:placeholder:text-navy-300
                                      dark:hover:bg-navy-800 dark:focus:bg-navy-900"
                               required />
                        <p class="text-xs text-slate-500 mt-1" x-text="form.nik.length + '/16 angka'"></p>
                    </label>

                    <!-- HP -->
                    <label class="block">
                        <span class="text-slate-700 dark:text-slate-100">Nomor HP</span>
                        <input type="tel" name="phoneNumber" x-model="form.phone"
                               @input="form.phone = form.phone.replace(/[^0-9]/g, '').slice(0,13)"
                               maxlength="13"
                               placeholder="Masukkan Nomor HP"
                               class="form-input mt-1.5 w-full rounded-lg bg-slate-150 px-3 py-2 ring-primary/50
                                      placeholder:text-slate-400 hover:bg-slate-200 focus:ring-2
                                      dark:bg-navy-900 dark:ring-accent/50 dark:placeholder:text-navy-300
                                      dark:hover:bg-navy-800 dark:focus:bg-navy-900"
                               required />
                        <p class="text-xs text-slate-500 mt-1" x-text="form.phone.length + '/13 angka'"></p>
                    </label>

                    <!-- KTP UPLOAD & CROPPER -->
                    <div x-data="ktpCropper()" class="space-y-4">
                        <label class="block">
                            <span class="text-slate-700 dark:text-slate-100">Upload atau Foto KTP</span>

                            <div class="relative mt-3 border-2 border-dashed border-slate-300 dark:border-navy-500 rounded-xl p-5 flex flex-col items-center justify-center bg-slate-50 dark:bg-navy-800/40 hover:border-fuchsia-500 transition duration-300 ease-in-out">

                                <!-- PREVIEW -->
                                <template x-if="preview">
                                    <div class="relative mb-3">
                                        <img :src="preview" alt="Preview KTP"
                                             class="w-80 md:w-96 aspect-[1.585] object-cover rounded-lg border border-slate-200 dark:border-navy-600 shadow-lg" />
                                        <button type="button" @click="removeFile"
                                                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 hover:bg-red-700 shadow">
                                            ✕
                                        </button>
                                    </div>
                                </template>

                                <!-- BUTTONS -->
                                <template x-if="!preview">
                                    <div class="flex flex-col items-center space-y-3">
                                        <button type="button" @click="$refs.ktpInput.click()"
                                                class="btn border border-primary font-medium text-primary hover:bg-primary hover:text-white focus:bg-primary focus:text-white active:bg-primary/90 dark:border-accent dark:text-accent-light dark:hover:bg-accent dark:hover:text-white dark:focus:bg-accent dark:focus:text-white dark:active:bg-accent/90">
                                            📁 Upload KTP
                                        </button>
                                    </div>
                                </template>

                                <input type="file" name="ktp_file" x-ref="ktpInput" accept="image/*" @change="handleFile($event)" class="hidden" required />

                            </div>
                            <p class="text-xs text-slate-500 mt-2" x-show="fileName" x-text="'File: ' + fileName"></p>
                        </label>

                        <!-- MODAL CROPPER -->
                        <template x-if="showModal">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto" @click.self="cancelCrop">
                                <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl p-5 w-full max-w-xl flex flex-col items-center animate-fade-in-up max-h-[90vh]">
                                    <h2 class="text-lg font-semibold text-slate-700 dark:text-slate-100 mb-3 text-center">
                                        Sesuaikan Foto KTP
                                    </h2>
                                    <div class="relative w-full md:w-[420px] aspect-[1.585] rounded-xl overflow-hidden border border-slate-300 dark:border-navy-600 bg-slate-100 dark:bg-navy-700 flex-shrink-0">
                                        <img x-ref="image" :src="tempImage" alt="Crop area" class="max-w-full max-h-[70vh] mx-auto my-auto block select-none object-contain" />
                                    </div>
                                    <div class="flex justify-center gap-3 mt-5 flex-shrink-0">
                                        <button @click="cancelCrop"
                                                class="px-4 py-2 rounded-lg border border-slate-300 dark:border-navy-600 text-slate-600 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-navy-700">
                                            Batal
                                        </button>
                                        <button @click="saveCrop"
                                                class="btn border border-secondary font-medium text-secondary hover:bg-secondary hover:text-white focus:bg-secondary focus:text-white active:bg-secondary/90 dark:text-secondary-light dark:hover:bg-secondary dark:hover:text-white dark:focus:bg-secondary dark:focus:text-white dark:active:bg-secondary/90">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- BUTTONS -->
                    <div class="flex justify-center space-x-2 pt-5">
                        <a href="{{ route('register.form') }}"
                           class="btn min-w-[7rem] border border-slate-300 font-medium text-slate-800 hover:bg-slate-150
                                  focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50
                                  dark:hover:bg-navy-700 dark:focus:bg-navy-700 dark:active:bg-navy-700/90 text-center px-4 py-2 rounded-lg">
                            Batal
                        </a>

                        <button type="submit"
                                class="btn min-w-[7rem] bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            Daftar
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>
</div>

<x-modal />
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet" />
<style>
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.4s ease-out; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registerForm', () => ({
        form: { name: '', nik: '', phone: '', ktp_file: null, ktp_file_name: '' },

        init() {
            // Tangkap event dari ktpCropper
            this.$el.addEventListener('ktp-updated', (e) => {
                this.form.ktp_file = e.detail.blob;
                this.form.ktp_file_name = e.detail.fileName;
            });
        },

        async submitForm() {
            try {
                let formData = new FormData(document.getElementById('registerForm'));

                // Masukkan file KTP cropped jika ada
                if (this.form.ktp_file) {
                    formData.set('ktp_file', this.form.ktp_file, this.form.ktp_file_name);
                }

                const response = await fetch("{{ route('register.submit') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
                    body: formData
                });

                const data = await response.json();

                if(data.status === 'success') {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: { type: 'success', title: 'Berhasil', message: data.message }
                    }));
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: { type: 'error', title: 'Gagal', message: data.message }
                    }));
                }
            } catch(err) {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', title: 'Error', message: 'Terjadi kesalahan jaringan atau server.' }
                }));
            }
        }
    }));
});

// ==== KTP CROPPER ====
function ktpCropper() {
    return {
        preview: null,
        tempImage: null,
        fileName: '',
        showModal: false,
        cropper: null,

        handleFile(event) {
            const file = event.target.files[0];
            if (!file || !file.type.startsWith("image/")) return alert("Hanya gambar yang diperbolehkan!");
            this.fileName = file.name;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.tempImage = e.target.result;
                this.showModal = true;
                this.$nextTick(() => this.initCropper());
            };
            reader.readAsDataURL(file);
        },

        initCropper() {
            if (this.cropper) this.cropper.destroy();
            this.cropper = new Cropper(this.$refs.image, {
                aspectRatio: 85.6 / 53.98,
                viewMode: 1,
                dragMode: "move",
                autoCropArea: 0.95,
                background: false,
                movable: true,
                zoomable: true,
                responsive: true,
                wheelZoomRatio: 0.1
            });
        },

        saveCrop() {
            if (!this.cropper) return;

            const canvas = this.cropper.getCroppedCanvas({
                width: 856,
                height: 540,
                imageSmoothingQuality: "high"
            });

            // Convert ke blob
            const blob = this.dataURLtoBlob(canvas.toDataURL("image/jpeg", 0.9));

            // Kirim event ke registerForm
            this.$dispatch('ktp-updated', { blob, fileName: this.fileName });

            // Tampilkan preview
            this.preview = canvas.toDataURL("image/jpeg", 0.9);

            // Tutup modal
            this.showModal = false;
            this.cropper.destroy();
        },

        cancelCrop() {
            if (this.cropper) this.cropper.destroy();
            this.showModal = false;
            this.tempImage = null;
        },

        removeFile() {
            this.preview = null;
            this.fileName = "";
            this.tempImage = null;

            // Hapus dari registerForm juga
            this.$dispatch('ktp-updated', { blob: null, fileName: '' });
        },

        dataURLtoBlob(dataurl) {
            let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            while(n--){ u8arr[n] = bstr.charCodeAt(n); }
            return new Blob([u8arr], {type:mime});
        }
    }
}
</script>

@endpush
