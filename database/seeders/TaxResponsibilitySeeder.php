<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxResponsibilitySeeder extends Seeder
{
    public function run(): void
    {
        $responsibilities = [
            [
                'name' => 'Gran contribuyente',
                'code' => 'O-13',
                'description' => 'Gran contribuyente',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Autorretenedor',
                'code' => 'O-15',
                'description' => 'Autorretenedor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agente de retención IVA',
                'code' => 'O-23',
                'description' => 'Agente de retención IVA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Régimen simple de tributación',
                'code' => 'O-47',
                'description' => 'Régimen simple de tributación',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'No responsable de IVA',
                'code' => 'O-49',
                'description' => 'No responsable de IVA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Responsable de IVA',
                'code' => 'R-99-PN',
                'description' => 'Responsable de IVA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tax_responsibilities')->insert($responsibilities);
    }
}
