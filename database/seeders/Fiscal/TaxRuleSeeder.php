<?php

namespace Database\Seeders\Fiscal;

use Illuminate\Database\Seeder;
use App\Models\Document\TaxRule;
use App\Models\Document\RegionalTaxRule;
use App\Models\Company\Company;
use App\Models\Location\Country;
use App\Models\Location\State;

class TaxRuleSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first();
        if (!$company) {
            $this->command->error('No hay compañías registradas. Por favor, ejecute el seeder de compañías primero.');
            return;
        }

        // IVA General 19%
        $ivaGeneral = TaxRule::create([
            'company_id' => $company->id,
            'name' => 'IVA General',
            'type' => 'iva',
            'rate' => 19.00,
            'min_amount' => 0,
            'is_active' => true
        ]);

        // IVA Reducido 5%
        TaxRule::create([
            'company_id' => $company->id,
            'name' => 'IVA Reducido',
            'type' => 'iva',
            'rate' => 5.00,
            'min_amount' => 0,
            'is_active' => true
        ]);

        // Retención en la Fuente 2.5%
        $retencionFuente = TaxRule::create([
            'company_id' => $company->id,
            'name' => 'Retención en la Fuente - Servicios',
            'type' => 'retencion_fuente',
            'rate' => 2.50,
            'min_amount' => 1088000, // Aproximadamente 4 UVT 2024
            'is_active' => true
        ]);

        // Retención IVA 15%
        TaxRule::create([
            'company_id' => $company->id,
            'name' => 'Retención IVA',
            'type' => 'retencion_iva',
            'rate' => 15.00,
            'min_amount' => 1088000, // Aproximadamente 4 UVT 2024
            'is_active' => true
        ]);

        // Retención ICA
        $retencionIca = TaxRule::create([
            'company_id' => $company->id,
            'name' => 'Retención ICA',
            'type' => 'retencion_ica',
            'rate' => 0.69,
            'min_amount' => 0,
            'is_active' => true
        ]);

        // Crear algunas reglas regionales
        $colombia = Country::where('code_2', 'CO')->first();
        if ($colombia) {
            // Bogotá tiene una tarifa ICA diferente
            $bogota = State::where('country_id', $colombia->id)
                         ->where('name', 'like', '%Bogotá%')
                         ->first();
            
            if ($bogota) {
                RegionalTaxRule::create([
                    'tax_rule_id' => $retencionIca->id,
                    'country_id' => $colombia->id,
                    'state_id' => $bogota->id,
                    'rate' => 1.104,
                    'is_active' => true
                ]);
            }

            // San Andrés está exento de IVA
            $sanAndres = State::where('country_id', $colombia->id)
                            ->where('name', 'like', '%San Andrés%')
                            ->first();
            
            if ($sanAndres) {
                RegionalTaxRule::create([
                    'tax_rule_id' => $ivaGeneral->id,
                    'country_id' => $colombia->id,
                    'state_id' => $sanAndres->id,
                    'rate' => 0,
                    'is_active' => true
                ]);
            }
        }
    }
}
