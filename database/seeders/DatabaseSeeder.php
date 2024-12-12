<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\MasterTablesSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MasterTablesSeeder::class,
            CompanySeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
        ]);

        // Then create the test user with super-admin role
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@dian-api.test',
            'password' => bcrypt('password'),
            'is_active' => true,
            'role_id' => 1,
        ]);

        // Asignar el rol de super-admin
        $user->assignRole('super-admin');
    }
}
