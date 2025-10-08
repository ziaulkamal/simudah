@extends('layouts.app')

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

    <form action="#" method="POST" class="mt-4 space-y-4">
      @csrf
      {{-- Identity Numbers --}}
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
          <span>No. KTP</span>
          <span class="relative mt-1.5 flex">
            <input name="identityNumber" type="text" placeholder="Nomor KTP"
              class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                     hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                     dark:focus:border-accent">
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
              <i class="fa-solid fa-id-card text-base"></i>
            </span>
          </span>
        </label>

        <label class="block">
          <span>No. KK</span>
          <span class="relative mt-1.5 flex">
            <input name="familyIdentityNumber" type="text" placeholder="Nomor Kartu Keluarga"
              class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                     hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                     dark:focus:border-accent">
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
              <i class="fa-solid fa-users text-base"></i>
            </span>
          </span>
        </label>
      </div>
      {{-- Full Name --}}
      <label class="block">
        <span>Nama Lengkap</span>
        <span class="relative mt-1.5 flex">
          <input name="fullName" type="text" placeholder="Nama lengkap Anda"
            class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                   placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                   dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
          <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                       peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
            <i class="fa-regular fa-user text-base"></i>
          </span>
        </span>
      </label>

      {{-- Age & Birthdate --}}
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
          <span>Usia</span>
          <span class="relative mt-1.5 flex">
            <input name="age" type="number" placeholder="##"
              class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                     placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                     dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" readonly>
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
              <i class="fa-solid fa-hashtag text-base"></i>
            </span>
          </span>
        </label>

        <label class="block">
          <span>Tanggal Lahir</span>
          <span class="relative mt-1.5 flex">
            <input name="birthdate" type="date"
              class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                     hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                     dark:focus:border-accent">
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
              <i class="fa-regular fa-calendar text-base"></i>
            </span>
          </span>
        </label>
      </div>



      {{-- Gender --}}
      <label class="block">
        <span>Jenis Kelamin</span>
        <select name="gender"
          class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                 dark:focus:border-accent">
          <option value="">-- Pilih Jenis Kelamin --</option>
          <option value="male">Laki-laki</option>
          <option value="female">Perempuan</option>
        </select>
      </label>

      {{-- Religion --}}
      <label class="block">
        <span>Agama</span>
        <select name="religion"
          class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                 dark:focus:border-accent">
          <option value="">-- Pilih Agama --</option>
          <option value="1">Islam</option>
          <option value="2">Kristen</option>
          <option value="3">Katolik</option>
          <option value="4">Hindu</option>
          <option value="5">Buddha</option>
          <option value="6">Konghucu</option>
        </select>
      </label>

      {{-- Address --}}
      <label class="block">
        <span>Alamat Lengkap</span>
        <textarea name="streetAddress" rows="3" placeholder="Nama jalan, RT/RW, dan area sekitar"
          class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent p-2.5
                 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                 dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"></textarea>
      </label>

      {{-- Wilayah --}}
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
          <span>Provinsi</span>
          <select name="provinceId"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                   dark:focus:border-accent">
            <option value="">-- Pilih Provinsi --</option>
          </select>
        </label>

        <label class="block">
          <span>Kabupaten / Kota</span>
          <select name="regencieId"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                   dark:focus:border-accent">
            <option value="">-- Pilih Kabupaten/Kota --</option>
          </select>
        </label>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
          <span>Kecamatan</span>
          <select name="districtId"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                   dark:focus:border-accent">
            <option value="">-- Pilih Kecamatan --</option>
          </select>
        </label>

        <label class="block">
          <span>Desa / Kelurahan</span>
          <select name="villageId"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2
                   hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                   dark:focus:border-accent">
            <option value="">-- Pilih Desa/Kelurahan --</option>
          </select>
        </label>
      </div>

      {{-- Phone & Email --}}
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
          <span>Nomor Telepon</span>
          <span class="relative mt-1.5 flex">
            <input name="phoneNumber" type="text" placeholder="08xxxxxxxxxx"
              class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                     hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                     dark:focus:border-accent">
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
              <i class="fa-solid fa-phone"></i>
            </span>
          </span>
        </label>

        <label class="block">
          <span>Email</span>
          <span class="relative mt-1.5 flex">
            <input name="email" type="email" placeholder="Alamat email (opsional)"
              class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9
                     hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400
                     dark:focus:border-accent">
            <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400
                         peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
              <i class="fa-regular fa-envelope text-base"></i>
            </span>
          </span>
        </label>
      </div>

      <div class="flex justify-end pt-4">
        <button type="submit"
          class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus
                 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus
                 dark:focus:bg-accent-focus dark:active:bg-accent/90">
          Simpan Data
        </button>
      </div>
    </form>

  </div>
</div>

  </div>
</main>
@endsection
