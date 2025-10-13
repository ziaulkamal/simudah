<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\CategorySeeder;
use Database\Seeders\Kemendagri;
use Database\Seeders\PeopleSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                Kemendagri::class,
                RoleSeeder::class,
                CategorySeeder::class,
                // PeopleSeeder::class,
            ]);
    }
}
