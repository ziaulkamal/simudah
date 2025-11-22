@extends('layouts.app')

@section('content')
<main x-data="transactionsTable()" class="main-content w-full px-[var(--margin-x)] pb-8">
    <x-breadcrumb-header
        :title="$title"
        :submenu="$submenu"
        route-name="customer.index"
        route-label="Pelanggan" />

    <!-- 🔹 FORM SEARCH NIK -->
    <div class="mt-6 w-full px-4 max-w-xl mx-auto">
        <label class="block">
            <input
                id="nikInput"
                x-model="nik"
                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                placeholder="Masukkan NIK"
                type="text"
            />
        </label>
        <button @click="searchNik()" type="button" class="mt-2 btn w-full bg-primary text-white hover:bg-primary-focus">
            Cari
        </button>
    </div>

    <!-- 🔹 INFO PELANGGAN & RINGKASAN TAGIHAN -->
    <template x-if="people">
        <div class="mt-6 space-y-4 w-full px-4">

            <!-- Card Info Personal -->
            <div class="p-4 border rounded-lg bg-gradient-to-r from-blue-50 to-blue-100 dark:from-navy-700 dark:to-navy-600 shadow-md">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100" x-text="people.name"></h2>
                        <p class="text-sm text-slate-500 dark:text-navy-300" x-text="people.category.name || '-'"></p>
                        <p class="mt-1 text-sm font-medium">Tagihan Bulan Ini: <span x-text="formatRupiah(currentMonthTotal())"></span></p>
                        <p class="text-sm font-medium">Jumlah Transaksi: <span x-text="transactions.length"></span></p>
                        <!-- 🔹 Status Akun -->
                        <div class="flex items-center gap-2 mt-1">
                            <span
                                class="badge rounded-full"
                                :class="people.role.level === 0 ? 'border border-error text-error' : 'border border-success text-success'"
                                x-text="people.role.level === 0 ? 'Nonaktif' : 'Aktif'"
                            ></span>
                        </div>
                    </div>

                    <!-- Bagian tombol & info personal -->
                    <div class="mt-3 sm:mt-0 flex flex-wrap gap-2">
                        <button
                            class="btn bg-primary text-white px-3 py-1 hover:bg-primary-focus"
                            @click="showHistory = !showHistory"
                            x-text="showHistory ? 'Tutup Riwayat' : 'Lihat Riwayat'"
                        ></button>

                        <!-- Tombol Aktifkan Ulang -->
                        <template x-if="people.role.level === 0">
                            <button
                                class="btn bg-success text-white px-3 py-1 hover:bg-success-focus"
                                @click="openConfirmModal()"
                            >
                                Aktifkan Ulang
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Table Riwayat Transaksi -->
            <div class="overflow-x-auto border rounded-lg" x-show="showHistory" x-transition>
                <table class="w-full text-left table-auto border-collapse">
                    <thead class="bg-slate-100 dark:bg-navy-800 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 border-b">Bulan/Tahun</th>
                            <th class="px-4 py-2 border-b">Kode Transaksi</th>
                            <th class="px-4 py-2 border-b">Kategori</th>
                            <th class="px-4 py-2 border-b">Role</th>
                            <th class="px-4 py-2 border-b">Jumlah</th>
                            <th class="px-4 py-2 border-b">Status</th>
                            <th class="px-4 py-2 border-b">Dibayar Pada</th>
                            <th class="px-4 py-2 border-b">Jatuh Tempo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-navy-600">
                        <template x-for="item in transactions" :key="item.id">
                            <tr class="hover:bg-slate-50 dark:hover:bg-navy-600 transition">
                                <td class="px-4 py-2" x-text="item.month + '/' + item.year"></td>
                                <td class="px-4 py-2" x-text="item.transaction_code"></td>
                                <td class="px-4 py-2" x-text="item.category"></td>
                                <td class="px-4 py-2" x-text="item.role"></td>
                                <td class="px-4 py-2" x-text="formatRupiah(item.amount)"></td>
                                <td class="px-4 py-2">
                                    <template x-if="item.status === 'paid'">
                                        <span class="badge text-success">Lunas</span>
                                    </template>
                                    <template x-if="item.status === 'pending'">
                                        <span class="badge text-secondary">Menunggu</span>
                                    </template>
                                    <template x-if="item.status === 'cancelled'">
                                        <span class="badge text-error">Dibatalkan</span>
                                    </template>
                                    <template x-if="item.status === 'expired'">
                                        <span class="badge text-slate-500">Kadaluarsa</span>
                                    </template>
                                </td>
                                <td class="px-4 py-2" x-text="item.paid_at"></td>
                                <td class="px-4 py-2" x-text="item.due_date"></td>
                            </tr>
                        </template>
                        <template x-if="transactions.length === 0">
                            <tr>
                                <td colspan="8" class="text-center py-6">Tidak ada transaksi</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </template>

    <!-- Modal Component -->
    {{-- <x-modal /> --}}
