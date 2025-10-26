<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('temporary_people_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporary_people_id')->constrained('temporary_peoples')->cascadeOnDelete();
            $table->string('type')->default('ktp');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('encrypted_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_people_documents');
    }
};
