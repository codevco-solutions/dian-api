<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\MasterTablesSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First create roles
        $this->call([
            RoleSeeder::class,
            MasterTablesSeeder::class,
        ]);

        // Then create the test user with super-admin role
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'role_id' => 1, // super-admin role
        ]);
    }
}
