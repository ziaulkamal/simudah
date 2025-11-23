<!-- Modal -->
<div x-show="openInvoice" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">

    <!-- Modal Box (lebih kecil) -->
    <div @click.away="closeInvoiceModal()"
         class="w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[85vh]">

        <!-- HEADER -->
        <div class="rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 px-4 py-4 text-white sm:px-5 text-white flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold tracking-wide">Invoice Pembayaran</h1>
                <p class="text-xs opacity-90 mt-1">
                    Kode Transaksi:
                    <span class="font-semibold" x-text="invoiceData?.transaction_code"></span>
                </p>
            </div>

            <button @click="closeInvoiceModal()"
                    class="text-white text-2xl leading-none hover:opacity-70 font-light">
                &times;
            </button>
        </div>

        <!-- BODY -->
        <div class="p-4 overflow-y-auto max-h-[60vh]">

            <!-- LOADING -->
            <template x-if="invoiceLoading">
                <div class="text-center py-8 text-gray-500 text-sm">Memuat data invoice...</div>
            </template>

            <!-- INVOICE DATA -->
            <template x-if="invoiceData && !invoiceLoading">
                <div class="space-y-6">

                    <!-- Pelanggan -->
                    <div class="">
                        <h2 class="font-semibold text-base text-gray-700 mb-2">Informasi Pelanggan</h2>

                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200">
                            <p class="text-gray-800 font-semibold text-base" x-text="invoiceData.fullName"></p>
                            <p class="text-xs text-gray-600"
                               x-text="`${invoiceData.role} — ${invoiceData.category}`"></p>

                            <div class="mt-2 text-gray-700 text-xs space-y-0.5">
                                <p x-text="invoiceData.phoneNumber"></p>
                                <p x-text="invoiceData.address.street"></p>
                                <p x-text="`${invoiceData.address.village}, ${invoiceData.address.district}`"></p>
                                <p x-text="`${invoiceData.address.regencie}, ${invoiceData.address.province}`"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Transaksi -->
                    <div>
                        <h2 class="font-semibold text-base text-gray-700 mb-2">Detail Transaksi</h2>

                        <div class="grid grid-cols-2 gap-3">

                            <div class="bg-white border p-3 rounded-xl shadow-sm">
                                <p class="text-xs text-gray-500">Kode Transaksi</p>
                                <p class="font-semibold text-sm text-gray-800"
                                   x-text="invoiceData.transaction_code"></p>
                            </div>

                            <div class="bg-white border p-3 rounded-xl shadow-sm">
                                <p class="text-xs text-gray-500">Status</p>

                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold"
                                      :class="{
                                          'bg-yellow-100 text-yellow-700': invoiceData.status === 'pending',
                                          'bg-green-100 text-green-700': invoiceData.status === 'paid',
                                          'bg-red-100 text-red-700': invoiceData.status === 'cancelled'
                                      }"
                                      x-text="
                                        invoiceData.status === 'pending' ? 'Menunggu Pembayaran'
                                        : invoiceData.status === 'paid' ? 'Lunas'
                                        : 'Dibatalkan'
                                      ">
                                </span>
                            </div>

                            <div class="bg-white border p-3 rounded-xl shadow-sm">
                                <p class="text-xs text-gray-500">Jumlah Pembayaran</p>
                                <p class="font-bold text-base text-blue-700"
                                   x-text="formatRupiah(invoiceData.amount)"></p>
                            </div>

                            <div class="bg-white border p-3 rounded-xl shadow-sm">
                                <p class="text-xs text-gray-500">Bulan / Tahun</p>
                                <p class="font-semibold text-sm"
                                   x-text="`${invoiceData.month} / ${invoiceData.year}`"></p>
                            </div>

                            <div class="bg-white border p-3 rounded-xl shadow-sm">
                                <p class="text-xs text-gray-500">Tanggal Pembayaran</p>
                                <p class="font-semibold text-sm" x-text="invoiceData.paid_at"></p>
                            </div>

                            <div class="bg-white border p-3 rounded-xl shadow-sm">
                                <p class="text-xs text-gray-500">Jatuh Tempo</p>
                                <p class="font-semibold text-sm" x-text="invoiceData.due_date"></p>
                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 px-4 py-4 text-white sm:px-5 flex justify-between items-center">

                        <p class="text-gray-600 text-xs"
                           x-text="
                               invoiceData.status === 'paid'
                                   ? 'Terima kasih, pembayaran telah diterima.'
                                   : invoiceData.status === 'pending'
                                       ? 'Silakan selesaikan pembayaran sebelum jatuh tempo.'
                                       : 'Transaksi ini telah dibatalkan.'
                           ">
                        </p>

                       <a :href="`/transactions/${invoiceData.transaction_code}/invoice/pdf`"
                            class="btn bg-info/10 text-white border border-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-info/20">
                                Download PDF
                        </a>

                    </div>

                </div>
            </template>

        </div>
    </div>
</div>
