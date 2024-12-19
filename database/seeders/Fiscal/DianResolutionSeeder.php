<?php

namespace Database\Seeders\Fiscal;

use Illuminate\Database\Seeder;
use App\Models\Document\DocResolution;
use App\Models\Company\Company;
use App\Models\Branch\Branch;
use Carbon\Carbon;

class DianResolutionSeeder extends Seeder
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

        // Resolución para Facturas de Venta
        DocResolution::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'type' => 'factura_venta',
            'resolution_number' => '18764000001',
            'prefix' => 'FV',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYears(2),
            'start_number' => 1,
            'end_number' => 99999,
            'current_number' => 0,
            'is_active' => true,
            'technical_key' => '693ff6f2-f20d-4c51-9af3-6b9f2a2ce055'
        ]);

        // Resolución para Notas Crédito
        DocResolution::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'type' => 'nota_credito',
            'resolution_number' => '18764000002',
            'prefix' => 'NC',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYears(2),
            'start_number' => 1,
            'end_number' => 99999,
            'current_number' => 0,
            'is_active' => true,
            'technical_key' => '794ff6f2-f20d-4c51-9af3-6b9f2a2ce056'
        ]);

        // Resolución para Notas Débito
        DocResolution::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'type' => 'nota_debito',
            'resolution_number' => '18764000003',
            'prefix' => 'ND',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYears(2),
            'start_number' => 1,
            'end_number' => 99999,
            'current_number' => 0,
            'is_active' => true,
            'technical_key' => '895ff6f2-f20d-4c51-9af3-6b9f2a2ce057'
        ]);
    }
}
