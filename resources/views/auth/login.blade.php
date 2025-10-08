
@extends('layouts.auth')
@section('pages')
<div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900"
     x-data="{ useOtp: false, input1: '', input2: '' }" x-cloak>
  <main class="grid w-full grow grid-cols-1 place-items-center">
    <div class="w-full max-w-[26rem] p-4 sm:px-5">
      <div class="text-center">
        <img class="mx-auto size-16" src="images/app-logo.svg" alt="logo" />
        <div class="mt-4">
          <h2 class="text-2xl font-semibold text-slate-600 dark:text-navy-100"> SiMudah </h2>
          <p class="text-slate-400 dark:text-navy-300"> Sistem Informasi Manajemen Sampah Mudah & Terarah </p>
        </div>
      </div>

      <div class="card mt-5 rounded-lg p-5 lg:p-7 space-y-4">

        <!-- Input 1 -->
        <label class="block">
          <span x-text="useOtp ? 'Nomor Induk Kependudukan:' : 'Username:'"></span>
          <input class="form-input w-full rounded-lg border px-3 py-2 transition"
                 :placeholder="useOtp ? '' : 'Masukan Username'"
                 type="text"
                 x-model="input1" />
        </label>

        <!-- Input 2 -->
        <label class="block">
          <span x-text="useOtp ? 'Nomor WhatsApp:' : 'Password:'"></span>
          <input class="form-input w-full rounded-lg border px-3 py-2 transition"
                 :type="useOtp ? 'text' : 'password'"
                 :placeholder="useOtp ? '628**' : 'Masukan Password'"
                 x-model="input2" />
        </label>

        <!-- Lupa Password selalu tampil di kanan -->
        <div class="flex justify-end">
          <a href="#" class="text-xs text-slate-400 hover:text-slate-800 dark:text-navy-300 dark:hover:text-navy-100">
            Lupa Password ?
          </a>
        </div>

        <!-- Tombol Login -->
        <button class="btn w-full bg-primary font-medium text-white mt-2">
          <span x-text="useOtp ? 'Login dengan OTP' : 'Login'"></span>
        </button>

        <!-- Belum punya akun -->
        <div class="text-center text-xs-plus mt-3">
          <p>
            <span>Belum Punya akun ? </span>
            <a class="text-primary hover:text-primary-focus dark:text-accent-light dark:hover:text-accent" href="#">
              Daftar Sebagai Pelanggan
            </a>
          </p>
        </div>

        <!-- Separator -->
        <div class="my-5 flex items-center space-x-3">
          <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
          <p>ATAU</p>
          <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
        </div>

        <!-- Toggle Button -->
        <div>
          <button
            @click="
              useOtp = !useOtp;
              input1 = '';
              input2 = '';
            "
            class="btn w-full flex items-center justify-center space-x-2 border border-slate-300 font-medium text-slate-800 dark:border-navy-450 dark:text-navy-50">

            <!-- Icon hanya tampil kalau masih mode OTP -->
            <img x-show="!useOtp" x-transition.opacity.duration.300 class="size-5.5" src="images/logos/whatsapp.svg" alt="logo" />

            <span x-text="useOtp ? 'Login dengan SSO' : 'Login dengan OTP'"></span>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 flex justify-center text-xs text-slate-400 dark:text-navy-300">
        <a href="#">Kebijakan Layanan</a>
        <div class="mx-3 my-1 w-px bg-slate-200 dark:bg-navy-500"></div>
        <a href="#">Ketentuan Pemakaian</a>
      </div>
    </div>
  </main>
</div>
@endsection

