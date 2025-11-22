<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeopleRequest;
use App\Models\Category;
use App\Models\People;

use App\Models\PeopleCategoryHistory;
use App\Models\Role;
use App\Models\TemporaryPeople;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeopleController extends Controller
{

    /**
     * 🔹 Tetapkan kategori untuk pelanggan dan buat transaksi iuran
     */
    public function assignCategory(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $people = People::with([
            'role',
            'categoryHistories.category',
            'transactions' => fn($q) => $q->latest('id')->limit(15)
        ])->find($id);

        if (!$people) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan'], 404);
        }

        // 🔹 0. Cek apakah pelanggan sudah nonaktif (role.level == 0)
        if ($people->role && $people->role->level == 0) {
            $lastChange = $people->categoryHistories()->latest('created_at')->first();
            $lastChangeDate = $lastChange
                ? $lastChange->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i')
                : '-';
            return response()->json([
                'success' => false,
                'message' => "Pelanggan ini sudah dinonaktifkan dan tidak dapat diubah kategorinya. Telah dinonaktifkan pada tanggal: {$lastChangeDate}.",
            ], 403);
        }

        // 🔍 1. Cegah perubahan jika kategori sama
        if ($people->category_id == $request->category_id) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori yang dipilih sama dengan kategori saat ini.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $category = Category::findOrFail($request->category_id);

            // 🔹 Update kategori pelanggan
            $people->category_id = $category->id;
            $people->save();

            // 🔹 Simpan riwayat perubahan kategori
            PeopleCategoryHistory::create([
                'people_id' => $people->id,
                'category_id' => $category->id,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ]);

            // 🔹 Ambil transaksi pending terakhir
            $lastPending = Transaction::where('people_id', $people->id)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            $dueDate = now('Asia/Jakarta')->addMonth()->setDay(10); // default
            if ($lastPending) {
                // Cancel transaksi sebelumnya
                $lastPending->status = 'cancelled';
                $lastPending->save();

                // Ambil due_date sebelumnya
                $dueDate = $lastPending->due_date;
            }

            // 🔹 Buat transaksi baru
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(uniqid()),
                'people_id' => $people->id,
                'category_id' => $category->id,
                'role_id' => $people->role_id ?? 1,
                'month' => $dueDate->month,
                'year' => $dueDate->year,
                'amount' => $category->price,
                'status' => 'pending',
                'paid_at' => null,
                'due_date' => $dueDate,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ]);

            DB::commit();

            // 🔹 Reload data people & relasi setelah perubahan
            $people->load([
                'category',
                'categoryHistories.category',
                'transactions' => fn($q) => $q->latest('id')->limit(15)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui dan transaksi baru dibuat.',
                'data' => [
                    'people' => $people,
                    'transaction' => [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'month' => $transaction->month,
                        'year' => $transaction->year,
                        'due_date' => $dueDate->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                        'status' => $transaction->status,
                    ],
                    'category_history' => $people->categoryHistories->map(fn($h) => [
                        'id' => $h->id,
                        'category' => [
                            'id' => $h->category?->id,
                            'name' => $h->category?->name,
                            'price' => $h->category?->price,
                        ],
                        'changed_at' => $h->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                    ]),
                    'transactions' => $people->transactions,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }




    public function categoryChangeCount($id)
    {
        $people = People::with('categoryHistories')->find($id);
        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Pelanggan tidak ditemukan.', null, 'warning', 404);
        }

        $count = $people->categoryHistories->count();

        return $this->respond(true, 'Data Ditemukan', 'Jumlah perubahan kategori berhasil dihitung.', [
            'people_id' => $people->id,
            'fullName' => $people->fullName,
            'current_category_id' => $people->category_id,
            'change_count' => $count,
            'category_history' => $people->categoryHistories->map(function ($history) {
                return [
                    'category_id' => $history->category_id,
                    'changed_at' => $history->created_at->toDateTimeString(),
                ];
            }),
        ]);
    }


    /**
     * 🔹 Helper untuk format respons seragam
     */
    private function respond($success, $title, $message, $data = null, $type = null, $code = 200)
    {
        return response()->json([
            'success' => $success,
            'type'    => $type ?? ($success ? 'success' : 'error'),
            'title'   => $title,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * Tampilkan data berdasarkan identity_hash
     */
    public function showByHash($identity_hash)
    {
        $people = People::where('identity_hash', $identity_hash)->first();

        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Pelanggan tidak ditemukan dalam sistem.', null, 'warning', 404);
        }

        return $this->respond(true, 'Data Ditemukan', 'Data pelanggan berhasil diambil.', $people);
    }

    /**
     * Simpan data baru
     */
    public function store(PeopleRequest $request)
    {
        // dd($request->identityNumber);
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Hitung umur
            $data['age'] = (new \DateTime())->diff(new \DateTime($data['birthdate']))->y;

            // Set role default jika kosong
            if (empty($data['role_id'])) {
                $defaultRole = Role::where('status', 'active')
                    ->where('level', 3)
                    ->first()
                    ?? Role::where('status', 'active')->orderByDesc('level')->first();
                $data['role_id'] = $defaultRole->id ?? null;
            }

            // Generate hash untuk identityNumber
            $identityHash = TemporaryPeople::generateHmac($request->identityNumber);

            // Cari apakah data ada di temporary
            $temporary = TemporaryPeople::where('identity_hash', $identityHash)->first();

            // Simpan data People
            $people = People::create($data);

            // Jika ditemukan di TemporaryPeople, hapus semua yang punya identity_hash sama
            if ($temporary) {
                TemporaryPeople::where('identity_hash', $identityHash)->delete();
            }

            DB::commit();

            return $this->respond(
                true,
                'Berhasil Disimpan',
                'Data pelanggan berhasil disimpan.',
                $people,
                'success',
                201
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani Duplicate Entry (kode 23000)
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Data pelanggan dengan NIK tersebut sudah terdaftar sebelumnya.',
                ], 409); // 409 Conflict
            }

            // Tangani error lain
            return response()->json([
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan tak terduga.',
            ], 500);
        }
    }

    /**
     * Update data pelanggan
     */
    public function update(PeopleRequest $request, $id)
    {
        $people = People::find($id);
        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Data pelanggan tidak ditemukan.', null, 'warning', 404);
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (isset($data['birthdate'])) {
                $data['age'] = (new \DateTime())->diff(new \DateTime($data['birthdate']))->y;
            }

            $people->update($data);
            $people->load(['province', 'regencie', 'district', 'village']);

            DB::commit();
            return $this->respond(true, 'Berhasil Diperbarui', 'Data pelanggan berhasil diperbarui.', $people);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(false, 'Gagal Diperbarui', $e->getMessage(), null, 'error', 500);
        }
    }

    /**
     * Simpan / update lokasi pelanggan
     */
    public function updateLocation(Request $request, $people_id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
            'district' => 'nullable|string',
            'province' => 'nullable|string',
            'regency' => 'nullable|string',
            'village' => 'nullable|string',
        ]);

        $people = People::find($people_id);
        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Pelanggan tidak ditemukan.', null, 'warning', 404);
        }

        try {
            $location = $people->location()->updateOrCreate(
                ['people_id' => $people->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address,
                    'district_name' => $request->district,
                    'province_name' => $request->province,
                    'regency_name' => $request->regency,
                    'village_name' => $request->village,
                ]
            );

            return $this->respond(true, 'Lokasi Disimpan', 'Lokasi pelanggan berhasil diperbarui.', $location, 'success');
        } catch (Exception $e) {
            return $this->respond(false, 'Gagal Menyimpan Lokasi', $e->getMessage(), null, 'error', 500);
        }
    }

    /**
     * Hapus data pelanggan
     */
    public function destroy($id)
    {
        $people = People::find($id);
        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Pelanggan tidak ditemukan.', null, 'warning', 404);
        }

        try {
            $people->delete();
            return $this->respond(true, 'Data Dihapus', 'Data pelanggan berhasil dihapus.', null, 'success');
        } catch (Exception $e) {
            return $this->respond(false, 'Gagal Menghapus', $e->getMessage(), null, 'error', 500);
        }
    }

    /**
     * Test NIK berdasarkan identity_hash
     */
    public function localSignature($nik)
    {
        $identity_hash = People::generateHmac($nik);
        $people = People::where('identity_hash', $identity_hash)->first();

        if (!$people) {
            return $this->respond(false, 'Tidak Ditemukan', 'Data dengan NIK ini tidak ditemukan.', [
                'nik' => $nik,
                'hash' => $identity_hash,
            ], 'warning');
        }

        return $this->respond(true, 'Data Ditemukan', 'Data pelanggan ditemukan.', [
            'data' => $people,
            'hash' => $identity_hash,
        ]);
    }

    public function closeAccount($id)
    {
        $people = People::with('transactions', 'role')->find($id);

        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Pelanggan tidak ditemukan.', null, 'warning', 404);
        }

        // 🔹 Cari role dengan level = 0 (role nonaktif)
        $inactiveRole = Role::where('level', 0)->first();

        if (!$inactiveRole) {
            return $this->respond(false, 'Role Tidak Ditemukan', 'Role dengan level 0 tidak ditemukan di sistem.', null, 'error', 500);
        }

        // 🔍 Jika akun sudah nonaktif sebelumnya
        if ($people->role_id === $inactiveRole->id) {
            return $this->respond(false, 'Akun Sudah Nonaktif', 'Akun pelanggan ini sudah ditutup sebelumnya.', null, 'info', 400);
        }

        DB::beginTransaction();
        try {
            // 🔹 1. Batalkan transaksi terakhir yang pending
            $lastTransaction = Transaction::where('people_id', $people->id)
                ->where('status', 'pending')
                ->latest('created_at')
                ->first();

            if ($lastTransaction) {
                $lastTransaction->update(['status' => 'cancelled']);
            }

            // 🔹 2. Update role ke role nonaktif
            $people->update(['role_id' => $inactiveRole->id]);

            DB::commit();

            return $this->respond(true, 'Akun Ditutup', 'Akun pelanggan berhasil dinonaktifkan dan transaksi terakhir dibatalkan.', [
                'people_id' => $people->id,
                'fullName' => $people->fullName,
                'status_role' => $inactiveRole->id,
                'transactions_cancelled' => (bool) $lastTransaction,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->respond(false, 'Gagal Menutup Akun', $e->getMessage(), null, 'error', 500);
        }
    }

    /**
     * 🔹 Reaktivasi akun pelanggan
     */
    public function reactivateAccount($id)
    {
        $people = People::with(['role', 'category', 'transactions'])->find($id);

        if (!$people) {
            return $this->respond(false, 'Data Tidak Ditemukan', 'Pelanggan tidak ditemukan.', null, 'warning', 404);
        }

        // Cari role aktif level 3
        $activeRole = Role::where('level', 3)->first();

        if (!$activeRole) {
            return $this->respond(false, 'Role Tidak Ditemukan', 'Role dengan level 3 (aktif) tidak ditemukan di sistem.', null, 'error', 500);
        }

        // Cek jika akun sudah aktif
        if ($people->role_id === $activeRole->id) {
            return $this->respond(false, 'Akun Sudah Aktif', 'Akun pelanggan ini sudah aktif.', null, 'info', 400);
        }

        DB::beginTransaction();
        try {
            // 1️⃣ Update role menjadi aktif
            $people->update(['role_id' => $activeRole->id]);

            // 2️⃣ Tentukan due_date bulan depan
            $dueDate = now('Asia/Jakarta')->addMonth()->setDay(10);

            // Jika ada transaksi pending sebelumnya, cancel
            $lastPending = Transaction::where('people_id', $people->id)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($lastPending) {
                $lastPending->update(['status' => 'cancelled']);
                $dueDate = $lastPending->due_date; // pakai due_date sebelumnya jika ada
            }

            // ❗ Jika kategori kosong, skip transaksi baru
            if (!$people->category_id || !$people->category) {

                Log::warning('Reaktivasi: transaksi baru dilewati karena category_id null', [
                    'people_id' => $people->id,
                    'category_id' => $people->category_id
                ]);

                DB::commit();

                $people->load(['role', 'category', 'transactions' => fn($q) => $q->latest('id')->limit(15)]);

                return $this->respond(
                    true,
                    'Akun Diaktifkan',
                    'Akun berhasil diaktifkan kembali tanpa pembuatan transaksi baru karena kategori kosong.',
                    ['people' => $people]
                );
            }
            // 3️⃣ Buat transaksi baru untuk bulan depan
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(uniqid()),
                'people_id' => $people->id,
                'category_id' => $people->category_id,
                'role_id' => $activeRole->id,
                'month' => $dueDate->month,
                'year' => $dueDate->year,
                'amount' => $people->category?->price ?? 0,
                'status' => 'pending',
                'paid_at' => null,
                'due_date' => $dueDate,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ]);

            DB::commit();

            // Reload relasi
            $people->load(['role', 'category', 'transactions' => fn($q) => $q->latest('id')->limit(15)]);

            return $this->respond(true, 'Akun Diaktifkan', 'Akun pelanggan berhasil diaktifkan ulang dan transaksi bulan depan dibuat.', [
                'people' => $people,
                'transaction' => [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'month' => $transaction->month,
                    'year' => $transaction->year,
                    'due_date' => $dueDate->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                    'status' => $transaction->status,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Gagal mengaktifkan ulang akun pelanggan', [
                'people_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return $this->respond(false, 'Gagal Mengaktifkan Akun', $e->getMessage(), null, 'error', 500);
        }
    }
}
