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
        Schema::create('peoples', function (Blueprint $table) {
            $table->id();
            $table->string('fullName');
            $table->integer('age');
            $table->date('birthdate');
            $table->text('identityNumber'); // hasil terenkripsi, tidak bisa unique
            $table->string('identity_hash', 64)->unique();

            $table->text('familyIdentityNumber')->nullable(); // hasil terenkripsi
            $table->string('family_identity_hash', 64)->nullable(); // opsional untuk pencarian cepat
            $table->enum('gender', ['male', 'female']);
            $table->string('streetAddress');
            $table->integer('religion');
            $table->unsignedBigInteger('provinceId');
            $table->unsignedBigInteger('regencieId');
            $table->unsignedBigInteger('districtId');
            $table->unsignedBigInteger('villageId');
            $table->string('phoneNumber');
            $table->string('email')->nullable();

            // Tambahan latitude & longitude yang boleh kosong
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Foreign key ke roles
            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peoples');
    }
};
