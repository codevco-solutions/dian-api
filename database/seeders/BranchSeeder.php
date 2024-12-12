<?php

namespace Database\Seeders;

use App\Models\Branch\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'company_id' => 1,
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'name' => 'Sucursal Principal Demo',
                'code' => 'DEMO001',
                'address' => 'Calle 123 # 45-67',
                'phone' => '1234567890',
                'email' => 'sucursal@empresademo.com',
                'is_main' => true,
                'is_active' => true
            ],
            [
                'company_id' => 2,
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'name' => 'Sucursal Principal Test',
                'code' => 'TEST001',
                'address' => 'Carrera 98 # 76-54',
                'phone' => '9876543210',
                'email' => 'sucursal@empresatest.com',
                'is_main' => true,
                'is_active' => true
            ]
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
