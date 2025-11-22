<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Disable', 'level' => 0, 'status' => 'active'],
            ['name' => 'Administrator', 'level' => 1, 'status' => 'active'],
            ['name' => 'Petugas', 'level' => 2, 'status' => 'active'],
            ['name' => 'Pelanggan', 'level' => 3, 'status' => 'active'],
            ['name' => 'Super Admin', 'level' => 99, 'status' => 'active'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
