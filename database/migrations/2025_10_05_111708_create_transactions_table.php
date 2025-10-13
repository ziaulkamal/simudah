<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // serial unik transaksi
            $table->string('transaction_code')->unique();

            // relasi ke people
            $table->foreignId('people_id')
                ->constrained('peoples')
                ->restrictOnDelete();

            // relasi ke category
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            // relasi ke role
            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();

            // periode iuran
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year'); // contoh: 2025

            // nominal iuran, ambil dari tarif category/role saat transaksi dibuat
            $table->decimal('amount', 12, 2);

            // status transaksi
            $table->enum('status', ['pending', 'paid', 'cancelled'])
                ->default('pending');

            // tanggal pembayaran aktual
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
