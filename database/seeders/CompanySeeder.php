<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'identification_type_id' => 1, // Cédula de ciudadanía
                'tax_regime_id' => 1, // Régimen Simple
                'organization_type_id' => 1, // Persona Natural
                'identification_number' => '900123456',
                'verification_code' => '7',
                'name' => 'Empresa Demo SAS',
                'commercial_name' => 'Empresa Demo',
                'email' => 'info@empresademo.com',
                'phone' => '1234567890',
                'address' => 'Calle 123 # 45-67',
                'website' => 'https://empresademo.com',
                'subdomain' => 'empresademo',
                'is_active' => true
            ],
            [
                'identification_type_id' => 1,
                'tax_regime_id' => 1,
                'organization_type_id' => 1,
                'identification_number' => '900654321',
                'verification_code' => '8',
                'name' => 'Empresa Test SAS',
                'commercial_name' => 'Empresa Test',
                'email' => 'info@empresatest.com',
                'phone' => '9876543210',
                'address' => 'Carrera 98 # 76-54',
                'website' => 'https://empresatest.com',
                'subdomain' => 'empresatest',
                'is_active' => true
            ]
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
