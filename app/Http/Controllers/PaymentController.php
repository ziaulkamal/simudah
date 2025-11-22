<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Bayar transaksi berdasarkan ID
     */
    public function payTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah dibayar sebelumnya.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Tandai transaksi saat ini sebagai LUNAS
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);


            // Hitung tanggal jatuh tempo berikutnya (10 bulan depan)
            $nextDueDate = \Carbon\Carbon::create($transaction->year, $transaction->month, 10)
                ->addMonth();

            // Buat transaksi baru untuk bulan berikutnya
            Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(uniqid()),
                'people_id' => $transaction->people_id,
                'category_id' => $transaction->category_id,
                'role_id' => $transaction->role_id,
                'month' => $nextDueDate->month,
                'year' => $nextDueDate->year,
                'amount' => $transaction->amount,
                'status' => 'pending',
                'due_date' => $nextDueDate->format('Y-m-d'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil, dan transaksi bulan berikutnya telah dibuat.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
