<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people_locations', function (Blueprint $table) {
            $table->id();

            // 🔹 sesuaikan nama tabel referensi ke "peoples"
            $table->foreignId('people_id')->constrained('peoples')->onDelete('cascade');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('address')->nullable();

            $table->string('province_name')->nullable();
            $table->string('regency_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('village_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people_locations');
    }
};
