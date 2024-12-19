<?php

namespace Database\Seeders\Fiscal;

use Illuminate\Database\Seeder;
use App\Models\Fiscal\DocumentNumberingConfig;
use App\Models\Company\Company;
use App\Models\Branch\Branch;

class DocumentNumberingConfigSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first();
        if (!$company) {
            $this->command->error('No hay compañías registradas. Por favor, ejecute el seeder de compañías primero.');
            return;
        }

        $branch = Branch::where('company_id', $company->id)->first();
        if (!$branch) {
            $this->command->error('No hay sucursales registradas. Por favor, ejecute el seeder de sucursales primero.');
            return;
        }

        // Configuración para Órdenes de Compra
        DocumentNumberingConfig::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'document_type' => 'orden_compra',
            'prefix' => 'OC',
            'padding' => 6,
            'last_number' => 0,
            'format' => '{PREFIX}-{NUMBER}',
            'reset_yearly' => true,
            'is_active' => true
        ]);

        // Configuración para Cotizaciones
        DocumentNumberingConfig::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'document_type' => 'cotizacion',
            'prefix' => 'COT',
            'padding' => 6,
            'last_number' => 0,
            'format' => '{PREFIX}-{NUMBER}',
            'reset_yearly' => true,
            'is_active' => true
        ]);

        // Configuración para Recibos
        DocumentNumberingConfig::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'document_type' => 'recibo',
            'prefix' => 'REC',
            'padding' => 6,
            'last_number' => 0,
            'format' => '{PREFIX}-{NUMBER}',
            'reset_yearly' => true,
            'is_active' => true
        ]);
    }
}
