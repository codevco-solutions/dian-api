<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\CitiesTableSeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\IdentificationTypeSeeder;
use Database\Seeders\OrganizationTypeSeeder;
use Database\Seeders\TaxRegimeSeeder;
use Database\Seeders\TaxResponsibilitySeeder;
use Database\Seeders\OperationTypeSeeder;
use Database\Seeders\PaymentAndEventSeeder;

class MasterTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Ubicaciones
            CountriesTableSeeder::class,
            CitiesTableSeeder::class,
            
            // Tablas maestras generales
            CurrencySeeder::class,
            IdentificationTypeSeeder::class,
            OrganizationTypeSeeder::class,
            TaxRegimeSeeder::class,
            TaxResponsibilitySeeder::class,
            OperationTypeSeeder::class,
            
            // Tablas maestras de pagos y eventos
            PaymentAndEventSeeder::class,
        ]);
    }
}
