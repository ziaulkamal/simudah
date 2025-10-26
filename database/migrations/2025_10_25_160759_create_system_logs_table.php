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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();

            // Pastikan tipe cocok dengan tabel people dan roles
            $table->foreignId('people_id')
                ->nullable()
                ->constrained('peoples')
                ->nullOnDelete();

            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles')
                ->nullOnDelete();

            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->default('info'); // info, success, warning, error
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