</main>

<script>
function transactionsTable() {
    return {
        nik: '',
        people: null,
        transactions: [],
        loading: false,
        showHistory: false,

        async searchNik() {
            if (!this.nik) return alert('Masukkan NIK terlebih dahulu!');
            this.loading = true;
            const nikInput = document.getElementById('nikInput');
            nikInput.classList.add('opacity-50', 'cursor-wait');

            try {
                const hashRes = await fetch(`/api/lsignature/${encodeURIComponent(this.nik)}`);
                const hashData = await hashRes.json();
                if (!hashData.success) {
                    alert(hashData.message || 'Data tidak ditemukan');
                    this.people = null;
                    this.transactions = [];
                    return;
                }

                const hash = hashData.data.hash;
                const res = await fetch(`/api/transactions/pelanggan/${hash}`);
                const data = await res.json();

                if (data.success) {
                    this.showHistory = false;
                    this.people = data.people;
                    this.transactions = data.transactions;
                } else {
                    alert(data.message || 'Transaksi tidak ditemukan');
                    this.people = null;
                    this.transactions = [];
                }

            } catch (err) {
                console.error(err);
                alert('Gagal memuat data');
                this.people = null;
                this.transactions = [];
            } finally {
                nikInput.classList.remove('opacity-50', 'cursor-wait');
                this.loading = false;
            }
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value || 0);
        },

        currentMonthTotal() {
            if (!this.people || !this.people.category) return 0;

            const now = new Date();
            const currentMonth = now.getMonth() + 1;
            const currentYear = now.getFullYear();
            const monthlyAmount = parseFloat(this.people.category.price) || 0;

            // Tentukan bulan terakhir ada transaksi yang dibayar atau pending
            let lastPaidMonth = 0;
            let lastPaidYear = 0;

            this.transactions.forEach(t => {
                if (t.status === 'paid' || t.status === 'pending') {
                    const tMonth = parseInt(t.month);
                    const tYear = parseInt(t.year);
                    if (tYear > lastPaidYear || (tYear === lastPaidYear && tMonth > lastPaidMonth)) {
                        lastPaidMonth = tMonth;
                        lastPaidYear = tYear;
                    }
                }
            });

            // Hitung jumlah bulan tertunda dari bulan terakhir sampai bulan sekarang
            let monthsPending = 0;
            let year = lastPaidYear;
            let month = lastPaidMonth + 1;

            while (year < currentYear || (year === currentYear && month <= currentMonth)) {
                monthsPending++;
                month++;
                if (month > 12) {
                    month = 1;
                    year++;
                }
            }

            return monthsPending * monthlyAmount;
        },


        // === Modal Trigger ===
        openConfirmModal() {
            window.dispatchEvent(new CustomEvent('show-confirm-modal', {
                detail: {
                    title: 'Aktifkan Ulang Akun',
                    message: 'Apakah Anda yakin ingin mengaktifkan ulang akun ini?',
                    onConfirm: () => this.reactivateAccount()
                }
            }));
        },

        reactivateAccount() {
            if (!this.people) return;
            fetch(`/api/people/${this.people.id}/reactivate`, { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                type: 'success',
                                title: 'Berhasil',
                                message: 'Akun berhasil diaktifkan ulang'
                            }
                        }));
                        this.people.role.level = 3; // update level frontend
                    } else {
                        window.dispatchEvent(new CustomEvent('show-alert', {
                            detail: {
                                type: 'error',
                                title: 'Gagal',
                                message: data.message || 'Gagal mengaktifkan ulang akun'
                            }
                        }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('show-alert', {
                        detail: {
                            type: 'error',
                            title: 'Kesalahan',
                            message: 'Terjadi kesalahan saat memproses permintaan'
                        }
                    }));
                });

        }
    }
}
</script>
@endsection
