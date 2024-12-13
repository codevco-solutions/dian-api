<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\DocumentStateSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\ApprovalFlowSeeder;
use Database\Seeders\RecurringDocumentSeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\CustomerSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders base del sistema (en orden correcto)
        $this->call([
            RoleSeeder::class,
            MasterDataSeeder::class,  // Datos maestros primero
            CompanySeeder::class,     // Luego empresas
            BranchSeeder::class,      // Después sucursales
            UserSeeder::class,        // Finalmente usuarios
            DocumentStateSeeder::class,
            DocumentTemplateSeeder::class,
            CustomerSeeder::class,
            ApprovalFlowSeeder::class,
            RecurringDocumentSeeder::class,
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
