@extends('layouts.auth')

@section('pages')
<div id="root" x-data="authPage" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak>

    <main class="grid w-full grow grid-cols-1 place-items-center">
        <div class="w-full max-w-[26rem] p-4 sm:px-5">
            <div class="text-center">
                <img class="mx-auto size-16" src="{{ asset('images/logo-simudah.png') }}" alt="logo" />
                <div class="mt-4">
                    <h2 class="text-2xl font-semibold text-slate-600 dark:text-navy-100">SIMUDAH</h2>
                    <p class="mt-2 text-base text-slate-500 dark:text-slate-300" >
                        Sistem Informasi Manajemen Persampahan <span class="text-green-600 font-medium">Mudah</span> &amp; <span class="text-teal-500 font-medium">Terarah</span>
                    </p>
                </div>
            </div>

            <div class="card mt-5 rounded-lg p-5 lg:p-7 space-y-4">
                <!-- LOGIN SSO -->
                <template x-if="!useOtp">
                    <div x-transition>
                        <label class="block">
                            <span>Username:</span>
                            <input class="form-input w-full rounded-lg border px-3 py-2" type="text" placeholder="Masukan Username" x-model="username" />
                        </label>

                        <label class="block mt-2">
                            <span>Password:</span>
                            <input class="form-input w-full rounded-lg border px-3 py-2" type="password" placeholder="Masukan Password" x-model="password" />
                        </label>

                        <div class="flex justify-end">
                            <a href="#" class="text-xs text-slate-400 hover:text-slate-800 dark:text-navy-300 dark:hover:text-navy-100">
                                Lupa Password ?
                            </a>
                        </div>

                        <button class="btn w-full bg-primary font-medium text-white mt-2" @click="loginSSO()">
                            Login
                        </button>
                    </div>
                </template>

                <!-- LOGIN OTP -->
                <template x-if="useOtp">
                    <div x-transition>
                        <!-- STEP 1: Input NIK & WA -->
                        <div x-show="!stepOtp" x-transition>
                            <label class="block">
                                <span>Nomor Induk Kependudukan:</span>
                                <input class="form-input w-full rounded-lg border px-3 py-2"
                                    type="tel" placeholder="Masukan NIK"
                                    x-model="nik"
                                    @input="nik = nik.replace(/[^0-9]/g, '').slice(0,16)" />
                            </label>

                            <label class="block mt-2">
                                <span>Nomor WhatsApp:</span>
                                <input class="form-input w-full rounded-lg border px-3 py-2"
                                    type="tel" placeholder="628**"
                                    x-model="wa"
                                    maxlength="13"
                                    @input="wa = wa.replace(/[^0-9]/g, '').slice(0,13)" />
                            </label>

                            <div class="flex justify-end">
                                <a href="#" class="text-xs text-slate-400 hover:text-slate-800 dark:text-navy-300 dark:hover:text-navy-100">Lupa Password ?</a>
                            </div>

                            <button class="btn w-full bg-primary font-medium text-white mt-2" @click="sendOtp()">
                                Kirim OTP
                            </button>
                        </div>

                        <!-- STEP 2: Verifikasi OTP -->
                        <div x-show="stepOtp" x-transition>
                            <p class="text-sm text-slate-500 mb-2" x-text="'Masukkan kode OTP yang dikirim ke ' + wa"></p>

                           <div class="flex justify-between mt-2 space-x-2">
                                <input type="tel" maxlength="1" x-model="otp1"
                                    @input="otp1 = otp1.replace(/[^0-9]/g, ''); if(otp1.length==1) $refs.otp2.focus()"
                                    x-ref="otp1"
                                    class="form-input w-12 text-center rounded-lg border px-3 py-2 text-lg font-semibold" />

                                <input type="tel" maxlength="1" x-model="otp2"
                                    @input="otp2 = otp2.replace(/[^0-9]/g, ''); if(otp2.length==1) $refs.otp3.focus()"
                                    @keydown.backspace="if($event.target.value=='') $refs.otp1.focus()"
                                    x-ref="otp2"
                                    class="form-input w-12 text-center rounded-lg border px-3 py-2 text-lg font-semibold" />

                                <input type="tel" maxlength="1" x-model="otp3"
                                    @input="otp3 = otp3.replace(/[^0-9]/g, ''); if(otp3.length==1) $refs.otp4.focus()"
                                    @keydown.backspace="if($event.target.value=='') $refs.otp2.focus()"
                                    x-ref="otp3"
                                    class="form-input w-12 text-center rounded-lg border px-3 py-2 text-lg font-semibold" />

                                <input type="tel" maxlength="1" x-model="otp4"
                                    @input="otp4 = otp4.replace(/[^0-9]/g, ''); if(otp4.length==1) $refs.otp5.focus()"
                                    @keydown.backspace="if($event.target.value=='') $refs.otp3.focus()"
                                    x-ref="otp4"
                                    class="form-input w-12 text-center rounded-lg border px-3 py-2 text-lg font-semibold" />

                                <input type="tel" maxlength="1" x-model="otp5"
                                    @input="otp5 = otp5.replace(/[^0-9]/g, '')"
                                    @keydown.backspace="if($event.target.value=='') $refs.otp4.focus()"
                                    x-ref="otp5"
                                    class="form-input w-12 text-center rounded-lg border px-3 py-2 text-lg font-semibold" />
                                </div>

                            <button class="btn w-full bg-primary font-medium text-white mt-2" @click="verifyOtp()">
                                Login dengan OTP
                            </button>
                        </div>


                    </div>
                </template>
