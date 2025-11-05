@extends('layouts.app')
{{-- @dd($people) --}}
@section('content')

<main class="main-content w-full px-[var(--margin-x)] pb-8">
  <div class="flex items-center space-x-4 py-5 lg:py-6">
    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
      Registrasi Pelanggan
    </h2>
    <div class="hidden h-full py-1 sm:flex">
      <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
    </div>
    <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
      <li class="flex items-center space-x-2">
        <a class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
           href="#">Forms</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </li>
      <li>Input Data Pelanggan</li>
    </ul>
  </div>

  <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
    <div class="col-span-12">
      <div class="card p-4 sm:p-5">
        <p class="text-base font-medium text-slate-700 dark:text-navy-100">
          Formulir Data Pelanggan
        </p>

        <form x-data="peopleForm({{ isset($model) ? $model->id : 'null' }})"
            @submit.prevent="submitForm"
            method="POST"
            action="{{ isset($model) ? route('people.update', $model->id) : route('people.store') }}"
            class="mt-4 space-y-4">
            @csrf
            @if(isset($model))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form-input
                    name="identityNumber"
                    label="No. KTP"
                    placeholder="Nomor KTP"
                    icon="fa-solid fa-id-card"
                    :value="old('identityNumber', $people['identityNumber'] ?? '')" />

                <x-form-input
                    name="familyIdentityNumber"
                    label="No. KK"
                    placeholder="Nomor Kartu Keluarga"
                    icon="fa-solid fa-users"
                    :value="old('familyIdentityNumber', isset($model->familyIdentityNumber) ? Crypt::decryptString($model->familyIdentityNumber) : null)" />
            </div>

            <x-form-input
                name="fullName"
                label="Nama Lengkap"
                placeholder="Nama lengkap Anda"
                icon="fa-regular fa-user"
                :value="old('fullName', $people['fullName'] ?? ($model->fullName ?? ''))" />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form-input
                    name="age"
                    label="Usia"
                    placeholder="##"
                    icon="fa-solid fa-hashtag"
                    :value="old('age', $people['age'] ?? ($model->age ?? ''))"
                    readonly />

                <x-form-input
                    name="birthdate"
                    label="Tanggal Lahir"
                    type="date"
                    icon="fa-regular fa-calendar"
                    :value="old('birthdate', $people['birthdate'] ?? ($model->birthdate ?? ''))" />
            </div>

            <x-select-dropdown
                name="gender"
                label="Jenis Kelamin"
                :options="['male' => 'Laki-laki', 'female' => 'Perempuan']"
                :selected="old('gender', $people['gender'] ?? ($model->gender ?? ''))" />

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
                :selected="old('religion', $model->religion ?? null)" />

            <x-wilayah-dropdown
                :provinceId="old('provinceId', $model->provinceId ?? null)"
                :regencyId="old('regencyId', $model->regencieId ?? null)"
                :districtId="old('districtId', $model->districtId ?? null)"
                :villageId="old('villageId', $model->villageId ?? null)" />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form-input
                    name="phoneNumber"
                    label="Nomor Telepon"
                    placeholder="08xxxxxxxxxx"
                    :value="old('phoneNumber', $people['phoneNumber'] ?? ($model->phoneNumber ?? ''))" />

                <x-form-input
                    name="email"
                    label="Email"
                    placeholder="Alamat email (opsional)"
                    type="email"
                    icon="fa-regular fa-envelope"
                    :value="old('email', $model->email ?? null)" />
            </div>

            <x-button type="submit" label="{{ isset($model) ? 'Perbarui Data' : 'Simpan Data' }}" color="primary" />
        </form>

      </div>
    </div>
  </div>
</main>
@endsection


@push('scripts')
{{-- ✅ Modal Notifikasi Global --}}
<div
    x-data="{ open: false, type: 'info', title: '', message: '' }"
    x-on:show-alert.window="
        open = true;
        type = $event.detail.type;
        title = $event.detail.title;
        message = $event.detail.message;
    "
    x-show="open"
    x-transition
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm"
    x-cloak >
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6">
        <!-- Judul -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
        <h3
            class="text-lg font-semibold"
            :class="{
            'text-green-600': type === 'success',
            'text-red-600': type === 'error',
            'text-yellow-600': type === 'warning',
            'text-blue-600': type === 'info'
            }"
            x-text="title"
        ></h3>

        <button
            @click="open = false"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
        </div>

        <!-- Pesan -->
        <p class="text-slate-700 dark:text-slate-200" x-text="message"></p>

        <!-- Tombol OK -->
        <div class="mt-6 text-right">
        <button
            @click="open = false"
            class="btn rounded-lg bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90"
        >
            OK
        </button>
        </div>
    </div>
