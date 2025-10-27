@extends('layouts.auth')

@section('pages')
<div id="root" x-data="authPage" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak>
    <main class="grid w-full grow grid-cols-1 place-items-center">
        <div class="w-full max-w-md p-6 text-center">
            <div class="text-center">
                <img class="mx-auto size-16" src="{{ asset('images/logo-simudah.png') }}" alt="logo" />
                <h2 class="mt-4 text-2xl font-semibold text-slate-600 dark:text-navy-100">Verifikasi OTP</h2>
                <p class="mt-2 text-base text-slate-500 dark:text-slate-300">
                    Masukkan kode OTP yang dikirim ke nomor <span class="font-medium">{{ $temp->phoneNumber }}</span>
                </p>
            </div>

            <div class="card mt-5 rounded-lg p-5 lg:p-7 space-y-4">
                <div x-show="true" x-transition>
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

                    <button class="btn w-full bg-primary font-medium text-white mt-4" @click="verifyOtp()">
                        Verifikasi OTP
                    </button>
                </div>
            </div>

            <div class="mt-8 text-xs text-slate-400 dark:text-navy-300">
                <p>Belum menerima kode? <a href="{{ route('register.resendOtp', ['id' => request('id')]) }}" class="btn btn-outline-primary">
    Kirim Ulang OTP
</a></p>
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
        otp1: '', otp2: '', otp3: '', otp4: '', otp5: '',
        personId: "{{ $temp->id }}",

        showModal(type, title, message) {
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { type, title, message }
            }));
        },

        verifyOtp: async function() {
            const otpCode = `${this.otp1}${this.otp2}${this.otp3}${this.otp4}${this.otp5}`;
            if (!this.personId) return this.showModal('error', 'Kesalahan', 'Person ID tidak ditemukan.');
            if (otpCode.length !== 5) return this.showModal('error', 'Kesalahan', 'Masukkan 5 digit kode OTP.');

            try {
                const res = await fetch(`{{ url('/verify-otp') }}/${this.personId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ otp_code: otpCode })
                });
                const data = await res.json();
                    if (res.ok && data.status === 'success') {
                        // Tampilkan modal confirm
                        window.dispatchEvent(new CustomEvent('show-confirm-modal', {
                            detail: {
                                type: 'success',
                                title: 'Verifikasi Berhasil',
                                message: data.message,
                                onConfirm: () => {
                                    window.location.href = '{{ route("auth.login") }}';
                                }
                            }
                        }));

                        // Otomatis arahkan setelah 5 detik
                        setTimeout(() => {
                            window.location.href = '{{ route("auth.login") }}';
                        }, 5000);

                    } else {
                        this.showModal('error', 'OTP Salah', data.message || 'Kode OTP tidak valid atau sudah kedaluwarsa.');
                    }
            } catch (err) {
                console.error(err);
                this.showModal('error', 'Kesalahan', 'Terjadi kesalahan saat verifikasi OTP.');
            }
        },

        init() {
            // Tampilkan modal swal_success dari session saat page load
            @if(session('swal_success'))
                this.showModal('success', 'Sukses', "{{ session('swal_success') }}");
            @endif
            @if(session('swal_error'))
                this.showModal('error', 'Error', "{{ session('swal_error') }}");
            @endif
        }
    }));
});
</script>
@endpush
