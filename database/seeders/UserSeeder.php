<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user
        $admin = User::create([
            'company_id' => 1,
            'branch_id' => 1,
            'name' => 'Super Admin',
            'email' => 'admin@empresademo.com',
            'password' => Hash::make('12345678'),
            'is_active' => true
        ]);

        $admin->assignRole('super-admin');

        // Create regular user
        $user = User::create([
            'company_id' => 2,
            'branch_id' => 2,
            'name' => 'Regular User',
            'email' => 'user@empresatest.com',
            'password' => Hash::make('12345678'),
            'is_active' => true
        ]);

        $user->assignRole('user');
    }
}
