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
        Schema::create('people_documents', function (Blueprint $table) {
            $table->id();

            // relasi ke people
            $table->foreignId('people_id')
                ->constrained('peoples')
                ->cascadeOnDelete();

            // jenis dokumen (ktp, kk, foto_rumah, foto_usaha, dll)
            $table->string('document_type');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_documents');
    }
};
