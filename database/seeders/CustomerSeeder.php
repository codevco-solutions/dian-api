<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        Customer::create([
            'company_id' => 1,
            'identification_type_id' => 1, // Cédula de ciudadanía
            'identification_number' => '1234567890',
            'verification_code' => '1',
            'organization_type_id' => 1, // Persona natural
            'tax_regime_id' => 1, // Régimen simple
            'name' => 'Cliente Prueba',
            'trade_name' => 'Cliente Prueba S.A.S',
            'address' => 'Calle Principal #123',
            'phone' => '3001234567',
            'mobile' => '3001234567',
            'email' => 'cliente@test.com',
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'is_active' => true
        ]);
    }
}