</div>
<script>
function peopleForm(id = null) {
    return {
        loading: false,

        async submitForm(event) {
            this.loading = true;

            const form = event.target;
            const formData = new FormData(form);

            // Konversi FormData ke JSON
            let object = {};
            formData.forEach((value, key) => (object[key] = value));
            const jsonData = JSON.stringify(object);

            // Tentukan method & URL
            let method = form.querySelector('input[name="_method"]')?.value || form.method;
            method = method.toUpperCase();
            let url = form.action;
            if (method === 'PUT' && id) url = `/api/people/${id}`;

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: jsonData,
                });

                // Cek konten respons
                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json')
                    ? await response.json()
                    : { message: await response.text() };

                // --- ✅ Respons sukses ---
                if (response.ok) {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'success',
                            title: 'Berhasil Disimpan',
                            message: data.message || 'Data pelanggan berhasil disimpan.',
                        },
                    }));

                    window.location.href = `{{ route('customer.index') }}`;

                    if (method === 'POST') form.reset();
                    return;
                }

                // --- ⚠️ Validasi error ---
                if (response.status === 422 && data.errors) {
                    const firstError = Object.values(data.errors)[0]?.[0] || 'Periksa kembali isian Anda.';
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'warning',
                            title: 'Validasi Gagal',
                            message: firstError,
                        },
                    }));
                    return;
                }

                // --- ❌ Error lain ---
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'error',
                        title: 'Gagal Menyimpan',
                        message: data.message || 'Terjadi kesalahan saat menyimpan data.',
                    },
                }));

            } catch (error) {
                console.error(error);
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: {
                        type: 'error',
                        title: 'Kesalahan Jaringan',
                        message: 'Tidak dapat terhubung ke server. Coba lagi nanti.',
                    },
                }));
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const nikInput = document.querySelector('[name="identityNumber"]');
    const birthdateInput = document.querySelector('[name="birthdate"]');
    const ageInput = document.querySelector('[name="age"]');
    const genderSelect = document.querySelector('[name="gender"]');
    const fullNameInput = document.querySelector('[name="fullName"]');
    const religionSelect = document.querySelector('[name="religion"]');

    let debounceTimer;
    const defaultFullNamePlaceholder = fullNameInput ? fullNameInput.getAttribute('placeholder') : '';

    const resetFormOnInvalidNik = () => {
        if (genderSelect) genderSelect.value = '';
        if (birthdateInput) birthdateInput.value = '';
        if (ageInput) ageInput.value = '';
        if (fullNameInput) fullNameInput.placeholder = defaultFullNamePlaceholder;
        document.dispatchEvent(new CustomEvent('updateWilayah', { detail: null }));
    };

    const updateAgeFromBirthdate = () => {
        if (!birthdateInput.value) return;
        const birth = new Date(birthdateInput.value);
        const today = new Date();
        const diffDays = (today - birth) / (1000*60*60*24);
        const years = Math.floor(diffDays / 365);
        if (ageInput) ageInput.value = years;
    };

    const setFormValues = (data) => {
        if (genderSelect && data.gender) genderSelect.value = data.gender;

        const birthdateValue = data.birthdate || data.birthDate || '';
        if (birthdateInput && birthdateValue) {
            birthdateInput.value = birthdateValue;
            updateAgeFromBirthdate();
        }

        if (ageInput && data.age !== undefined) ageInput.value = data.age;
        if (fullNameInput && data.name) fullNameInput.placeholder = data.name;

        checkAndTriggerSearch();
    };

    // --- Perbaikan fetch NIK ---
    const fetchNIKData = async (nik) => {
        try {
            // Kirim GET tanpa body
            const sigRes = await fetch(`/api/signature?nik=${encodeURIComponent(nik)}`);
            const { signature, timestamp } = await sigRes.json();

            nikInput.classList.add('opacity-50', 'cursor-wait');

            const response = await fetch('/api/mendagri/identity/nik', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-App-Signature': signature,
                    'X-App-Timestamp': timestamp
                },
                body: JSON.stringify({ nik }) // POST → aman
            });

            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            setFormValues(data);
        } catch (err) {
            console.error(err);
            resetFormOnInvalidNik();
        } finally {
            nikInput.classList.remove('opacity-50', 'cursor-wait');
        }
    };

    // --- Perbaikan search ---
    const triggerIdentitySearch = async () => {
        const nik = nikInput.value;
        const name = fullNameInput.value || fullNameInput.placeholder;
        if (!nik || !name) return;

        // GET signature tanpa body
        const sigRes = await fetch(`/api/signature?nik=${encodeURIComponent(nik)}&name=${encodeURIComponent(name)}`);
        const { signature, timestamp } = await sigRes.json();

        try {
            const response = await fetch('/api/mendagri/identity/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-App-Signature': signature,
                    'X-App-Timestamp': timestamp
                },
                body: JSON.stringify({ nik, name }) // POST → aman
            });

            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            // console.log('Search API Response:', data);
            document.dispatchEvent(new CustomEvent('updateWilayah', { detail: data }));
        } catch (err) {
            console.error(err);
        }
    };

    const checkAndTriggerSearch = () => {
        if (
            nikInput.value.length === 16 &&
            birthdateInput.value &&
            ageInput.value &&
            genderSelect.value &&
            fullNameInput.placeholder &&
            religionSelect.value
        ) {
            triggerIdentitySearch();
        }
    };

    nikInput.addEventListener('input', function() {
        const value = nikInput.value;
        clearTimeout(debounceTimer);
        if (/^\d{16}$/.test(value)) {
            debounceTimer = setTimeout(() => fetchNIKData(value), 1000);
        } else {
            resetFormOnInvalidNik();
        }
    });

    if (birthdateInput) birthdateInput.addEventListener('change', () => {
        updateAgeFromBirthdate();
        checkAndTriggerSearch();
    });

    if (religionSelect) religionSelect.addEventListener('change', checkAndTriggerSearch);
});
</script>
@endpush
