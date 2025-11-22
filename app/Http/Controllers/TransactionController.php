<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;


class TransactionController extends Controller
{
    /**
     * Tampilkan semua transaksi milik seseorang berdasarkan ID orang.
     */


    public function index($people_id)
    {
        $people = People::with(['transactions.category'])
            ->find($people_id);

        if (!$people) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan'], 404);
        }

        $now = Carbon::now();

        $transactions = $people->transactions->map(function ($trx) use ($now) {
            if ($trx->due_date) {
                $dueDate = Carbon::parse($trx->due_date);

                // Jika due date sudah lewat bulan & tahun sekarang, sembunyikan
                if ($dueDate->year > $now->year || ($dueDate->year === $now->year && $dueDate->month > $now->month)) {
                    return null;
                }
            }

            return [
                'id' => $trx->id,
                'transaction_code' => $trx->transaction_code,
                'category' => [
                    'id' => $trx->category?->id,
                    'name' => $trx->category?->name,
                    'price' => $trx->category?->price,
                ],
                'amount' => $trx->amount,
                'month' => $trx->month,
                'year' => $trx->year,
                'status' => $trx->status,
                'paid_at' => $trx->paid_at ? Carbon::parse($trx->paid_at)->format('d/m/Y H:i') : '-',
                'due_date' => $trx->due_date ? date_time_id(Carbon::parse($trx->due_date)->format('d/m/Y H:i')) : '-',
            ];
        })->filter()->values(); // remove null

        return response()->json([
            'success' => true,
            'data' => [
                'people' => [
                    'id' => $people->id,
                    'name' => $people->name,
                    'category' => $people->category?->name,
                ],
                'transactions' => $transactions,
            ]
        ]);
    }


    /**
     * Tampilkan detail transaksi berdasarkan ID transaksi
     */
    public function show($id)
    {
        $trx = Transaction::with(['people', 'category'])->find($id);

        if (!$trx) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $trx->id,
                'transaction_code' => $trx->transaction_code,
                'people' => [
                    'id' => $trx->people?->id,
                    'name' => $trx->people?->name,
                ],
                'category' => [
                    'id' => $trx->category?->id,
                    'name' => $trx->category?->name,
                    'price' => $trx->category?->price,
                ],
                'amount' => $trx->amount,
                'month' => $trx->month,
                'year' => $trx->year,
                'status' => $trx->status,
                'paid_at' => $trx->paid_at ? date('d/m/Y H:i', strtotime($trx->paid_at)) : '-',
            ]
        ]);
    }

    public function all(Request $request)
    {
        $query = Transaction::with([
            'people.regencie',
            'people.district',
            'category',
            'role',
            'people.regencie',
            'people.district'
        ])->orderByDesc('created_at');

        // 🔍 Filter opsional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('people_id')) {
            $query->where('people_id', $request->people_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('provinceId')) {
            $query->whereHas('people', fn($p) => $p->where('provinceId', $request->provinceId));
        }
        if ($request->filled('regencieId')) {
            $query->whereHas('people', fn($p) => $p->where('regencieId', $request->regencieId));
        }
        if ($request->filled('districtId')) {
            $query->whereHas('people', fn($p) => $p->where('districtId', $request->districtId));
        }
        if ($request->filled('villageId')) {
            $query->whereHas('people', fn($p) => $p->where('villageId', $request->villageId));
        }


        // 🔍 Filter wilayah berjenjang (provinsi → kabupaten → kecamatan → desa)
        $query->whereHas('people', function ($q) use ($request) {
            if ($request->filled('provinceId')) {
                $q->where('provinceId', $request->provinceId);
            }
            if ($request->filled('regencieId')) {
                $q->where('regencieId', $request->regencieId);
            }
            if ($request->filled('districtId')) {
                $q->where('districtId', $request->districtId);
            }
            if ($request->filled('villageId')) {
                $q->where('villageId', $request->villageId);
            }
        });

        // 🔍 Pencarian teks (kode transaksi / nama kabupaten / kecamatan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhereHas('people.regencie', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('people.district', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 🔹 Pagination default 20
        $perPage = $request->get('per_page', 20);
        $transactions = $query->paginate($perPage);

        // 🔹 Format data output
        $data = $transactions->getCollection()->transform(function ($trx) {
            return [
                'id' => $trx->id,
                'transaction_code' => $trx->transaction_code,
                'people' => [
                    'id' => $trx->people?->id,
                    'district' => $trx->people?->district?->name,
                    'village' => $trx->people?->village?->name,
                ],
                'category' => [
                    'id' => $trx->category?->id,
                    'name' => $trx->category?->name,
                    'price' => $trx->category?->price,
                ],
                'role' => [
                    'id' => $trx->role?->id,
                    'name' => $trx->role?->name,
                ],
                'amount' => $trx->amount,
                'month' => $trx->month,
                'year' => $trx->year,
                'status' => $trx->status,
                'paid_at' => $trx->paid_at ? $trx->paid_at->format('d/m/Y H:i') : '-',
                'due_date' => $trx->due_date ? $trx->due_date->format('d/m/Y H:i') : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'meta' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
            'data' => $data,
        ]);
    }

    public function searchHashIdentity($hash)
    {
        $people = People::with([
            'category',              // 🔹 ambil relasi kategori
            'transactions.category', // 🔹 relasi kategori tiap transaksi
            'transactions.role',
            'transactions.role',
            'role',
        ])->where('identity_hash', $hash)->first();

        if (!$people) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan']);
        }

        $transactions = $people->transactions->map(function ($trx) {
            return [
                'id' => $trx->id,
                'transaction_code' => $trx->transaction_code,
                'category' => $trx->category?->name,
                'role' => $trx->role?->name,
                'amount' => $trx->amount,
                'month' => $trx->month,
                'year' => $trx->year,
                'status' => $trx->status,
                'paid_at' => date_time_id($trx->paid_at?->format('d/m/Y H:i'), false) ?? '-',
                'due_date' => date_time_id($trx->due_date?->format('d/m/Y H:i'), false) ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'people' => [
                'id' => $people->id,
                'name' => strtoupper($people->fullName),
                'category' => $people->category,
                'role' => [
                    'name' => $people->role?->name,
                    'level' => $people->role?->level,
                ],
            ],
            'transactions' => $transactions,
        ]);
    }
}
