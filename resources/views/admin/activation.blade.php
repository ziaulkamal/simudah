@extends('layouts.app')

@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8" x-data="activationPage()">
    <x-breadcrumb-header title="{{ $title }}" submenu="{{ $submenu }}" />

    <div class="card mt-3">
        <!-- Desktop Table -->
        <div class="overflow-x-auto hidden sm:block">
            <table class="table-auto w-full border border-slate-200 dark:border-navy-500 text-left">
                <thead class="bg-slate-100 dark:bg-navy-800">
                    <tr>
                        <th class="px-4 py-2 border-b border-slate-200 dark:border-navy-500">#</th>
                        <th class="px-4 py-2 border-b border-slate-200 dark:border-navy-500">Nama Lengkap</th>
                        <th class="px-4 py-2 border-b border-slate-200 dark:border-navy-500">NIK</th>
                        <th class="px-4 py-2 border-b border-slate-200 dark:border-navy-500">Telepon</th>
                        <th class="px-4 py-2 border-b border-slate-200 dark:border-navy-500">Status</th>
                        <th class="px-4 py-2 border-b border-slate-200 dark:border-navy-500 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(person, index) in peoples" :key="person.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-navy-600">
                            <td class="px-4 py-2 border-b" x-text="(from - 1) + (index + 1)"></td>
                            <td class="px-4 py-2 border-b" x-text="person.fullName.toUpperCase()"></td>
                            <td class="px-4 py-2 border-b" x-text="person.identityNumber"></td>
                            <td class="px-4 py-2 border-b" x-text="person.phoneNumber"></td>
                            <td class="px-4 py-2 border-b">
                                <span
                                    x-bind:class="person.status === 'aktif' ? 'badge bg-success text-white' : 'badge bg-warning text-white'"
                                    x-text="person.status === 'aktif' ? 'Sudah Aktif' : 'Belum Aktif'">
                                </span>
                            </td>
                            <td class="px-4 py-2 border-b text-center">
                                <div class="flex justify-center space-x-2">
                                    <button
                                        @click="showKtp(person.documents)"
                                        class="btn bg-info text-white hover:bg-info-focus hover:shadow-lg">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <template x-if="person.status === 'belum aktif'">
                                        <form :action="`/activation/activate/${person.id}`" method="POST" @submit.prevent="activateUser(person)">
                                            @csrf
                                            <button class="btn bg-success text-white hover:bg-success-focus hover:shadow-lg">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    </template>

                                    <template x-if="person.status === 'aktif'">
                                        <button class="btn bg-slate-400 text-white cursor-not-allowed" disabled>
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Pagination Container --}}
            <div
                class="text-xs-plus flex justify-center sm:justify-end text-center sm:text-right text-slate-600 dark:text-navy-100 sm:pr-[calc(var(--margin-x)*-1)]"
                x-html="paginationHtml">
            </div>
        </div>

        <!-- Mobile Accordion -->
        <div class="sm:hidden space-y-2">
            <template x-for="person in peoples" :key="person.id">
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <button @click="person.open = !person.open"
                        class="w-full flex justify-between items-center px-4 py-2 bg-slate-100 dark:bg-navy-800">
                        <span x-text="person.fullName.toUpperCase()"></span>
                        <i :class="person.open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
                    </button>

                    <div x-show="person.open" x-transition class="px-4 py-2 bg-white dark:bg-navy-700 space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">NIK:</span>
                            <span x-text="person.identityNumber"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Telepon:</span>
                            <span x-text="person.phoneNumber"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Status:</span>
                            <span :class="person.status === 'aktif' ? 'badge bg-success text-white' : 'badge bg-warning text-white'"
                                  x-text="person.status === 'aktif' ? 'Sudah Aktif' : 'Belum Aktif'"></span>
                        </div>
                        <div class="flex space-x-2 justify-end pt-2">
                            <button @click="showKtp(person.documents)" class="btn bg-info text-white hover:bg-info-focus hover:shadow-lg">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <template x-if="person.status === 'belum aktif'">
                                <form :action="`/activation/activate/${person.id}`" method="POST" @submit.prevent="activateUser(person)">
                                    @csrf
                                    <button class="btn bg-success text-white hover:bg-success-focus hover:shadow-lg">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            </template>
                            <template x-if="person.status === 'aktif'">
                                <button class="btn bg-slate-400 text-white cursor-not-allowed" disabled>
                                    <i class="fa-solid fa-lock"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal Dokumen KTP -->
    <template x-if="ktpModal.open">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-navy-700 rounded-lg shadow-lg overflow-auto">
                <div class="flex justify-between items-center bg-slate-200 dark:bg-navy-800 px-4 py-3 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Dokumen KTP</h3>
                    <button @click="closeKtpModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-navy-100">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-4 grid grid-cols-1 gap-4 justify-items-center">
                    <template x-for="doc in ktpModal.documents" :key="doc.id">
                        <div class="w-full flex justify-center">
                            <img :src="doc.file_url" alt="KTP" class="max-w-full max-h-[70vh] object-contain rounded shadow-md">
                        </div>
                    </template>
                    <template x-if="!ktpModal.documents || ktpModal.documents.length === 0">
                        <p class="text-gray-400 text-sm text-center py-4 col-span-full">Tidak ada dokumen KTP</p>
                    </template>
                </div>
            </div>
        </div>
    </template>
</main>
<x-modal-confirm />
<script>
function activationPage() {
  return {
    peoples: [],
    paginationHtml: '',
    from: 1,

    init() {
      this.loadData('/api/admin/activation/data');
    },

    loadData(url) {
      fetch(url, {
                headers: {
                    'Accept': 'application/json',
                },
            })
        .then(res => res.json())
        .then(data => {
          this.peoples = data.data;
          this.paginationHtml = data.pagination;
          this.from = Number(data.from) || 1;
          this.bindPaginationLinks();
        })
        .catch(err => console.error('Error load data:', err));
    },

    bindPaginationLinks() {
      this.$nextTick(() => {
        document.querySelectorAll('.pagination a').forEach(link => {
          link.addEventListener('click', e => {
            e.preventDefault();
            this.loadData(link.href);
          });
        });
      });
    },

    showKtp(documents) {
      this.ktpModal.documents = documents.filter(d => d.type.toLowerCase().includes('ktp'));
      this.ktpModal.open = true;
    },

    closeKtpModal() {
      this.ktpModal.open = false;
      this.ktpModal.documents = [];
    },

    activateUser(person) {
  window.dispatchEvent(new CustomEvent('show-confirm', {
    detail: {
      title: 'Aktivasi Pengguna',
      message: `Apakah anda ingin lanjutkan aktivasi pengguna ${person.fullName} dengan Nomor Induk Kependudukan ${person.identityNumber} ?`,
      yes: () => {
        fetch(`/activation/activate/${person.id}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        }).then(() => {
          person.status = 'aktif';
          window.location.href = `/add-pelanggan/${person.id}`;
        });
      },
      no: () => {
        console.log('Aktivasi dibatalkan');
      }
    }
  }));
},

    ktpModal: {
      open: false,
      documents: [],
    },
  };
}

</script>
@endsection
