<div
    x-data="transactionTab({{ $people->id }})"
    x-init="loadTransactions()"
    class="space-y-6"
>
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 dark:border-navy-500 pb-4 gap-3">
        <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">
            Transaksi Pembayaran
        </h2>

        <div class="flex items-center gap-2">
            <select
                x-model="filterStatus"
                class="form-select w-28 rounded-lg border border-slate-300 bg-transparent px-2 py-2 text-sm
                    hover:border-slate-400 focus:border-primary
                    dark:border-navy-500 dark:hover:border-navy-400 dark:focus:border-accent transition-colors"
            >
                <option value="all">Semua</option>
                <option value="pending">Tunda</option>
                <option value="paid">Lunas</option>
                <option value="cancelled">Batal</option>
            </select>

            <!-- 🔄 Tombol Reload -->
            <button
                @click="loadTransactions"
                class="btn btn-sm bg-primary text-white hover:bg-primary-focus"
            >
                <i class="fa fa-rotate-right mr-1"></i> Reload
            </button>
        </div>
    </div>

    <!-- TABEL TRANSAKSI -->
    <template x-if="loading">
        <div class="py-8 text-center text-slate-500 dark:text-navy-300">
            Memuat data transaksi...
        </div>
    </template>

    <template x-if="!loading">
        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-navy-500">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-navy-500">
                <thead class="bg-slate-50 dark:bg-navy-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-slate-600 dark:text-navy-100">Tanggal Bayar</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-slate-600 dark:text-navy-100">Jatuh Tempo</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-slate-600 dark:text-navy-100">Kode Transaksi</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-slate-600 dark:text-navy-100">Jumlah</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold text-slate-600 dark:text-navy-100">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                    <template
                        x-for="trx in transactions.filter(t => filterStatus === 'all' || t.status === filterStatus)"
                        :key="trx.id"
                    >
                        <tr class="hover:bg-slate-50 dark:hover:bg-navy-700/40 transition-colors">
                            <!-- Tanggal Bayar -->
                            <td class="px-4 py-2 text-sm text-slate-700 dark:text-navy-100 whitespace-nowrap"
                                x-text="trx.paid_at ? trx.paid_at : '-'"></td>

                            <!-- Jatuh Tempo -->
                            <td class="px-4 py-2 text-sm text-slate-700 dark:text-navy-100 whitespace-nowrap"
                                x-text="trx.due_date ? trx.due_date : '-'"></td>

                            <!-- Kode Transaksi -->
                            <td class="px-4 py-2 text-sm text-slate-600 dark:text-navy-200"
                                x-text="trx.transaction_code || 'Transaksi'"></td>

                            <!-- Jumlah -->
                            <td class="px-4 py-2 text-sm text-right font-medium text-slate-700 dark:text-navy-100">
                                Rp<span x-text="Number(trx.amount).toLocaleString('id-ID')"></span>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-2 text-sm text-center">
                                <template x-if="trx.status === 'paid'">
                                    <span class="badge bg-success/10 text-success">Lunas</span>
                                </template>

                                <template x-if="trx.status === 'pending'">
                                    <button
                                        @click="payNow(trx.id)"
                                        class="btn bg-warning/10 text-warning hover:bg-warning/20 text-xs px-2 py-1 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Bayar Sekarang
                                    </button>
                                </template>

                                <template x-if="trx.status === 'cancelled'">
                                    <span class="badge bg-error/10 text-error">Batal</span>
                                </template>
                            </td>
                        </tr>
                    </template>

                    <!-- JIKA KOSONG -->
                    <tr x-show="transactions.length === 0 && !loading">
                        <td colspan="5" class="py-4 text-center text-slate-500 dark:text-navy-300">
                            Belum ada transaksi.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </template>
</div>

<!-- 🔹 SCRIPT -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('transactionTab', (peopleId) => ({
        transactions: [],
        filterStatus: 'all',
        loading: false,

        async loadTransactions() {
            this.loading = true;
            try {
                const res = await fetch(`/api/transactions/${peopleId}/people`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
                const data = await res.json();

                if (data.success) {
                    this.transactions = data.data.transactions
                        .sort((a, b) => b.id - a.id)
                        .slice(0, 15);
                } else {
                    this.transactions = [];
                }
            } catch (err) {
                console.error('Gagal memuat transaksi:', err);
                window.toast?.('Gagal memuat transaksi', { type: 'error' });
            } finally {
                this.loading = false;
            }
        },

        async payNow(id) {
            if (!confirm('Yakin ingin menandai transaksi ini sebagai LUNAS?')) return;
            try {
                const res = await fetch(`/api/transactions/${id}/pay`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (data.success) {
                    window.toast?.('Pembayaran berhasil dicatat.', { type: 'success' });
                    await this.loadTransactions();
                } else {
                    alert(data.message || 'Gagal memproses pembayaran');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memproses pembayaran');
            }
        },
    }));
});
</script>