<div class="text-center text-xs-plus mt-3">
                            <p>
                                <span>Belum Punya akun ? </span>
                                <a class="text-primary hover:text-primary-focus dark:text-accent-light dark:hover:text-accent" href="{{ route('register.form') }}">
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
                <button
                    @click="useOtp = !useOtp; stepOtp=false; username=''; password=''; nik=''; wa=''; otp1=''; otp2=''; otp3=''; otp4=''; otp5='';"
                    class="btn w-full flex items-center justify-center space-x-2 border border-slate-300 font-medium text-slate-800 dark:border-navy-450 dark:text-navy-50">
                    <img x-show="!useOtp" x-transition.opacity.duration.300 class="size-5.5" src="images/logos/whatsapp.svg" alt="logo" />
                    <span x-text="useOtp ? 'Login dengan SSO' : 'Login dengan OTP'"></span>
                </button>
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

{{-- Modal Alert --}}
<x-modal />
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('authPage', () => ({
                useOtp: false,
                stepOtp: false,
                username: '', password: '',
                nik: '', wa: '',
                otp1:'', otp2:'', otp3:'', otp4:'', otp5:'',
                personId: null,

                // --- ALERT MODAL HELPER ---
                showModal(type, title, message) {
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: { type, title, message }
                    }));
                },

                // --- Function SSO LOGIN ---
                async loginSSO() {
                    try {
                        const res = await fetch('{{ url('/login') }}', { // ✅ ubah ke /login
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            credentials: 'include', // ✅ penting agar cookie session ikut disimpan
                            body: JSON.stringify({
                                username: this.username,
                                password: this.password
                            })
                        });

                        let data;
                        try { data = await res.json(); } catch(e) {
                            this.showModal('error', 'Gagal', 'Server tidak merespon JSON');
                            return;
                        }

                        if (res.ok && data.status === 'success') {
                            this.showModal('success', 'Berhasil', 'Login berhasil! Anda akan diarahkan...');
                            setTimeout(() => window.location.href = '/', 1500);
                        } else {
                            this.showModal('error', 'Gagal Login', data.message || 'Username atau password salah');
                        }
                    } catch (err) {
                        console.error(err);
                        this.showModal('error', 'Kesalahan', 'Terjadi kesalahan saat login.');
                    }
                },

                // --- Function KIRIM OTP ---
                async sendOtp() {
                    try {
                        const res = await fetch('{{ url('/send-otp') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                            },
                            body: JSON.stringify({ nik: this.nik, wa: this.wa })
                        });

                        let data;
                        try { data = await res.json(); } catch(e) {
                            this.showModal('error', 'Kesalahan', 'Server tidak merespon JSON');
                            return;
                        }

                        if (res.ok && data.status === 'success') {
                            this.stepOtp = true;
                            this.personId = data.person_id;
                            this.showModal('success', 'OTP Dikirim', 'Kode OTP telah dikirim ke WhatsApp Anda.');
                        } else {
                            this.showModal('error', 'Gagal', data.message || 'NIK atau nomor WA tidak ditemukan');
                        }
                    } catch (err) {
                        console.error(err);
                        this.showModal('error', 'Kesalahan', 'Terjadi kesalahan saat mengirim OTP.');
                    }
                },

                // --- Function VERIFIKASI OTP ---
                async verifyOtp() {
                    try {
                        const otpCode = `${this.otp1}${this.otp2}${this.otp3}${this.otp4}${this.otp5}`;

                        if (!this.personId) {
                            this.showModal('error', 'Kesalahan', 'Person ID tidak ditemukan. Silakan kirim OTP ulang.');
                            return;
                        }
                        if (otpCode.length !== 5) {
                            this.showModal('error', 'Kesalahan', 'Masukkan 5 digit kode OTP.');
                            return;
                        }

                        const res = await fetch('{{ url('/verify-otp') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                person_id: this.personId,
                                otp: otpCode
                            })
                        });

                        let data;
                        try { data = await res.json(); } catch(e) {
                            this.showModal('error', 'Kesalahan', 'Server tidak merespon JSON');
                            return;
                        }

                        if (res.ok && data.status === 'success') {
                            this.showModal('success', 'Login Berhasil', 'Anda akan diarahkan ke dashboard.');
                            setTimeout(() => window.location.href = '/', 1500);
                        } else {
                            this.showModal('error', 'OTP Salah', data.message || 'Kode OTP tidak valid atau sudah kedaluwarsa.');
                        }

                    } catch (err) {
                        console.error(err);
                        this.showModal('error', 'Kesalahan', 'Terjadi kesalahan saat verifikasi OTP.');
                    }
                }


            }));
        });
    </script>
@endpush

