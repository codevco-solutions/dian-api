<?php

namespace Database\Seeders;

use App\Models\Role; // Add this line to import the Role model
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'super-admin',
                'description' => 'Usuario con acceso total al sistema'
            ],
            [
                'name' => 'Administrador de Compañía',
                'slug' => 'company-admin',
                'description' => 'Usuario con acceso total a su compañía'
            ],
            [
                'name' => 'Usuario',
                'slug' => 'user',
                'description' => 'Usuario con acceso limitado a su compañía'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
