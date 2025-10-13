<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Kemendagri\Districts;
use App\Models\Kemendagri\Provinces;
use App\Models\Kemendagri\Regencies;
use App\Models\Kemendagri\Villages;
use App\Models\People;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        $genders = ['male', 'female'];
        $religions = [1, 2, 3, 4, 5, 6];
        $streets = [
            'Jl. Merdeka No. 10',
            'Jl. Sudirman No. 22',
            'Jl. Gajah Mada No. 5',
            'Jl. Diponegoro No. 17',
            'Jl. Gatot Subroto No. 3',
        ];

        $roles = Role::pluck('id')->toArray();
        $categories = Category::pluck('id')->toArray();

        // 🎯 Ambil wilayah Aceh Barat Daya (1112)
        $province = Provinces::where('id', 11)->first(); // Aceh
        $regency = Regencies::where('id', 1112)->first(); // Aceh Barat Daya

        // Semua kecamatan di kabupaten ini
        $districts = Districts::where('regency_id', $regency->id)->get();

        for ($i = 1; $i <= 500; $i++) {
            $gender = $genders[array_rand($genders)];
            $age = rand(17, 65);
            $identityNumber = str_pad(rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT);
            $familyIdentityNumber = str_pad(rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT);

            // Pilih kecamatan & desa secara acak dari DB
            $district = $districts->random();
            $villages = Villages::where('district_id', $district->id)->pluck('id');
            $villageId = $villages->isNotEmpty() ? $villages->random() : null;

            People::create([
                'fullName' => fake()->name($gender === 'male' ? 'male' : 'female'),
                'age' => $age,
                'birthdate' => now()->subYears($age)->format('Y-m-d'),
                'identityNumber' => $identityNumber,
                'familyIdentityNumber' => $familyIdentityNumber,
                'gender' => $gender,
                'streetAddress' => $streets[array_rand($streets)],
                'religion' => $religions[array_rand($religions)],

                // ✅ Data wilayah real dari DB
                'provinceId' => $province->id,
                'regencieId' => $regency->id,
                'districtId' => $district->id,
                'villageId' => $villageId,

                'phoneNumber' => '08' . rand(1000000000, 9999999999),
                'email' => Str::lower(Str::random(6)) . '@example.com',
                'latitude' => fake()->latitude(-8, 1),
                'longitude' => fake()->longitude(95, 141),
                'role_id' => $roles ? $roles[array_rand($roles)] : null,
                'category_id' => $categories ? $categories[array_rand($categories)] : null,
            ]);
        }
    }
}
